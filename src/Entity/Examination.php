<?php

namespace App\Entity;

use App\Repository\ExaminationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ExaminationRepository::class)]
class Examination
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $shortcut = null;

    #[ORM\Column(length: 200)]
    private ?string $examinationName = null;

    #[ORM\Column(length: 200, nullable: true)]
    private ?string $norms = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $machine = null;

    /**
     * @var Collection<int, ExaminationWithResults>
     */
    #[ORM\OneToMany(targetEntity: ExaminationWithResults::class, mappedBy: 'examination', orphanRemoval: true)]
    private Collection $examinationWithResults;

    public function __construct()
    {
        $this->examinationWithResults = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getShortcut(): ?string
    {
        return $this->shortcut;
    }

    public function setShortcut(string $shortcut): static
    {
        $this->shortcut = $shortcut;

        return $this;
    }

    public function getExaminationName(): ?string
    {
        return $this->examinationName;
    }

    public function setExaminationName(string $examinationName): static
    {
        $this->examinationName = $examinationName;

        return $this;
    }

    public function getNorms(): ?string
    {
        return $this->norms;
    }

    public function setNorms(?string $norms): static
    {
        $this->norms = $norms;

        return $this;
    }

    public function getMachine(): ?string
    {
        return $this->machine;
    }

    public function setMachine(?string $machine): static
    {
        $this->machine = $machine;

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
            $examinationWithResult->setExamination($this);
        }

        return $this;
    }

    public function removeExaminationWithResult(ExaminationWithResults $examinationWithResult): static
    {
        if ($this->examinationWithResults->removeElement($examinationWithResult)) {
            // set the owning side to null (unless already changed)
            if ($examinationWithResult->getExamination() === $this) {
                $examinationWithResult->setExamination(null);
            }
        }

        return $this;
    }
}
