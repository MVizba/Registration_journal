<?php

namespace App\Controller;

use App\Entity\ExaminationWithResults;
use App\Entity\Appointment;
use App\Form\ExaminationWithResultsType;
use App\Repository\ExaminationWithResultsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/examination/w/r')]
final class ExaminationWRController extends AbstractController
{
    #[Route(name: 'app_examination_w_r_index', methods: ['GET'])]
    public function index(ExaminationWithResultsRepository $examinationWithResultsRepository): Response
    {
        return $this->render('examination_wr/index.html.twig', [
            'examination_with_results' => $examinationWithResultsRepository->findAll(),
        ]);
    }

    #[Route('/new/{appointmentId}', name: 'app_examination_w_r_new', methods: ['GET', 'POST'])]
    public function new(int $appointmentId, Request $request, EntityManagerInterface $entityManager): Response
    {
        $appointment = $entityManager->getRepository(Appointment::class)->find($appointmentId);

        if (!$appointment) {
            $this->addFlash('error', 'Appointment not found.');
            return $this->redirectToRoute('app_appointment_index');
        }

        $examinationWithResult = new ExaminationWithResults();
        $examinationWithResult->setAppointment($appointment);

        $form = $this->createForm(ExaminationWithResultsType::class, $examinationWithResult);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($examinationWithResult);
            $entityManager->flush();

            return $this->redirectToRoute('app_appointment_edit', [
                'id' => $appointment->getId(),
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->render('examination_wr/new.html.twig', [
            'examination_with_result' => $examinationWithResult,
            'form' => $form->createView(),
            'appointment' => $appointment,
        ]);
    }

    #[Route('/{id}', name: 'app_examination_w_r_show', methods: ['GET'])]
    public function show(ExaminationWithResults $examinationWithResult): Response
    {
        return $this->render('examination_wr/show.html.twig', [
            'examination_with_result' => $examinationWithResult,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_examination_w_r_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ExaminationWithResults $examinationWithResult, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ExaminationWithResultsType::class, $examinationWithResult);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_appointment_edit', [
                'id' => $examinationWithResult->getAppointment()->getId(),
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->render('examination_wr/edit.html.twig', [
            'examination_with_result' => $examinationWithResult,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_examination_w_r_delete', methods: ['POST'])]
    public function delete(Request $request, ExaminationWithResults $examinationWithResult, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$examinationWithResult->getId(), $request->request->get('_token'))) {
            $entityManager->remove($examinationWithResult);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_appointment_edit', [
            'id' => $examinationWithResult->getAppointment()->getId(),
        ], Response::HTTP_SEE_OTHER);return $this->redirectToRoute('app_appointment_edit', [], Response::HTTP_SEE_OTHER);
    }
}
