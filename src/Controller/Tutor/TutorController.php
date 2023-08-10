<?php

namespace App\Controller\Tutor;

use App\Entity\Student;
use App\Repository\TutoringRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/tutor')]
class TutorController extends AbstractController
{
    public function __construct(
        private readonly TutoringRepository $tutoringRepository,
    ) {
    }

    #[Route('/', name: 'tutor_dashboard')]
    public function index(): Response
    {
        /** @var Student $user */
        $user = $this->getUser();
        $tutorings = $this->tutoringRepository->findByTutor($user);

        return $this->render('tutor/dashboard.html.twig', [
            'tutoringSessions' => $tutorings,
        ]);
    }
}
