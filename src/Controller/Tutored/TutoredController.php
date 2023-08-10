<?php

namespace App\Controller\Tutored;

use App\Entity\Student;
use App\Repository\TutoringSessionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/tutored')]
class TutoredController extends AbstractController
{
    #[Route('/', name: 'tutored_home')]
    public function index(
        TutoringSessionRepository $tutoringSessionRepository,
    ): Response {
        /** @var Student $user */
        $user = $this->getUser();
        $tutoringSessions = $tutoringSessionRepository->findByTutored($user);

        return $this->render('tutored/dashboard.html.twig', [
            'controller_name' => 'TutoredController',
            'tutoringSessions' => $tutoringSessions,
        ]);
    }
}
