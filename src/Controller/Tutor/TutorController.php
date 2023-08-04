<?php

namespace App\Controller\Tutor;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/tutor")
 */
class TutorController extends AbstractController
{
    #[Route('/', name: 'tutor_home')]
    public function index(): Response
    {
        return $this->render('tutor/index.html.twig', [
            'controller_name' => 'TutorController',
        ]);
    }
}
