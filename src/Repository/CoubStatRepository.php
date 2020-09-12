<?php

namespace App\Repository;

use App\Entity\CoubStat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CoubStat|null find($id, $lockMode = null, $lockVersion = null)
 * @method CoubStat|null findOneBy(array $criteria, array $orderBy = null)
 * @method CoubStat[]    findAll()
 * @method CoubStat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CoubStatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CoubStat::class);
    }

    // /**
    //  * @return CoubStat[] Returns an array of CoubStat objects
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
    public function findOneBySomeField($value): ?CoubStat
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
