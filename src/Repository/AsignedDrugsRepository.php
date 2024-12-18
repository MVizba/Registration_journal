<?php

namespace App\Repository;

use App\Entity\AsignedDrugs;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AsignedDrugs>
 */
class AsignedDrugsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AsignedDrugs::class);
    }

    /**
     * Find drugs assigned within a date range.
     *
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @return AsignedDrugs[] An array of AsignedDrugs objects
     */
    public function findByDateRange(\DateTime $startDate, \DateTime $endDate): array
    {
        /** @var AsignedDrugs[] $result */
        $result = $this->createQueryBuilder('ad')
            ->andWhere('ad.date >= :start_date')
            ->andWhere('ad.date <= :end_date')
            ->setParameter('start_date', $startDate)
            ->setParameter('end_date', $endDate)
            ->orderBy('ad.date', 'ASC')
            ->getQuery()
            ->getResult();

        return $result;
    }

}
