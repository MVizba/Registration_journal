<?php

namespace App\Entity;

use App\Repository\AppointmentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AppointmentRepository::class)]
class Appointment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    // @phpstan-ignore-next-line
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\ManyToOne(inversedBy: 'appointments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Client $client = null;

    #[ORM\ManyToOne(inversedBy: 'appointments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Patient $patient = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $registrationDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $symptomsDate = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $status = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $diagnosis = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $services = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $endResult = null;

    /**
     * @var Collection<int, ExaminationWithResults>
     */
    #[ORM\OneToMany(targetEntity: ExaminationWithResults::class, mappedBy: 'appointment', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $examinationWithResults;

    /**
     * @var Collection<int, AsignedDrugs>
     */
    #[ORM\OneToMany(targetEntity: AsignedDrugs::class, mappedBy: 'appointment', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $asignedDrugs;

    public function __construct()
    {
        $this->examinationWithResults = new ArrayCollection();
        $this->asignedDrugs = new ArrayCollection();
    }

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

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): static
    {
        $this->client = $client;

        return $this;
    }

    public function getPatient(): ?Patient
    {
        return $this->patient;
    }

    public function setPatient(?Patient $patient): static
    {
        $this->patient = $patient;

        return $this;
    }

    public function getRegistrationDate(): ?\DateTimeInterface
    {
        return $this->registrationDate;
    }

    public function setRegistrationDate(\DateTimeInterface $registrationDate): static
    {
        $this->registrationDate = $registrationDate;

        return $this;
    }

    public function getSymptomsDate(): ?\DateTimeInterface
    {
        return $this->symptomsDate;
    }

    public function setSymptomsDate(\DateTimeInterface $symptomsDate): static
    {
        $this->symptomsDate = $symptomsDate;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getDiagnosis(): ?string
    {
        return $this->diagnosis;
    }

    public function setDiagnosis(string $diagnosis): static
    {
        $this->diagnosis = $diagnosis;

        return $this;
    }

    public function getServices(): ?string
    {
        return $this->services;
    }

    public function setServices(?string $services): static
    {
        $this->services = $services;

        return $this;
    }

    public function getEndResult(): ?string
    {
        return $this->endResult;
    }

    public function setEndResult(?string $endResult): static
    {
        $this->endResult = $endResult;

        return $this;
    }

    /**
     * @return Collection<int, ExaminationWithResults>
     */
    public function getExaminationWithResults(): Collection
    {
        return $this->examinationWithResults;
    }

    public function addExaminationWithResult(ExaminationWithResults $examinationWithResult): static
    {
        if (!$this->examinationWithResults->contains($examinationWithResult)) {
            $this->examinationWithResults->add($examinationWithResult);
            $examinationWithResult->setAppointment($this);
        }

        return $this;
    }

    public function removeExaminationWithResult(ExaminationWithResults $examinationWithResult): static
    {
        if ($this->examinationWithResults->removeElement($examinationWithResult)) {
            // set the owning side to null (unless already changed)
            if ($examinationWithResult->getAppointment() === $this) {
                $examinationWithResult->setAppointment(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, AsignedDrugs>
     */
    public function getAsignedDrugs(): Collection
    {
        return $this->asignedDrugs;
    }

    public function addAsignedDrug(AsignedDrugs $asignedDrug): static
    {
        if (!$this->asignedDrugs->contains($asignedDrug)) {
            $this->asignedDrugs->add($asignedDrug);
            $asignedDrug->setAppointment($this);
        }

        return $this;
    }

    public function removeAsignedDrug(AsignedDrugs $asignedDrug): static
    {
        if ($this->asignedDrugs->removeElement($asignedDrug)) {
            // set the owning side to null (unless already changed)
            if ($asignedDrug->getAppointment() === $this) {
                $asignedDrug->setAppointment(null);
            }
        }

        return $this;
    }
}
