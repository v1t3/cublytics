<?php

namespace App\Repository;

use App\Entity\CoubRepostCount;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CoubRepostCount|null find($id, $lockMode = null, $lockVersion = null)
 * @method CoubRepostCount|null findOneBy(array $criteria, array $orderBy = null)
 * @method CoubRepostCount[]    findAll()
 * @method CoubRepostCount[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CoubRepostCountRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CoubRepostCount::class);
    }

    // /**
    //  * @return CoubRepostCount[] Returns an array of CoubRepostCount objects
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
    public function findOneBySomeField($value): ?CoubRepostCount
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
