<?php

namespace App\Entity;

use App\Repository\DrugWarehouseRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

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
    private ?string $DrugName = null;

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

    public function getDrugName(): ?string
    {
        return $this->DrugName;
    }

    public function setDrugName(string $DrugName): static
    {
        $this->DrugName = $DrugName;

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

    public function getAsignedDrugs(): Collection
    {
        return $this->asignedDrugs;
    }
}
