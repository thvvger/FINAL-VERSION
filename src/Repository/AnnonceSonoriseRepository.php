<?php

namespace App\Repository;

use App\Entity\AnnonceSonorise;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AnnonceSonorise|null find($id, $lockMode = null, $lockVersion = null)
 * @method AnnonceSonorise|null findOneBy(array $criteria, array $orderBy = null)
 * @method AnnonceSonorise[]    findAll()
 * @method AnnonceSonorise[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnnonceSonoriseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AnnonceSonorise::class);
    }

    // /**
    //  * @return AnnonceSonorise[] Returns an array of AnnonceSonorise objects
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

    public function getLatest($organisation)
    {
        return $this->createQueryBuilder('a')
            ->innerJoin("a.annonce","aa")
            ->andWhere('aa.organisation = :val')
            ->setParameter('val', $organisation)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult()
            ;
    }
    public function getAllSonorise($organisation)
    {
        return $this->createQueryBuilder('a')
            ->join("a.annonce","annonce")
            ->addSelect('annonce')
            ->andWhere('annonce.organisation = :val')
            ->setParameter('val', $organisation)
            ->orderBy('a.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }
    public function getLatestValide($organisation)
    {
        return $this->createQueryBuilder('a')
            ->innerJoin("a.annonce","aa")
            ->andWhere('aa.organisation = :val')
            ->andWhere('aa.estValider = 1')
            ->setParameter('val', $organisation)
            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(5)
            ->getQuery()
            ->getResult()
            ;
    }
    public function getRejete($organisation)
    {
        return $this->createQueryBuilder('a')
            ->innerJoin("a.annonce","aa")
            ->andWhere('aa.organisation = :val')
            ->andWhere('aa.estValider = 0')
            ->setParameter('val', $organisation)
            ->orderBy('a.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function getLatestRejete($organisation)
    {
        return $this->createQueryBuilder('a')
            ->innerJoin("a.annonce","aa")
            ->andWhere('aa.organisation = :val')
            ->andWhere('aa.estValider = 0')
            ->setParameter('val', $organisation)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult()
            ;
    }

    public function findOneBySomeField()
    {
        try {
            return $this->createQueryBuilder('a')
                ->select("COUNT(a.id)" ,"u.nom","u.prenom")
                ->join("App\Entity\User","u")
                ->addGroupBy('a.user')
                ->getQuery()
                ->getResult();
        } catch (NonUniqueResultException $e) {
        }
    }
}
