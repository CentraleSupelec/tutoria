<?php

namespace App\Controller;

use App\Entity\Administrator;
use App\Entity\Student;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'base_home')]
    #[Route('/accueil', name: 'home')]
    public function index(): Response
    {
        $user = $this->getUser();

        if ($user instanceof Administrator) {
            return $this->redirectToRoute('sonata_admin_dashboard');
        } elseif ($user instanceof Student) {
            if (in_array(Student::ROLE_TUTOR, $user->getRoles())) {
                return $this->redirectToRoute('tutor_home');
            } else {
                return $this->redirectToRoute('tutee_home');
            }
        }

        return $this->render('home/index.html.twig');
    }
}
