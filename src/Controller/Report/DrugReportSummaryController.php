<?php

namespace App\Controller\Report;

use App\Form\DrugReportType;
use App\Repository\AsignedDrugsRepository;
use App\Repository\DrugWarehouseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/report/summary')]
#[IsGranted('ROLE_USER')]
class DrugReportSummaryController extends AbstractController
{
    #[Route('/', name: 'app_report_summary_index', methods: ['GET', 'POST'])]
    public function index(
        Request $request,
        AsignedDrugsRepository $asignedDrugsRepository,
        DrugWarehouseRepository $drugWarehouseRepository
    ): Response {
        $form = $this->createForm(DrugReportType::class);
        $form->handleRequest($request);

        $drugSummary = [];
        $dateRange = null;

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $startDate = $data['startDate'];
            $endDate = $data['endDate'];
            // Ensure the full day is included for both start and end dates
            if ($startDate instanceof \DateTime) {
                $startDate->setTime(0, 0, 0);
            }
            if ($endDate instanceof \DateTime) {
                $endDate->setTime(23, 59, 59);
            }
            $dateRange = ['start' => $startDate, 'end' => $endDate];

            // Fetch all drugs in the warehouse
            $allDrugs = $drugWarehouseRepository->findAll();

            // Fetch all assigned drugs in the period
            $asignedDrugs = $asignedDrugsRepository->findByDateRange($startDate, $endDate);

            // Map assigned drugs by warehouse drug id
            $assignedByDrugId = [];
            foreach ($asignedDrugs as $asignedDrug) {
                $drugWarehouse = $asignedDrug->getDrugWarehouse();
                if (!$drugWarehouse) continue;
                $drugId = $drugWarehouse->getId();
                if (!isset($assignedByDrugId[$drugId])) {
                    $assignedByDrugId[$drugId] = [
                        'Sunaudotas kiekis' => 0,
                    ];
                }
                $assignedByDrugId[$drugId]['Sunaudotas kiekis'] += $asignedDrug->getAmount();
            }

            // Fetch all drugs in the warehouse
            $allDrugs = $drugWarehouseRepository->findAll();

            // Compose summary for all drugs
            foreach ($allDrugs as $drugWarehouse) {
                $drugId = $drugWarehouse->getId();
                if (!isset($assignedByDrugId[$drugId])) {
                    continue;
                }
                $drugSummary[$drugId] = [
                    'id' => $drugWarehouse->getId(),
                    'Gavimo Data' => $drugWarehouse->getDateOfReceipt(),
                    'Pavadinimas' => $drugWarehouse->getDrugName(),
                    'Dokumento numeris' => $drugWarehouse->getDocumentNumber(),
                    'Gautas Kiekis' => $drugWarehouse->getAmount(),
                    'Tipas' => $drugWarehouse->getType(),
                    'Tinkamumo naudoti laikas' => $drugWarehouse->getExpirationDate(),
                    'Serija' => $drugWarehouse->getSeries(),
                    'Sunaudotas kiekis' => $assignedByDrugId[$drugId]['Sunaudotas kiekis'],
                    'Likutis' => $drugWarehouse->getRemainingAmount(),
                ];
            }

            // Sort by date ascending
            uasort($drugSummary, function($a, $b) {
                return $a['Gavimo Data'] <=> $b['Gavimo Data'];
            });
        }

        return $this->render('report/summary.html.twig', [
            'form' => $form->createView(),
            'drugSummary' => $drugSummary,
            'dateRange' => $dateRange
        ]);
    }
} 