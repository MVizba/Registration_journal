<?php

namespace App\Event;

use App\Entity\AsignedDrugs;
use Symfony\Contracts\EventDispatcher\Event;

class DrugAssignedEvent extends Event
{
    public const NAME = 'drug.assigned';

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
