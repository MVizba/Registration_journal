<?php

namespace App\EventSubscriber;

use App\Entity\DrugWarehouse;
use App\Event\DrugRemovedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DrugRemovedSubscriber implements EventSubscriberInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            DrugRemovedEvent::NAME => 'onDrugRemoved',
        ];
    }

    public function onDrugRemoved(DrugRemovedEvent $event): void
    {
        $asignedDrug = $event->getAsignedDrug();
        $drugWarehouse = $asignedDrug->getDrugWarehouse();

        if (null === $drugWarehouse) {
            return;
        }

        $removedAmount = $asignedDrug->getAmount();
        $currentUsedAmount = $drugWarehouse->getUsedAmount();

        // Subtract the removed amount from used amount
        $newUsedAmount = max(0, $currentUsedAmount - $removedAmount);
        $drugWarehouse->setUsedAmount($newUsedAmount);

        $this->entityManager->flush();
    }
}
