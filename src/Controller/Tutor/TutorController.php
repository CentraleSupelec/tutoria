<?php

namespace App\Controller\Tutor;

use App\Entity\Student;
use App\Form\BatchTutoringSessionCreationType;
use App\Model\BatchTutoringSessionCreationModel;
use App\Repository\TutoringRepository;
use App\Service\BatchTutoringSessionCreationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/tutor')]
class TutorController extends AbstractController
{
    #[Route('/', name: 'tutor_home')]
    public function index(
        TutoringRepository $tutoringRepository
    ): Response {
        /** @var Student $user */
        $user = $this->getUser();
        $tutorings = $tutoringRepository->findByTutor($user);

        return $this->render('tutor/dashboard.html.twig', [
            'tutorings' => $tutorings,
        ]);
    }

    #[Route('/batch-create-sessions', name: 'batch_create_sessions', options: ['expose' => true], methods: ['POST'])]
    public function batchCreateSessions(Request $request, BatchTutoringSessionCreationService $batchTutoringSessionCreationService): Response
    {
        $batchTutoringSessionCreationModel = new BatchTutoringSessionCreationModel();
        $form = $this->createForm(BatchTutoringSessionCreationType::class, $batchTutoringSessionCreationModel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $batchTutoringSessionCreationService->batchCreateSessions($batchTutoringSessionCreationModel);

            return new Response('Sessions created successfully !', Response::HTTP_OK);
        } else {
            return new Response('', Response::HTTP_BAD_REQUEST);
        }
    }
}
