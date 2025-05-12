<?php

namespace App\Service;

use App\Entity\Patient;
use App\Entity\Appointment;

class PatientAppointmentsManager
{
    public function addAppointment(Patient $patient, Appointment $appointment): void
    {
        if (!$patient->getAppointments()->contains($appointment)) {
            $patient->getAppointments()->add($appointment);
            $appointment->setPatient($patient);
        }
    }

    public function removeAppointment(Patient $patient, Appointment $appointment): void
    {
        if ($patient->getAppointments()->removeElement($appointment)) {
            if ($appointment->getPatient() === $patient) {
                $appointment->setPatient(null);
            }
        }
    }
}
