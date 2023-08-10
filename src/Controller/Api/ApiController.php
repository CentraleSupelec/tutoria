<?php

namespace App\Controller\Api;

use App\Repository\CampusRepository;
use App\Repository\TutoringSessionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('/tutoring-sessions-by-tutorings', name: 'tutoring_sessions_by_tutorings', options: ['expose' => true], methods: ['POST'])]
    public function getTutoringSessions(
        Request $request,
        SerializerInterface $serializer,
        TutoringSessionRepository $tutoringSessionRepository,
    ): JsonResponse {
        $parameters = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $tutoringIds = $parameters['tutorings'];
        if ($tutoringIds) {
            $tutoringSessions = $tutoringSessionRepository->findByTutorings($tutoringIds);
        } else {
            $tutoringSessions = $tutoringSessionRepository->findAll();
        }
        $tutoringSessionsJSON = $serializer->serialize($tutoringSessions, 'json', ['groups' => 'tutoringSessions']);

        return new JsonResponse($tutoringSessionsJSON, json: true);
    }
}
