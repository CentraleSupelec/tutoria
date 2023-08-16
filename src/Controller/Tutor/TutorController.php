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
use App\Repository\TutoringSessionRepository;
use App\Service\BatchTutoringSessionCreationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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

    #[Route('/batch-create-sessions', methods: ['POST'], name: 'batch_create_sessions', options: ['expose' => true])]
    public function batchCreateSessions(Request $request, BatchTutoringSessionCreationService $batchTutoringSessionCreationService, EntityManagerInterface $entityManager): Response
    {
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
                    ->setDefaultRoom($batchTutoringSessionCreationModel->getRoom());

                $entityManager->persist($tutoring);
                $entityManager->flush();
            }
            $batchTutoringSessionCreationService->batchCreateSessions($batchTutoringSessionCreationModel);

            return new Response('Sessions created successfully !', Response::HTTP_OK);
        } else {
            return new Response('Form is invalid', Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    #[Route('/tutoring/{id}/update', methods: ['POST'], name: 'update_tutoring', options: ['expose' => true])]
    public function updateTutoring(Tutoring $tutoring, Request $request, EntityManagerInterface $entityManager, TutoringRepository $tutoringRepository): Response
    {
        $form = $this->createForm(TutoringType::class, $tutoring);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($tutoring);
            $entityManager->flush();

            return new Response('Tutoring updated successfully !', Response::HTTP_OK);
        } else {
            return new Response('Form is invalid', Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    #[Route('/tutoring-session/new', methods: ['POST'], name: 'create_tutoring_session', options: ['expose' => true])]
    public function createTutoringSession(Request $request, EntityManagerInterface $entityManager, TutoringRepository $tutoringRepository): Response
    {
        /** @var Student $user */
        $user = $this->getUser();

        $tutoringSession = new TutoringSession();
        $form = $this->createForm(TutoringSessionType::class, $tutoringSession);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tutoringSession
                ->setCreatedBy($user);
            $entityManager->persist($tutoringSession);
            $entityManager->flush();

            return new Response('Tutoring session created successfully !', Response::HTTP_OK);
        } else {
            return new Response('Form is invalid', Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    #[Route('/tutoring-session/{id}/update', methods: ['POST'], name: 'update_tutoring_session', options: ['expose' => true])]
    public function updateTutoringSession(TutoringSession $tutoringSession, Request $request, EntityManagerInterface $entityManager, TutoringSessionRepository $tutoringSessionRepository): Response
    {
        $form = $this->createForm(TutoringSessionType::class, $tutoringSession);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($tutoringSession);
            $entityManager->flush();

            return new Response('Tutoring session updated successfully !', Response::HTTP_OK);
        } else {
            return new Response('Form is invalid', Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
