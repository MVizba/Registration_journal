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

        $drugName = $drugWarehouse->getDrugName();
        $assignedAmount = $asignedDrug->getAmount();

        $drugWarehouseEntity = $this->entityManager->getRepository(DrugWarehouse::class)
            ->findOneBy(['drugName' => $drugName]);

        if (null === $drugWarehouseEntity) {
            return;
        }

        $currentAmount = $drugWarehouseEntity->getAmount();
        $newAmount = $currentAmount - $assignedAmount;
        $drugWarehouseEntity->setAmount($newAmount);

        $this->entityManager->flush();
    }
}
