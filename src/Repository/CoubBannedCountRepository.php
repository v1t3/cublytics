<?php

namespace App\Repository;

use App\Entity\CoubBannedCount;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CoubBannedCount|null find($id, $lockMode = null, $lockVersion = null)
 * @method CoubBannedCount|null findOneBy(array $criteria, array $orderBy = null)
 * @method CoubBannedCount[]    findAll()
 * @method CoubBannedCount[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CoubBannedCountRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CoubBannedCount::class);
    }

    // /**
    //  * @return CoubBannedCount[] Returns an array of CoubBannedCount objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CoubBannedCount
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
