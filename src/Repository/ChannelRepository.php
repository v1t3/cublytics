<?php

namespace App\Repository;

use App\Entity\Channel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Channel|null find($id, $lockMode = null, $lockVersion = null)
 * @method Channel|null findOneBy(array $criteria, array $orderBy = null)
 * @method Channel[]    findAll()
 * @method Channel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChannelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Channel::class);
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

    public function findOneByUserId($value): ?Channel
    {
        try {
            return $this->createQueryBuilder('c')
                ->andWhere('c.user_id = :val')
                ->setParameter('val', $value)
                ->getQuery()
                ->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            trigger_error($e);
        }
    }

    public function findOneByChannelId($value): ?Channel
    {
        try {
            return $this->createQueryBuilder('c')
                ->andWhere('c.channel_id = :val')
                ->setParameter('val', $value)
                ->getQuery()
                ->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            trigger_error($e);
        }
    }

    public function findOneByPermalink($value)
    {
        try {
            return $this->createQueryBuilder('c')
                ->andWhere('c.channel_permalink = :val')
                ->setParameter('val', $value)
                ->getQuery()
                ->getArrayResult();
        } catch (NonUniqueResultException $e) {
            trigger_error($e);
        }
    }
}
