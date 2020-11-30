<?php

namespace App\Repository;

use App\Entity\CoubViewsCount;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CoubViewsCount|null find($id, $lockMode = null, $lockVersion = null)
 * @method CoubViewsCount|null findOneBy(array $criteria, array $orderBy = null)
 * @method CoubViewsCount[]    findAll()
 * @method CoubViewsCount[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CoubViewsCountRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CoubViewsCount::class);
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

    public function findByPeriodCoub($coubId, \DateTime $dateStart, \DateTime $dateEnd)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.coub_id = :id')
            ->andWhere('c.date_create >= :date_start')
            ->andWhere('c.date_create <= :date_end')
            ->setParameter('id', $coubId)
            ->setParameter('date_start', $dateStart)
            ->setParameter('date_end', $dateEnd)
            ->orderBy('c.date_create', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    // /**
    //  * @return CoubViewsCount[] Returns an array of CoubViewsCount objects
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
    public function findOneBySomeField($value): ?CoubViewsCount
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
