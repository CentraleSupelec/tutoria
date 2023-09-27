<?php

namespace App\Controller\Tutor;

use App\Entity\Student;
use App\Entity\Tutoring;
use App\Entity\TutoringSession;
use App\Form\BatchTutoringSessionCreationType;
use App\Form\TutoringSessionType;
use App\Form\TutoringType;
use App\Model\BatchTutoringSessionCreationModel;
use App\Repository\TutoringRepository;
use App\Security\Voters\TutoringSessionVoter;
use App\Service\BatchTutoringSessionCreationService;
use App\Utils\ErrorUtils;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/tutor')]
class TutorController extends AbstractController
{
    #[Route('/', name: 'tutor_home')]
    public function index(
        TutoringRepository $tutoringRepository
    ): Response {
        /** @var Student $user */
        $user = $this->getUser();
        $tutorings = $tutoringRepository->findByTutor($user);

        return $this->render('tutor/dashboard.html.twig', [
            'tutorings' => $tutorings,
        ]);
    }

    #[Route('/batch-create-sessions', name: 'batch_create_sessions', options: ['expose' => true], methods: ['POST'])]
    public function batchCreateSessions(
        Request $request,
        BatchTutoringSessionCreationService $batchTutoringSessionCreationService,
        EntityManagerInterface $entityManager,
        ErrorUtils $errorUtils
    ): Response {
        $batchTutoringSessionCreationModel = new BatchTutoringSessionCreationModel();
        $form = $this->createForm(BatchTutoringSessionCreationType::class, $batchTutoringSessionCreationModel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($batchTutoringSessionCreationModel->getSaveDefaultValues()) {
                $tutoring = $batchTutoringSessionCreationModel->getTutoring();
                $tutoring
                    ->setDefaultWeekDays($batchTutoringSessionCreationModel->getWeekDays())
                    ->setDefaultStartTime($batchTutoringSessionCreationModel->getStartTime())
                    ->setDefaultEndTime($batchTutoringSessionCreationModel->getEndTime())
                    ->setDefaultBuilding($batchTutoringSessionCreationModel->getBuilding())
                    ->setDefaultRoom($batchTutoringSessionCreationModel->getRoom())
                ;

                $entityManager->persist($tutoring);
                $entityManager->flush();
            }
            $batchTutoringSessionCreationService->batchCreateSessions($batchTutoringSessionCreationModel);

            return $this->json([
                'status' => 'OK',
            ], Response::HTTP_OK);
        } else {
            $errors = $errorUtils->parseFormErrors($form->getErrors(true));

            return $this->json([
                'status' => 'KO',
                'errors' => $errors,
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    #[Route('/tutoring/{id}/update', name: 'update_tutoring', options: ['expose' => true], methods: ['POST'])]
    public function updateTutoring(
        Tutoring $tutoring,
        Request $request,
        EntityManagerInterface $entityManager,
        TutoringRepository $tutoringRepository,
        ErrorUtils $errorUtils
    ): Response {
        $form = $this->createForm(TutoringType::class, $tutoring);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($tutoring);
            $entityManager->flush();

            return $this->json([
                'status' => 'OK',
            ], Response::HTTP_OK);
        } else {
            $errors = $errorUtils->parseFormErrors($form->getErrors(true));

            return $this->json([
                'status' => 'KO',
                'errors' => $errors,
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    #[Route('/tutoring-session/new', name: 'create_tutoring_session', options: ['expose' => true], methods: ['POST'])]
    public function createTutoringSession(
        Request $request,
        EntityManagerInterface $entityManager,
        TutoringRepository $tutoringRepository,
        ErrorUtils $errorUtils
    ): Response {
        /** @var Student $user */
        $user = $this->getUser();

        $tutoringSession = new TutoringSession();
        $form = $this->createForm(TutoringSessionType::class, $tutoringSession);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tutoringSession
                ->setCreatedBy($user)
                ->addTutor($user)
            ;

            $entityManager->persist($tutoringSession);
            $entityManager->flush();

            return $this->json([
                'status' => 'OK',
            ], Response::HTTP_OK);
        } else {
            $errors = $errorUtils->parseFormErrors($form->getErrors(true));

            return $this->json([
                'status' => 'KO',
                'errors' => $errors,
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    #[Route('/tutoring-session/{id}/update', name: 'update_tutoring_session', options: ['expose' => true], methods: ['POST'])]
    #[IsGranted(TutoringSessionVoter::TUTOR_EDIT_TUTORING_SESSION, subject: 'tutoringSession')]
    public function updateTutoringSession(
        TutoringSession $tutoringSession,
        Request $request,
        EntityManagerInterface $entityManager,
        ErrorUtils $errorUtils
    ): Response {
        $form = $this->createForm(TutoringSessionType::class, $tutoringSession);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($tutoringSession);
            $entityManager->flush();

            return $this->json([
                'status' => 'OK',
            ], Response::HTTP_OK);
        } else {
            $errors = $errorUtils->parseFormErrors($form->getErrors(true));

            return $this->json([
                'status' => 'KO',
                'errors' => $errors,
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    #[Route('/tutoring-session/{id}/delete', name: 'delete_tutoring_session', options: ['expose' => true])]
    #[IsGranted(TutoringSessionVoter::TUTOR_DELETE_TUTORING_SESSION, subject: 'tutoringSession')]
    public function deleteTutoringSession(TutoringSession $tutoringSession, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($tutoringSession);
        $entityManager->flush();

        return $this->json([
            'status' => 'OK',
        ], Response::HTTP_OK);
    }
}
