<?php

namespace App\Entity;

use App\Repository\AsignedDrugsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AsignedDrugsRepository::class)]
class AsignedDrugs
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    // @phpstan-ignore-next-line
    private ?int $id = null;

    #[ORM\Column(type: 'date')]
    private ?\DateTimeInterface $date = null;

    #[ORM\ManyToOne(targetEntity: DrugWarehouse::class, inversedBy: 'asignedDrugs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?DrugWarehouse $drugWarehouse = null;


    #[ORM\Column]
    private ?float $amount = null;

    #[ORM\ManyToOne(inversedBy: 'asignedDrugs')]
    private ?Appointment $appointment = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getDrugWarehouse(): ?DrugWarehouse
    {
        return $this->drugWarehouse;
    }

    public function setDrugWarehouse(?DrugWarehouse $drugWarehouse): static
    {
        $this->drugWarehouse = $drugWarehouse;

        return $this;
    }


    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getAppointment(): ?Appointment
    {
        return $this->appointment;
    }

    public function setAppointment(?Appointment $appointment): static
    {
        $this->appointment = $appointment;

        return $this;
    }
}
