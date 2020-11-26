<?php

namespace App\Repository;

use App\Entity\CoubLikeCount;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CoubLikeCount|null find($id, $lockMode = null, $lockVersion = null)
 * @method CoubLikeCount|null findOneBy(array $criteria, array $orderBy = null)
 * @method CoubLikeCount[]    findAll()
 * @method CoubLikeCount[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CoubLikeCountRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CoubLikeCount::class);
    }

    // /**
    //  * @return CoubLikeCount[] Returns an array of CoubLikeCount objects
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
    public function findOneBySomeField($value): ?CoubLikeCount
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
