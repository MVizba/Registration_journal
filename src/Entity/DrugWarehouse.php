<?php

namespace App\Entity;

use App\Repository\DrugWarehouseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DrugWarehouseRepository::class)]
class DrugWarehouse
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'date')]
    private ?\DateTimeInterface $dateOfReceipt = null;

    #[ORM\Column(length: 255)]
    private ?string $drugName = null; // Changed to camelCase

    #[ORM\Column(length: 255)]
    private ?string $drugManufacturer = null;

    #[ORM\Column(length: 255)]
    private ?string $documentNumber = null;

    #[ORM\Column]
    private ?int $amount = null;

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $manufactureDate = null;

    #[ORM\Column(type: 'date')]
    private ?\DateTimeInterface $expirationDate = null;

    #[ORM\Column(length: 255)]
    private ?string $series = null;

    #[ORM\Column(length: 255)]
    private ?string $whereObtainedFrom = null;

    // One-to-many relationship with AsignedDrugs
    #[ORM\OneToMany(mappedBy: 'drugWarehouse', targetEntity: AsignedDrugs::class)]
    private Collection $asignedDrugs;

    public function __construct()
    {
        $this->asignedDrugs = new ArrayCollection();
    }

    // Getters and setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateOfReceipt(): ?\DateTimeInterface
    {
        return $this->dateOfReceipt;
    }

    public function setDateOfReceipt(\DateTimeInterface $dateOfReceipt): static
    {
        $this->dateOfReceipt = $dateOfReceipt;

        return $this;
    }

    public function getDrugName(): ?string
    {
        return $this->drugName;
    }

    public function setDrugName(string $drugName): static
    {
        $this->drugName = $drugName;

        return $this;
    }

    public function getDrugManufacturer(): ?string
    {
        return $this->drugManufacturer;
    }

    public function setDrugManufacturer(string $drugManufacturer): static
    {
        $this->drugManufacturer = $drugManufacturer;

        return $this;
    }

    public function getDocumentNumber(): ?string
    {
        return $this->documentNumber;
    }

    public function setDocumentNumber(string $documentNumber): static
    {
        $this->documentNumber = $documentNumber;

        return $this;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getManufactureDate(): ?\DateTimeInterface
    {
        return $this->manufactureDate;
    }

    public function setManufactureDate(?\DateTimeInterface $manufactureDate): static
    {
        $this->manufactureDate = $manufactureDate;

        return $this;
    }

    public function getExpirationDate(): ?\DateTimeInterface
    {
        return $this->expirationDate;
    }

    public function setExpirationDate(\DateTimeInterface $expirationDate): static
    {
        $this->expirationDate = $expirationDate;

        return $this;
    }

    public function getSeries(): ?string
    {
        return $this->series;
    }

    public function setSeries(string $series): static
    {
        $this->series = $series;

        return $this;
    }

    public function getWhereObtainedFrom(): ?string
    {
        return $this->whereObtainedFrom;
    }

    public function setWhereObtainedFrom(string $whereObtainedFrom): static
    {
        $this->whereObtainedFrom = $whereObtainedFrom;

        return $this;
    }

    public function getAsignedDrugs(): Collection
    {
        return $this->asignedDrugs;
    }

    public function addAsignedDrug(AsignedDrugs $asignedDrug): static
    {
        if (!$this->asignedDrugs->contains($asignedDrug)) {
            $this->asignedDrugs[] = $asignedDrug;
            $asignedDrug->setDrugWarehouse($this);
        }

        return $this;
    }

    public function removeAsignedDrug(AsignedDrugs $asignedDrug): static
    {
        if ($this->asignedDrugs->removeElement($asignedDrug)) {
            // Set the owning side to null (unless already changed)
            if ($asignedDrug->getDrugWarehouse() === $this) {
                $asignedDrug->setDrugWarehouse(null);
            }
        }

        return $this;
    }
}
