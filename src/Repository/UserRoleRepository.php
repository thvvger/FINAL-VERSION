<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\UserRole;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserRole|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserRole|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserRole[]    findAll()
 * @method UserRole[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRoleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserRole::class);
    }

    // /**
    //  * @return UserRole[] Returns an array of UserRole objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UserRole
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function findUserRole(User $user)
    {
        $qb = $this->createQueryBuilder('userRole')
            ->innerJoin('userRole.role', 'role')
            ->addSelect()
            ->where('role.deleted = :false')
            ->andWhere('userRole.deleted = :false')
            ->andWhere('userRole.user = :user')

            ->setParameter('false', false)
            ->setParameter('user', $user)
            ;

        return $qb->getQuery()->getResult();

    }

//    public function getUserPermissions(User $user)
//    {
//        $qb = $this->createQueryBuilder('userRole')
//            ->innerJoin('userRole.role', 'role')
//            ->addSelect()
//            ->innerJoin('')
//    }
}
