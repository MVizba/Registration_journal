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

    //    /**
    //     * @return AsignedDrugs[] Returns an array of AsignedDrugs objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?AsignedDrugs
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    /**
     * Find drugs assigned within a date range.
     *
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @return AsignedDrugs[]
     */
    public function findByDateRange(\DateTime $startDate, \DateTime $endDate)
    {
        return $this->createQueryBuilder('ad')
            ->andWhere('ad.date >= :start_date')
            ->andWhere('ad.date <= :end_date')
            ->setParameter('start_date', $startDate)
            ->setParameter('end_date', $endDate)
            ->orderBy('ad.date', 'ASC')
            ->getQuery()
            ->getResult();
    }

}
