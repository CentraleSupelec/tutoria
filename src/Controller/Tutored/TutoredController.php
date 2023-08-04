<?php

namespace App\Controller\Tutored;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/tutored")
 */
class TutoredController extends AbstractController
{
    #[Route('/', name: 'tutored_home')]
    public function index(): Response
    {
        return $this->render('tutored/index.html.twig', [
            'controller_name' => 'TutoredController',
        ]);
    }
}
