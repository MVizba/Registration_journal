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
        // Ensure the full day is included for both start and end dates
        if ($startDate instanceof \DateTime) {
            $startDate->setTime(0, 0, 0);
        }
        if ($endDate instanceof \DateTime) {
            $endDate->setTime(23, 59, 59);
        }

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

        // Compose summary for all drugs
        $drugSummary = [];
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

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Drug Usage Summary');

        // Ataskaitos pavadinimas
        $sheet->setCellValue('A1', 'VETERINARINIŲ VAISTŲ IR VAISTINIŲ PREPARATŲ APSKAITOS ŽURNALAS');
        $sheet->mergeCells('A1:H1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        // Date range info in the same cell per line
        $sheet->setCellValue('A2', 'Nuo: ' . ($startDate instanceof \DateTimeInterface ? $startDate->format('Y-m-d') : ''));
        $sheet->setCellValue('A3', 'Iki: ' . ($endDate instanceof \DateTimeInterface ? $endDate->format('Y-m-d') : ''));

        // Headerius pradeti nuo 6 eilutes
        $sheet->setCellValue('A6', 'Gavimo Data')
            ->setCellValue('B6', 'Pavadinimas')
            ->setCellValue('C6', 'Dokumento numeris')
            ->setCellValue('D6', 'Gautas Kiekis')
            ->setCellValue('E6', 'Tipas')
            ->setCellValue('F6', 'Tinkamumo naudoti laikas')
            ->setCellValue('G6', 'Serija')
            ->setCellValue('H6', 'Sunaudotas kiekis')
            ->setCellValue('I6', 'Likutis');
        // Paboldinti headerius
        $sheet->getStyle('A6:I6')->getFont()->setBold(true);

        // Nuo 7 eilutės pridėti ataskaitą
        $row = 7;
        foreach ($drugSummary as $summary) {
            $sheet->fromArray([
                $summary['Gavimo Data'] instanceof \DateTimeInterface ? $summary['Gavimo Data']->format('Y-m-d') : '',
                $summary['Pavadinimas'],
                $summary['Dokumento numeris'],
                $summary['Gautas Kiekis'],
                $summary['Tipas'],
                $summary['Tinkamumo naudoti laikas'] instanceof \DateTimeInterface ? $summary['Tinkamumo naudoti laikas']->format('Y-m-d') : '',
                $summary['Serija'],
                $summary['Sunaudotas kiekis'],
                $summary['Likutis'],
            ], null, "A$row");
            ++$row;
        }

        // Pridėti dvi tuščias linijas
        $row += 2;
        // Pridėti patvirtinimo žinutę.
        $sheet->setCellValue("A$row", 'Patvirtinu, kad šioje ataskaitoje pateikti visi teisingi ir reikiami duomenys ir informacija.');
        $sheet->mergeCells("A$row:I$row");
        $sheet->getStyle("A$row")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $row++;
        // Pridėti dvi tuščias linijas
        $row++;
        $row++;
        // Pridėti vietą parašui
        $sheet->setCellValue("A$row", '_________________________________');
        $row++;
        // pareigų informacija
        $sheet->setCellValue("A$row", 'Ataskaitą užpildžiusio asmens pareigos.');
        $row++;
        // Papildomas tuščia eilutė
        $row++;
        // Pridėti vietą parašui, vardui pavardei
        $sheet->setCellValue("A$row", '_________________________________');
        $row++;
        // Informacija viršutinei eilutei
        $sheet->setCellValue("A$row", 'Vardas, pavardė, parašas');
        $sheet->getStyle("A$row")->getFont()->setItalic(true);


        foreach (range('A', 'I') as $col) {
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
