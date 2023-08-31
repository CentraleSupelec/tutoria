<?php

namespace App\Controller\Tutee;

use App\Entity\Student;
use App\Entity\TutoringSession;
use App\Repository\TutoringRepository;
use App\Repository\TutoringSessionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/tutee')]
class TuteeController extends AbstractController
{
    #[Route('/', name: 'tutee_home')]
    public function index(
        TutoringRepository $tutoringRepository,
        TutoringSessionRepository $tutoringSessionRepository,
    ): Response {
        /** @var Student $user */
        $user = $this->getUser();
        $allTutorings = $tutoringRepository->fetchAllTutoringWithSessionsWithFutureEndDate();
        $allTutoringSessions = $tutoringSessionRepository->fetchAllTutoringSessionsWithFutureEndDate();
        $incomingTutoringSessions = $tutoringSessionRepository->findIncomingSessionsByTutee($user);
        $pastTutoringSessions = $tutoringSessionRepository->findPastSessionsByTutee($user);

        return $this->render('tutee/dashboard.html.twig', [
            'controller_name' => 'TuteeController',
            'tutorings' => $allTutorings,
            'tutoringSessions' => $allTutoringSessions,
            'incomingTutoringSessions' => $incomingTutoringSessions,
            'pastTutoringSessions' => $pastTutoringSessions,
        ]);
    }

    #[Route('/tutoring-session/{id}/subscribe', name: 'subscribe_to_tutoring_session', options: ['expose' => true])]
    public function subscribeToTutoringSession(TutoringSession $tutoringSession, EntityManagerInterface $entityManager): Response
    {
        /** @var Student $user */
        $user = $this->getUser();
        $tutoringSession->addStudent($user);

        $entityManager->persist($tutoringSession);
        $entityManager->flush();

        return new Response('', Response::HTTP_OK);
    }

    #[Route('/tutoring-session/{id}/unsubscribe', name: 'unsubscribe_to_tutoring_session', options: ['expose' => true])]
    public function unsubscribeToTutoringSession(TutoringSession $tutoringSession, EntityManagerInterface $entityManager): Response
    {
        /** @var Student $user */
        $user = $this->getUser();
        $tutoringSession->removeStudent($user);

        $entityManager->persist($tutoringSession);
        $entityManager->flush();

        return new Response('', Response::HTTP_OK);
    }
}
