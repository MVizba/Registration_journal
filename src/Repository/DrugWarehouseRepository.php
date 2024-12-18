<?php

namespace App\Repository;

use App\Entity\DrugWarehouse;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DrugWarehouse>
 */
class DrugWarehouseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DrugWarehouse::class);
    }

    /**
     * @return DrugWarehouse[] An array of DrugWarehouse objects
     */
    public function findDrugsWithAvailableStock(): array
    {
        /** @var DrugWarehouse[] $result */
        $result = $this->createQueryBuilder('d')
            ->where('d.amount > 0')
            ->getQuery()
            ->getResult();

        return $result;
    }
}
