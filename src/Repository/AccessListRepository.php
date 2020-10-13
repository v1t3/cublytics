<?php

namespace App\Repository;

use App\Entity\AccessList;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AccessList|null find($id, $lockMode = null, $lockVersion = null)
 * @method AccessList|null findOneBy(array $criteria, array $orderBy = null)
 * @method AccessList[]    findAll()
 * @method AccessList[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AccessListRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AccessList::class);
    }

    // /**
    //  * @return AccessList[] Returns an array of AccessList objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?AccessList
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
