<?php

namespace App\Event;

use App\Entity\AsignedDrugs;
use Symfony\Contracts\EventDispatcher\Event;

class DrugRemovedEvent extends Event
{
    public const NAME = 'drug.removed';

    private AsignedDrugs $asignedDrug;

    public function __construct(AsignedDrugs $asignedDrug)
    {
        $this->asignedDrug = $asignedDrug;
    }

    public function getAsignedDrug(): AsignedDrugs
    {
        return $this->asignedDrug;
    }
}