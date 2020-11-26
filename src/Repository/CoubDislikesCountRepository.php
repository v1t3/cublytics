<?php

namespace App\Repository;

use App\Entity\CoubDislikesCount;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CoubDislikesCount|null find($id, $lockMode = null, $lockVersion = null)
 * @method CoubDislikesCount|null findOneBy(array $criteria, array $orderBy = null)
 * @method CoubDislikesCount[]    findAll()
 * @method CoubDislikesCount[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CoubDislikesCountRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CoubDislikesCount::class);
    }

    // /**
    //  * @return CoubDislikesCount[] Returns an array of CoubDislikesCount objects
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
    public function findOneBySomeField($value): ?CoubDislikesCount
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
