<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends AbstractController
{
    #[Route('/tutor/logout', name: 'student_cas_logout')]
    public function logoutWithCAS(): void
    {
        // Handled by LogoutEvent catcher
    }

    #[Route('/tutor/forbidden', name: 'student_access_denied')]
    public function accessDenied(): Response
    {
        return $this->render('security/access_denied.html.twig');
    }
}
