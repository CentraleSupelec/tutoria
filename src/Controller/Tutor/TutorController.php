<?php

namespace App\Controller\Tutor;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/tutor')]
class TutorController extends AbstractController
{
    public function __construct()
    {
    }

    #[Route('/', name: 'tutor_dashboard')]
    public function index(): Response
    {
        return $this->render('tutor/dashboard.html.twig');
    }
}
