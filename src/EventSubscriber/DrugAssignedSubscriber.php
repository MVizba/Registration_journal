<?php

namespace App\EventSubscriber;

use App\Entity\DrugWarehouse;
use App\Event\DrugAssignedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DrugAssignedSubscriber implements EventSubscriberInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            DrugAssignedEvent::NAME => 'onDrugAssigned',
        ];
    }

    public function onDrugAssigned(DrugAssignedEvent $event): void
    {
        $asignedDrug = $event->getAsignedDrug();
        $drugWarehouse = $asignedDrug->getDrugWarehouse();

        if (null === $drugWarehouse) {
            return;
        }

        $assignedAmount = $asignedDrug->getAmount();
        $currentUsedAmount = $drugWarehouse->getUsedAmount();
        $totalAmount = $drugWarehouse->getAmount();

        // Check if there's enough stock
        if ($currentUsedAmount + $assignedAmount > $totalAmount) {
            throw new \RuntimeException('Not enough stock available.');
        }

        // Update the used amount
        $drugWarehouse->addUsedAmount($assignedAmount);
        $this->entityManager->flush();
    }
}
