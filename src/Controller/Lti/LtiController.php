<?php

declare(strict_types=1);

namespace App\Controller\Lti;

use App\Entity\Student;
use App\Repository\StudentRepository;
use Doctrine\ORM\EntityManagerInterface;
use OAT\Library\Lti1p3Core\Exception\LtiExceptionInterface;
use OAT\Library\Lti1p3Core\Message\Launch\Validator\Tool\ToolLaunchValidator;
use OAT\Library\Lti1p3Core\Security\Oidc\OidcInitiator;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\PsrHttpMessage\HttpMessageFactoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/lti')]
class LtiController extends AbstractController
{
    #[Route('/oidc/initiation', name: 'oidc_initiator')]
    public function oidcInitiation(Request $request, OidcInitiator $oidcInitiator, HttpMessageFactoryInterface $httpMessageFactory, LoggerInterface $logger): RedirectResponse|JsonResponse
    {
        try {
            $ltiMessage = $oidcInitiator->initiate($this->toPsr($request, $httpMessageFactory));

            $logger->info('OidcInitiationAction: initiation success');

            return new RedirectResponse($ltiMessage->toUrl());
        } catch (LtiExceptionInterface $exception) {
            $logger->error(sprintf('OidcInitiationAction: %s', $exception->getMessage()));

            return $this->json([
                'status' => 'KO',
                'errors' => [$exception->getMessage()],
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/launch', name: 'lti_launch')]
    public function ltiLaunch(
        Request $request,
        ToolLaunchValidator $toolLaunchValidator,
        StudentRepository $studentRepository,
        EntityManagerInterface $entityManager,
        Security $security,
        HttpMessageFactoryInterface $httpMessageFactory,
        ValidatorInterface $validator
    ): RedirectResponse|JsonResponse {
        $launchValidationResult = $toolLaunchValidator->validatePlatformOriginatingLaunch($this->toPsr($request, $httpMessageFactory));

        if (!$launchValidationResult->hasError()) {
            $email = $launchValidationResult->getPayload()->getClaim('email');
            $student = $studentRepository->findOneBy(['email' => $email]);
            $shouldFlush = false;
            if (!$student instanceof Student) {
                $student = (new Student())
                    ->setEmail($email)
                    ->setFirstName($launchValidationResult->getPayload()->getClaim('given_name'))
                    ->setLastName($launchValidationResult->getPayload()->getClaim('family_name'))
                    ->setEnabled(true)
                    ->setRoles([Student::ROLE_TUTEE])
                ;
                $shouldFlush = true;
            } elseif (!in_array(Student::ROLE_TUTEE, $student->getRoles())) {
                $newRoles = $student->getRoles();
                $newRoles[] = Student::ROLE_TUTEE;
                $student->setRoles($newRoles);
                $shouldFlush = true;
            }

            if ($shouldFlush) {
                $constraintViolationList = $validator->validate($student);

                if (count($constraintViolationList) > 0) {
                    return $this->json([
                        'status' => 'KO',
                        'errors' => $constraintViolationList,
                    ], Response::HTTP_BAD_REQUEST);
                } else {
                    $entityManager->persist($student);
                    $entityManager->flush();
                }
            }

            $security->login($student);

            return new RedirectResponse('/tutee');
        } else {
            return $this->json([
                'status' => 'KO',
                'errors' => [$launchValidationResult->getError()],
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    private function toPsr(Request $request, HttpMessageFactoryInterface $httpMessageFactory): ServerRequestInterface
    {
        // As we cannot decorate the Symfony Request object, we convert it into
        // a PSR Request so we can override the PSR HTTP Message factory if
        // needed.
        // See the reasons at https://github.com/ecphp/cas-lib/issues/5
        return $httpMessageFactory->createRequest($request);
    }
}
