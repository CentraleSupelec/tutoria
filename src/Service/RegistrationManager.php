<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Lti\Registration;
use App\Exception\NonUniqueRegistrationException;
use App\Repository\Lti\RegistrationRepository;
use App\Utils\LtiToolUtils;
use OAT\Library\Lti1p3Core\Registration\RegistrationInterface;
use OAT\Library\Lti1p3Core\Registration\RegistrationRepositoryInterface;
use Psr\Log\LoggerInterface;

class RegistrationManager implements RegistrationRepositoryInterface
{
    public function __construct(private readonly RegistrationRepository $registrationRepository, private readonly LtiToolUtils $ltiToolUtils, private readonly LoggerInterface $logger)
    {
    }

    private function addToolCongigurationToRegistration(?Registration $registration): ?Registration
    {
        if ($registration instanceof Registration) {
            $registration->setTool($this->ltiToolUtils->getTutorIATool())
                ->setToolKeyChain($this->ltiToolUtils->getTutorIAkeyChain())
            ;
        }

        return $registration;
    }

    private function addToolCongigurationToRegistrations(array $registrations): array
    {
        $registrationsWithToolConfiguration = [];
        foreach ($registrations as $registration) {
            $registrationsWithToolConfiguration[] = $this->addToolCongigurationToRegistration($registration);
        }

        return $registrationsWithToolConfiguration;
    }

    public function find(string $identifier): ?RegistrationInterface
    {
        return $this->addToolCongigurationToRegistration($this->registrationRepository->findOneBy(['id' => $identifier]));
    }

    public function findAll(): array
    {
        return $this->addToolCongigurationToRegistrations($this->registrationRepository->findAll());
    }

    public function findByClientId(string $clientId): ?RegistrationInterface
    {
        return $this->addToolCongigurationToRegistration($this->registrationRepository->findOneBy(['clientId' => $clientId]));
    }

    public function findByPlatformIssuer(string $issuer, string $clientId = null): ?RegistrationInterface
    {
        try {
            return $this->addToolCongigurationToRegistration($this->registrationRepository->findByPlatformIssuer($issuer, $clientId));
        } catch (NonUniqueRegistrationException $exception) {
            $this->logger->error($exception);

            return null;
        }
    }

    public function findByToolIssuer(string $issuer, string $clientId = null): ?RegistrationInterface
    {
        return null;
    }
}
