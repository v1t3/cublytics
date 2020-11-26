<?php

namespace App\Repository;

use App\Entity\CoubRemixesCount;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CoubRemixesCount|null find($id, $lockMode = null, $lockVersion = null)
 * @method CoubRemixesCount|null findOneBy(array $criteria, array $orderBy = null)
 * @method CoubRemixesCount[]    findAll()
 * @method CoubRemixesCount[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CoubRemixesCountRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CoubRemixesCount::class);
    }

    // /**
    //  * @return CoubRemixesCount[] Returns an array of CoubRemixesCount objects
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
    public function findOneBySomeField($value): ?CoubRemixesCount
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
