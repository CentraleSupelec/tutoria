<?php

namespace App\Controller\Student;

use App\Entity\Student;
use App\Repository\TutoringSessionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/student')]
class StudentController extends AbstractController
{
    public function __construct(
        private readonly TutoringSessionRepository $tutoringSessionRepository,
    ) {
    }

    #[Route('/', name: 'student_dashboard')]
    public function index(): Response
    {
        /** @var Student $user */
        $user = $this->getUser();
        $tutoringSessions = $this->tutoringSessionRepository->findByStudent($user);

        return $this->render('student/dashboard.html.twig', [
            'tutoringSessions' => $tutoringSessions,
        ]);
    }
}
