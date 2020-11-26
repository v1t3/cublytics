<?php

namespace App\Repository;

use App\Entity\CoubKdCount;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CoubKdCount|null find($id, $lockMode = null, $lockVersion = null)
 * @method CoubKdCount|null findOneBy(array $criteria, array $orderBy = null)
 * @method CoubKdCount[]    findAll()
 * @method CoubKdCount[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CoubKdCountRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CoubKdCount::class);
    }

    // /**
    //  * @return CoubKdCount[] Returns an array of CoubKdCount objects
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
    public function findOneBySomeField($value): ?CoubKdCount
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
