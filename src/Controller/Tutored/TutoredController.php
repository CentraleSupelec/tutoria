<?php

namespace App\Controller\Tutored;

use App\Entity\Student;
use App\Form\TutoringSessionSearchType;
use App\Model\TutoringSessionSearch;
use App\Repository\TutoringRepository;
use App\Repository\TutoringSessionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/tutored')]
class TutoredController extends AbstractController
{
    #[Route('/', name: 'tutored_home')]
    public function index(
        Request $request,
        TutoringRepository $tutoringRepository,
        TutoringSessionRepository $tutoringSessionRepository,
    ): Response {
        /** @var Student $user */
        $user = $this->getUser();
        $tutoringSessions = $tutoringSessionRepository->findByTutored($user);
        $allTutorings = $tutoringRepository->findAll();

        $tutoringSessionSearch = new TutoringSessionSearch();
        $form = $this->createForm(TutoringSessionSearchType::class, $tutoringSessionSearch, [
            'tutorings' => $allTutorings,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tutoringSessions = $tutoringSessionRepository->findBy(['tutoring' => $tutoringSessionSearch->getTutoring()->getId()]);
        }

        return $this->render('tutored/dashboard.html.twig', [
            'controller_name' => 'TutoredController',
            'tutoringSessions' => $tutoringSessions,
            'tutorings' => $allTutorings,
            'form' => $form,
        ]);
    }
}
