<?php

namespace App\Controller\Report;

use App\Form\DrugReportType;
use App\Repository\AsignedDrugsRepository;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/report/export/detailed')]
#[IsGranted('ROLE_USER')]
class DrugReportDetailedExportController extends AbstractController
{
    #[Route('/', name: 'app_report_detailed_export', methods: ['POST'])]
    public function export(
        Request $request,
        AsignedDrugsRepository $asignedDrugsRepository
    ): Response {
        $form = $this->createForm(DrugReportType::class);
        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            $this->addFlash('error', 'Invalid form submission');
            return $this->redirectToRoute('app_report_detailed_index');
        }

        $data = $form->getData();
        $startDate = $data['startDate'];
        $endDate = $data['endDate'];

        $asignedDrugs = $asignedDrugsRepository->findByDateRange($startDate, $endDate);
        if (empty($asignedDrugs)) {
            $this->addFlash('warning', 'No data found for the specified date range');
            return $this->redirectToRoute('app_report_detailed_index');
        }

        // Generate detailed usage data
        $detailedUsage = [];
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

        $spreadsheet = new Spreadsheet();
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