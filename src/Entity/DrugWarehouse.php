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
    // @phpstan-ignore-next-line
    private ?int $id = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $dateOfReceipt = null;

    #[ORM\Column(length: 255)]
    private ?string $drugName = null;

    #[ORM\Column(length: 255)]
    private ?string $drugManufacturer = null;

    #[ORM\Column(length: 255)]
    private ?string $documentNumber = null;

    #[ORM\Column]
    private ?float $amount = null;

    #[ORM\Column]
    private ?float $usedAmount = 0.0;

    #[ORM\Column(length: 100)]
    private ?string $type = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $manufactureDate = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $expirationDate = null;

    #[ORM\Column(length: 255)]
    private ?string $series = null;

    #[ORM\Column(length: 255)]
    private ?string $whereObtainedFrom = null;


    /**
     * @var Collection<int, AsignedDrugs>
     */
    #[ORM\OneToMany(targetEntity: AsignedDrugs::class, mappedBy: 'drugWarehouse')]
    private Collection $asignedDrugs;

    public function __construct()
    {
        $this->asignedDrugs = new ArrayCollection();
        $this->usedAmount = 0.0;
    }


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

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getUsedAmount(): ?float
    {
        return $this->usedAmount;
    }

    public function setUsedAmount(float $usedAmount): static
    {
        $this->usedAmount = $usedAmount;

        return $this;
    }

    public function addUsedAmount(float $amount): static
    {
        $this->usedAmount += $amount;

        return $this;
    }

    public function getRemainingAmount(): float
    {
        return ($this->amount ?? 0.0) - ($this->usedAmount ?? 0.0);
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

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
