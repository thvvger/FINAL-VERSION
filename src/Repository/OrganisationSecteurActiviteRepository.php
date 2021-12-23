<?php

namespace App\Repository;

use App\Entity\OrganisationSecteurActivite;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method OrganisationSecteurActivite|null find($id, $lockMode = null, $lockVersion = null)
 * @method OrganisationSecteurActivite|null findOneBy(array $criteria, array $orderBy = null)
 * @method OrganisationSecteurActivite[]    findAll()
 * @method OrganisationSecteurActivite[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrganisationSecteurActiviteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrganisationSecteurActivite::class);
    }

    // /**
    //  * @return OrganisationSecteurActivite[] Returns an array of OrganisationSecteurActivite objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?OrganisationSecteurActivite
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
