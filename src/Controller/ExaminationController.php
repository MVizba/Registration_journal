<?php

namespace App\Controller;

use App\Entity\Examination;
use App\Form\ExaminationType;
use App\Repository\ExaminationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/examination')]
#[IsGranted('ROLE_ADMIN')]

final class ExaminationController extends AbstractController
{
    #[Route(name: 'app_examination_index', methods: ['GET'])]
    public function index(ExaminationRepository $examinationRepository): Response
    {
        return $this->render('examination/index.html.twig', [
            'examinations' => $examinationRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_examination_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $examination = new Examination();
        $form = $this->createForm(ExaminationType::class, $examination);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($examination);
            $entityManager->flush();

            return $this->redirectToRoute('app_examination_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('examination/new.html.twig', [
            'examination' => $examination,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_examination_show', methods: ['GET'])]
    public function show(Examination $examination): Response
    {
        return $this->render('examination/show.html.twig', [
            'examination' => $examination,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_examination_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Examination $examination, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ExaminationType::class, $examination);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_examination_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('examination/edit.html.twig', [
            'examination' => $examination,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_examination_delete', methods: ['POST'])]
    public function delete(Request $request, Examination $examination, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$examination->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($examination);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_examination_index', [], Response::HTTP_SEE_OTHER);
    }
}
