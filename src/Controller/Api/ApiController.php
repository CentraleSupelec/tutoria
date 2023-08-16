<?php

namespace App\Controller\Api;

use App\Entity\Tutoring;
use App\Entity\TutoringSession;
use App\Form\TutoringSessionSearchType;
use App\Model\TutoringSessionSearch;
use App\Repository\CampusRepository;
use App\Repository\TutoringSessionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api')]
class ApiController extends AbstractController
{
    #[Route('/campuses', name: 'campuses', options: ['expose' => true])]
    public function getBuildings(CampusRepository $campusRepository, SerializerInterface $serializer): JsonResponse
    {
        $campuses = $campusRepository->findAll();
        $campusesJSON = $serializer->serialize($campuses, 'json', ['groups' => 'api']);

        return new JsonResponse($campusesJSON, json: true);
    }

    #[Route('/tutoring/{id}', name: 'get_tutoring', options: ['expose' => true])]
    public function getTutoring(Tutoring $tutoring, SerializerInterface $serializer): JsonResponse
    {
        $tutoringJSON = $serializer->serialize($tutoring, 'json', ['groups' => 'tutorings']);

        return new JsonResponse($tutoringJSON, json: true);
    }

    #[Route('/tutoring-session/{id}', name: 'get_tutoring_session', options: ['expose' => true])]
    public function getTutoringSession(TutoringSession $tutoringSession, SerializerInterface $serializer): JsonResponse
    {
        $tutoringSessionJSON = $serializer->serialize($tutoringSession, 'json', ['groups' => 'tutorings']);

        return new JsonResponse($tutoringSessionJSON, json: true);
    }

    #[Route('/tutoring-sessions-by-tutorings', name: 'tutoring_sessions_by_tutorings', options: ['expose' => true], methods: ['POST'])]
    public function getTutoringSessions(
        Request $request,
        SerializerInterface $serializer,
        TutoringSessionRepository $tutoringSessionRepository,
    ): Response {
        $tutoringSessionSearch = new TutoringSessionSearch();
        $form = $this->createForm(TutoringSessionSearchType::class, $tutoringSessionSearch);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tutoringSessions = $tutoringSessionRepository->findByTutorings($tutoringSessionSearch->getTutorings());
        } else {
            $tutoringSessions = $tutoringSessionRepository->findAll();
        }
        $tutoringSessionsJSON = $serializer->serialize($tutoringSessions, 'json', ['groups' => 'tutoringSessions']);

        return new JsonResponse($tutoringSessionsJSON, json: true);
    }
}
