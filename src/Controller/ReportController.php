<?php

namespace App\Controller;

use App\Form\DrugReportType;
use App\Repository\AsignedDrugsRepository;
use App\Repository\DrugWarehouseRepository;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/report')]
#[IsGranted('ROLE_USER')]
class ReportController extends AbstractController
{
    #[Route('/', name: 'app_report_index', methods: ['GET', 'POST'])]
    public function index(
        Request $request,
        AsignedDrugsRepository $asignedDrugsRepository,
        DrugWarehouseRepository $drugWarehouseRepository
    ): Response {
        $form = $this->createForm(DrugReportType::class);
        $form->handleRequest($request);

        $drugSummary = [];
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
                return $this->redirectToRoute('app_report_index');
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

                // Collect detailed usage information
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

            // If download button was clicked
            if ($request->request->has('download')) {
                $downloadType = $request->request->get('download');
                if ($downloadType === 'summary') {
                    return $this->generateExcelReport($drugSummary, $detailedUsage, $startDate, $endDate, true);
                } elseif ($downloadType === 'detailed') {
                    return $this->generateExcelReport($drugSummary, $detailedUsage, $startDate, $endDate, false);
                }
            }
        }

        return $this->render('report/index.html.twig', [
            'form' => $form->createView(),
            'drugSummary' => $drugSummary,
            'detailedUsage' => $detailedUsage,
            'dateRange' => $dateRange
        ]);
    }

    private function generateExcelReport(array $drugSummary, array $detailedUsage, \DateTime $startDate, \DateTime $endDate, bool $isSummary = true): Response
    {
        $spreadsheet = new Spreadsheet();
        
        if ($isSummary) {
            // First sheet - Drug Usage Summary
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Drug Usage Summary');

            // Set headers
            $sheet->fromArray([
                'Drug Name',
                'Initial Amount',
                'Used Amount',
                'Remaining Amount',
                'Unit',
                'Expiration Date'
            ], null, 'A1');

            // Add summary data
            $row = 2;
            foreach ($drugSummary as $summary) {
                $sheet->fromArray([
                    $summary['name'],
                    $summary['initial'],
                    $summary['used'],
                    $summary['remaining'],
                    $summary['unit'],
                    $summary['expiration']->format('Y-m-d')
                ], null, "A$row");
                ++$row;
            }

            // Auto-size columns
            foreach (range('A', 'F') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            $filename = sprintf('drug_summary_%s_to_%s.xlsx',
                $startDate->format('Ymd'),
                $endDate->format('Ymd')
            );
        } else {
            // Detailed Usage sheet
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Detailed Usage');

            // Set headers
            $sheet->fromArray([
                'Date',
                'Drug Name',
                'Amount Used',
                'Unit',
                'Patient',
                'Client'
            ], null, 'A1');

            // Add detailed data
            $row = 2;
            foreach ($detailedUsage as $usage) {
                $sheet->fromArray([
                    $usage['date']->format('Y-m-d'),
                    $usage['drugName'],
                    $usage['amount'],
                    $usage['unit'],
                    $usage['patient'],
                    $usage['client']
                ], null, "A$row");
                ++$row;
            }

            // Auto-size columns
            foreach (range('A', 'F') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            $filename = sprintf('drug_detailed_usage_%s_to_%s.xlsx',
                $startDate->format('Ymd'),
                $endDate->format('Ymd')
            );
        }

        // Create the Excel file
        $writer = new Xlsx($spreadsheet);
        $tempFile = tempnam(sys_get_temp_dir(), 'drug_report_');
        $writer->save($tempFile);

        // Return the file as a download
        $response = new BinaryFileResponse($tempFile);
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $filename
        );
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->deleteFileAfterSend(true);

        return $response;
    }
} 