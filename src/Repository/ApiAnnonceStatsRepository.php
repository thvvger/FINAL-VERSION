<?php

namespace App\Repository;

use App\Entity\ApiAnnonceStats;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ApiAnnonceStats|null find($id, $lockMode = null, $lockVersion = null)
 * @method ApiAnnonceStats|null findOneBy(array $criteria, array $orderBy = null)
 * @method ApiAnnonceStats[]    findAll()
 * @method ApiAnnonceStats[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ApiAnnonceStatsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ApiAnnonceStats::class);
    }

    // /**
    //  * @return ApiAnnonceStats[] Returns an array of ApiAnnonceStats objects
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
    public function findOneBySomeField($value): ?ApiAnnonceStats
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
