<?php

namespace App\Controller;

use App\Repository\AppointmentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DashboardController extends AbstractController
{
    #[Route('/', name: 'app_dashboard')]
    #[IsGranted('ROLE_USER')]
    public function index(AppointmentRepository $appointmentRepository)
    {
        $appointments = $appointmentRepository->findAll();

        return $this->render('dashboard/index.html.twig', [
            'appointments' => $appointments,
        ]);
    }
}
