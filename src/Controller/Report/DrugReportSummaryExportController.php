<?php

namespace App\Controller\Report;

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

#[Route('/report/export/summary')]
#[IsGranted('ROLE_USER')]
class DrugReportSummaryExportController extends AbstractController
{
    #[Route('/', name: 'app_report_summary_export', methods: ['POST'])]
    public function export(
        Request $request,
        AsignedDrugsRepository $asignedDrugsRepository,
        DrugWarehouseRepository $drugWarehouseRepository,
    ): Response {
        $form = $this->createForm(DrugReportType::class);
        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            $this->addFlash('error', 'Invalid form submission');

            return $this->redirectToRoute('app_report_summary_index');
        }

        $data = $form->getData();
        $startDate = $data['startDate'];
        $endDate = $data['endDate'];

        $asignedDrugs = $asignedDrugsRepository->findByDateRange($startDate, $endDate);
        if (empty($asignedDrugs)) {
            $this->addFlash('warning', 'Šiame periode nėra išrašytų medikamentų.');

            return $this->redirectToRoute('app_report_summary_index');
        }

        // Generate summary data
        $drugSummary = [];
        foreach ($asignedDrugs as $asignedDrug) {
            $drugWarehouse = $asignedDrug->getDrugWarehouse();
            if (!$drugWarehouse) {
                continue;
            }

            $drugId = $drugWarehouse->getId();
            if (!isset($drugSummary[$drugId])) {
                $drugSummary[$drugId] = [
                    'name' => $drugWarehouse->getDrugName(),
                    'initial' => $drugWarehouse->getAmount(),
                    'used' => $drugWarehouse->getUsedAmount(),
                    'remaining' => $drugWarehouse->getRemainingAmount(),
                    'unit' => $drugWarehouse->getType(),
                    'expiration' => $drugWarehouse->getExpirationDate(),
                ];
            }
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Drug Usage Summary');

        // Set headers
        $sheet->setCellValue('A1', 'Pavadinimas')
            ->setCellValue('B1', 'Gauta')
            ->setCellValue('C1', 'Sunaudota')
            ->setCellValue('D1', 'Liko')
            ->setCellValue('E1', 'Tipas')
            ->setCellValue('F1', 'Galioja iki:');

        // Add summary data
        $row = 2;
        foreach ($drugSummary as $summary) {
            $sheet->fromArray([
                $summary['name'],
                $summary['initial'],
                $summary['used'],
                $summary['remaining'],
                $summary['unit'],
                $summary['expiration']->format('Y-m-d'),
            ], null, "A$row");
            ++$row;
        }

        // Auto-size columns
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = sprintf(
            'drug_summary_%s_to_%s.xlsx',
            $startDate->format('Ymd'),
            $endDate->format('Ymd')
        );

        return $this->createExcelResponse($spreadsheet, $filename);
    }

    private function createExcelResponse(Spreadsheet $spreadsheet, string $filename): Response
    {
        $writer = new Xlsx($spreadsheet);
        $tempFile = tempnam(sys_get_temp_dir(), 'drug_report_');
        $writer->save($tempFile);

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
