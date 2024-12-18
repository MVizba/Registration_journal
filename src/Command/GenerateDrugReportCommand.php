<?php

namespace App\Command;

use App\Entity\Appointment;
use App\Entity\AsignedDrugs;
use App\Entity\DrugWarehouse;
use App\Repository\AsignedDrugsRepository;
use DateTime;
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
class GenerateDrugReportCommand extends Command
{
    private AsignedDrugsRepository $asignedDrugsRepository;

    public function __construct(AsignedDrugsRepository $asignedDrugsRepository)
    {
        parent::__construct();
        $this->asignedDrugsRepository = $asignedDrugsRepository;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('start_date', InputArgument::REQUIRED, 'Start date (YYYY-MM-DD)')
            ->addArgument('end_date', InputArgument::REQUIRED, 'End date (YYYY-MM-DD)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $startDateArg = $input->getArgument('start_date');
        $endDateArg = $input->getArgument('end_date');

        // Ensure that these arguments are strings before passing to DateTime
        if (!is_string($startDateArg) || !is_string($endDateArg)) {
            $io->error('Invalid date arguments.');

            return Command::FAILURE;
        }

        try {
            $startDate = new \DateTime($startDateArg);
            $endDate = new \DateTime($endDateArg);
        } catch (\Exception $e) {
            $io->error('Invalid date format. Please use YYYY-MM-DD.');

            return Command::FAILURE;
        }

        $asignedDrugs = $this->asignedDrugsRepository->findByDateRange($startDate, $endDate);

        if (!$asignedDrugs) {
            $io->warning('No assigned drugs found for the specified date range.');

            return Command::SUCCESS;
        }

        $this->exportToExcel($asignedDrugs, $startDate, $endDate, $io);

        return Command::SUCCESS;
    }

    /**
     * @param AsignedDrugs[] $asignedDrugs
     */
    private function exportToExcel(array $asignedDrugs, \DateTimeInterface $startDate, \DateTimeInterface $endDate, SymfonyStyle $io): void
    {
        $filename = "drug_report_{$startDate->format('Ymd')}_to_{$endDate->format('Ymd')}.xlsx";

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Drug Report');

        $sheet->fromArray(['Date', 'Drug Name', 'Amount', 'Patient', 'Client'], null, 'A1');

        $row = 2;
        foreach ($asignedDrugs as $asignedDrug) {

            $dateObj = $asignedDrug->getDate();
            $dateStr = ($dateObj instanceof \DateTimeInterface) ? $dateObj->format('Y-m-d') : 'Unknown';

            $drugWarehouse = $asignedDrug->getDrugWarehouse();
            $drugName = ($drugWarehouse instanceof DrugWarehouse) ? $drugWarehouse->getDrugName() : 'Unknown';

            $amount = $asignedDrug->getAmount();

            $appointment = $asignedDrug->getAppointment();

            $patientName = 'Unknown';
            if ($appointment instanceof Appointment && $appointment->getPatient()) {
                $patientName = $appointment->getPatient()->getName();
            }

            $clientName = 'Unknown';
            if ($appointment instanceof Appointment && $appointment->getClient()) {
                $clientName = $appointment->getClient()->getName();
            }

            $sheet->fromArray([
                $dateStr,
                $drugName,
                $amount,
                $patientName,
                $clientName,
            ], null, "A$row");

            ++$row;
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save($filename);

        $io->success("Report exported to Excel file: $filename");
    }
}
