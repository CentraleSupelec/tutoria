<?php

namespace App\Controller\Tutee;

use App\Entity\Student;
use App\Repository\TutoringRepository;
use App\Repository\TutoringSessionRepository;
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
        $tutoringSessions = $tutoringSessionRepository->findByTutee($user);
        $allTutorings = $tutoringRepository->findAll();

        return $this->render('tutee/dashboard.html.twig', [
            'controller_name' => 'TuteeController',
            'tutoringSessions' => $tutoringSessions,
            'tutorings' => $allTutorings,
        ]);
    }
}
