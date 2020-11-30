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

    public function findByPeriodChannel($channelId, \DateTime $dateStart, \DateTime $dateEnd)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.channel_id = :id')
            ->andWhere('c.date_create >= :date_start')
            ->andWhere('c.date_create <= :date_end')
            ->setParameter('id', $channelId)
            ->setParameter('date_start', $dateStart)
            ->setParameter('date_end', $dateEnd)
            ->orderBy('c.date_create', 'ASC')
            ->getQuery()
            ->getResult()
            ;
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
