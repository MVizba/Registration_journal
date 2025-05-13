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
            $dateRange = ['start' => $startDate, 'end' => $endDate];

            $asignedDrugs = $asignedDrugsRepository->findByDateRange($startDate, $endDate);

            if (empty($asignedDrugs)) {
                $this->addFlash('warning', 'Šiame periode nėra išrašytų medikamentų.');
                return $this->redirectToRoute('app_report_summary_index');
            }

            // Group assigned drugs by drug warehouse for summary
            foreach ($asignedDrugs as $asignedDrug) {
                $drugWarehouse = $asignedDrug->getDrugWarehouse();
                if (!$drugWarehouse) continue;

                $drugId = $drugWarehouse->getId();
                if (!isset($drugSummary[$drugId])) {
                    $drugSummary[$drugId] = [
                        'name' => $drugWarehouse->getDrugName(),
                        'initial' => $drugWarehouse->getAmount(),
                        'used' => $drugWarehouse->getUsedAmount(),
                        'remaining' => $drugWarehouse->getRemainingAmount(),
                        'unit' => $drugWarehouse->getType(),
                        'expiration' => $drugWarehouse->getExpirationDate()
                    ];
                }
            }
        }

        return $this->render('report/summary.html.twig', [
            'form' => $form->createView(),
            'drugSummary' => $drugSummary,
            'dateRange' => $dateRange
        ]);
    }
} 