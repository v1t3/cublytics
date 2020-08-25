<?php

namespace App\Repository;

use App\Entity\CoubChannel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CoubChannel|null find($id, $lockMode = null, $lockVersion = null)
 * @method CoubChannel|null findOneBy(array $criteria, array $orderBy = null)
 * @method CoubChannel[]    findAll()
 * @method CoubChannel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CoubChannelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CoubChannel::class);
    }

    // /**
    //  * @return CoubChannel[] Returns an array of CoubChannel objects
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
    public function findOneBySomeField($value): ?CoubChannel
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
