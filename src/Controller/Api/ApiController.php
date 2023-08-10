<?php

namespace App\Controller\Api;

use App\Repository\CampusRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
}
