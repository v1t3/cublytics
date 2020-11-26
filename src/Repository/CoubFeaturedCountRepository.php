<?php

namespace App\Repository;

use App\Entity\CoubFeaturedCount;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CoubFeaturedCount|null find($id, $lockMode = null, $lockVersion = null)
 * @method CoubFeaturedCount|null findOneBy(array $criteria, array $orderBy = null)
 * @method CoubFeaturedCount[]    findAll()
 * @method CoubFeaturedCount[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CoubFeaturedCountRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CoubFeaturedCount::class);
    }

    // /**
    //  * @return CoubFeaturedCount[] Returns an array of CoubFeaturedCount objects
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
    public function findOneBySomeField($value): ?CoubFeaturedCount
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
