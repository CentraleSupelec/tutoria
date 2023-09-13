<?php

declare(strict_types=1);

namespace App\Controller\Jwks;

use App\Utils\LtiToolUtils;
use OAT\Library\Lti1p3Core\Security\Jwks\Exporter\JwksExporter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/jwks')]
class JwksController extends AbstractController
{
    #[Route('/tutorIaKeySet.json', name: 'jwks')]
    public function index(JwksExporter $jwksExporter): JsonResponse
    {
        return new JsonResponse($jwksExporter->export(LtiToolUtils::TUTOR_IA_KEY_SET_NAME));
    }
}
