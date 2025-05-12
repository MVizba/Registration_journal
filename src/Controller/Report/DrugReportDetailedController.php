<?php

namespace App\Controller\Report;

use App\Form\DrugReportType;
use App\Repository\AsignedDrugsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/report/detailed')]
#[IsGranted('ROLE_USER')]
class DrugReportDetailedController extends AbstractController
{
    #[Route('/', name: 'app_report_detailed_index', methods: ['GET', 'POST'])]
    public function index(
        Request $request,
        AsignedDrugsRepository $asignedDrugsRepository
    ): Response {
        $form = $this->createForm(DrugReportType::class);
        $form->handleRequest($request);

        $detailedUsage = [];
        $dateRange = null;

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $startDate = $data['startDate'];
            $endDate = $data['endDate'];
            $dateRange = ['start' => $startDate, 'end' => $endDate];

            $asignedDrugs = $asignedDrugsRepository->findByDateRange($startDate, $endDate);

            if (empty($asignedDrugs)) {
                $this->addFlash('warning', 'No assigned drugs found for the specified date range.');
                return $this->redirectToRoute('app_report_detailed_index');
            }

            foreach ($asignedDrugs as $asignedDrug) {
                $drugWarehouse = $asignedDrug->getDrugWarehouse();
                if (!$drugWarehouse) continue;

                $appointment = $asignedDrug->getAppointment();
                $detailedUsage[] = [
                    'date' => $asignedDrug->getDate(),
                    'drugName' => $drugWarehouse->getDrugName(),
                    'amount' => $asignedDrug->getAmount(),
                    'unit' => $drugWarehouse->getType(),
                    'patient' => $appointment && $appointment->getPatient() ? $appointment->getPatient()->getName() : 'Unknown',
                    'client' => $appointment && $appointment->getClient() ? $appointment->getClient()->getName() : 'Unknown'
                ];
            }
        }

        return $this->render('report/detailed.html.twig', [
            'form' => $form->createView(),
            'detailedUsage' => $detailedUsage,
            'dateRange' => $dateRange
        ]);
    }
} 