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
        $drugName = $asignedDrug->getDrugWarehouse()->getDrugName();
        $assignedAmount = $asignedDrug->getAmount();

        $drugWarehouse = $this->entityManager->getRepository(DrugWarehouse::class)->findOneBy(['drugName' => $drugName]);

        if ($drugWarehouse) {
            $currentAmount = $drugWarehouse->getAmount();
            $newAmount = $currentAmount + $assignedAmount;
            $drugWarehouse->setAmount($newAmount);

            $this->entityManager->flush();
        }
    }
}
