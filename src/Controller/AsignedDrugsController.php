<?php

namespace App\Controller;

use App\Entity\Appointment;
use App\Entity\AsignedDrugs;
use App\Form\AsignedDrugsType;
use App\Repository\AsignedDrugsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/asigned/drugs')]
final class AsignedDrugsController extends AbstractController
{
    #[Route(name: 'app_asigned_drugs_index', methods: ['GET'])]
    public function index(AsignedDrugsRepository $asignedDrugsRepository): Response
    {
        $asignedDrugs = $asignedDrugsRepository->findAll();
        return $this->render('asigned_drugs/index.html.twig', [
            'asigned_drugs' => $asignedDrugs,
        ]);
    }

    #[Route('/new/{appointmentId}', name: 'app_asigned_drugs_new', methods: ['GET', 'POST'])]
    public function new(int $appointmentId, Request $request, EntityManagerInterface $entityManager): Response
    {
        $appointment = $entityManager->getRepository(Appointment::class)->find($appointmentId);

        if (!$appointment) {
            $this->addFlash('error', 'Appointment not found.');

            return $this->redirectToRoute('app_appointment_index');
        }

        $asignedDrug = new AsignedDrugs();
        $asignedDrug->setAppointment($appointment);

        $form = $this->createForm(AsignedDrugsType::class, $asignedDrug);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($asignedDrug);
            $entityManager->flush();

            return $this->redirectToRoute('app_appointment_edit', [
                'id' => $appointment->getId(),
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->render('asigned_drugs/new.html.twig', [
            'asigned_drug' => $asignedDrug,
            'form' => $form->createView(),
            'appointment' => $appointment,
        ]);
    }

    #[Route('/{id}', name: 'app_asigned_drugs_show', methods: ['GET'])]
    public function show(AsignedDrugs $asignedDrug): Response
    {
        return $this->render('asigned_drugs/show.html.twig', [
            'asigned_drug' => $asignedDrug,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_asigned_drugs_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, AsignedDrugs $asignedDrug, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AsignedDrugsType::class, $asignedDrug);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_appointment_edit', [
                'id' => $asignedDrug->getAppointment()->getId(),
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->render('asigned_drugs/edit.html.twig', [
            'asigned_drug' => $asignedDrug,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_asigned_drugs_delete', methods: ['POST'])]
    public function delete(Request $request, AsignedDrugs $asignedDrug, EntityManagerInterface $entityManager): Response
    {
        $appointmentId = $asignedDrug->getAppointment()->getId();

        if ($this->isCsrfTokenValid('delete'.$asignedDrug->getId(), $request->request->get('_token'))) {
            $entityManager->remove($asignedDrug);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_appointment_edit', [
            'id' => $appointmentId,
        ], Response::HTTP_SEE_OTHER);
    }
}
