<?php

namespace App\Controller;

use App\Entity\DrugWarehouse;
use App\Form\DrugWarehouseType;
use App\Repository\DrugWarehouseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/drug/warehouse')]
#[IsGranted('ROLE_ADMIN')]

final class DrugWarehouseController extends AbstractController
{
    #[Route(name: 'app_drug_warehouse_index', methods: ['GET'])]
    public function index(DrugWarehouseRepository $drugWarehouseRepository): Response
    {
        return $this->render('drug_warehouse/index.html.twig', [
            'drug_warehouses' => $drugWarehouseRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_drug_warehouse_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $drugWarehouse = new DrugWarehouse();
        $form = $this->createForm(DrugWarehouseType::class, $drugWarehouse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($drugWarehouse);
            $entityManager->flush();

            return $this->redirectToRoute('app_drug_warehouse_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('drug_warehouse/new.html.twig', [
            'drug_warehouse' => $drugWarehouse,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_drug_warehouse_show', methods: ['GET'])]
    public function show(DrugWarehouse $drugWarehouse): Response
    {
        return $this->render('drug_warehouse/show.html.twig', [
            'drug_warehouse' => $drugWarehouse,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_drug_warehouse_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, DrugWarehouse $drugWarehouse, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DrugWarehouseType::class, $drugWarehouse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_drug_warehouse_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('drug_warehouse/edit.html.twig', [
            'drug_warehouse' => $drugWarehouse,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_drug_warehouse_delete', methods: ['POST'])]
    public function delete(Request $request, DrugWarehouse $drugWarehouse, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$drugWarehouse->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($drugWarehouse);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_drug_warehouse_index', [], Response::HTTP_SEE_OTHER);
    }
}
