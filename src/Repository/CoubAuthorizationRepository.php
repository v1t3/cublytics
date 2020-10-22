<?php

namespace App\Repository;

use App\Entity\CoubAuthorization;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CoubAuthorization|null find($id, $lockMode = null, $lockVersion = null)
 * @method CoubAuthorization|null findOneBy(array $criteria, array $orderBy = null)
 * @method CoubAuthorization[]    findAll()
 * @method CoubAuthorization[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CoubAuthorizationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CoubAuthorization::class);
    }

    // /**
    //  * @return CoubAuthorization[] Returns an array of CoubAuthorization objects
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
    public function findOneBySomeField($value): ?CoubAuthorization
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
