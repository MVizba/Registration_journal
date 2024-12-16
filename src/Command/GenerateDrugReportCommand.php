<?php

namespace App\Command;

use App\Repository\AsignedDrugsRepository;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:drug-report',
    description: 'Generate a report of assigned drugs within a specified time period and export it to Excel.',
)]
class DrugReportCommand extends Command
{
    private AsignedDrugsRepository $asignedDrugsRepository;

    public function __construct(AsignedDrugsRepository $asignedDrugsRepository)
    {
        parent::__construct();
        $this->asignedDrugsRepository = $asignedDrugsRepository;
    }

    protected function configure()
    {
        $this
            ->addArgument('start_date', InputArgument::REQUIRED, 'Start date (YYYY-MM-DD)')
            ->addArgument('end_date', InputArgument::REQUIRED, 'End date (YYYY-MM-DD)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $startDate = new \DateTime($input->getArgument('start_date'));
        $endDate = new \DateTime($input->getArgument('end_date'));

        $asignedDrugs = $this->asignedDrugsRepository->findByDateRange($startDate, $endDate);

        if (!$asignedDrugs) {
            $io->warning('No assigned drugs found for the specified date range.');

            return Command::SUCCESS;
        }

        $this->exportToExcel($asignedDrugs, $startDate, $endDate, $io);

        return Command::SUCCESS;
    }

    private function exportToExcel(array $asignedDrugs, \DateTime $startDate, \DateTime $endDate, SymfonyStyle $io): void
    {
        $filename = "drug_report_{$startDate->format('Ymd')}_to_{$endDate->format('Ymd')}.xlsx";

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Drug Report');

        $sheet->fromArray(['Date', 'Drug Name', 'Amount', 'Patient', 'Client'], null, 'A1');

        $row = 2;
        foreach ($asignedDrugs as $asignedDrug) {
            $appointment = $asignedDrug->getAppointment();
            $sheet->fromArray([
                $asignedDrug->getDate()->format('Y-m-d'),
                $asignedDrug->getDrugWarehouse()->getDrugName(),
                $asignedDrug->getAmount(),
                $appointment ? $appointment->getPatient()->getName() : 'Unknown',
                $appointment ? $appointment->getClient()->getName() : 'Unknown',
            ], null, "A$row");
            ++$row;
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save($filename);

        $io->success("Report exported to Excel file: $filename");
    }
}
