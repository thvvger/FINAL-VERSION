<?php
/**
 * Created by PhpStorm.
 * User: OSSPO DEV
 * Date: 20/07/2020
 * Time: 14:52
 */

namespace App\Service;


use App\Entity\Offre;
use App\Entity\Permission;
use App\Entity\RolePermission;
use App\Entity\User;
use App\Entity\UserPermission;
use App\Entity\UserRole;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Security;

class UserServices
{

    private $em;
    private $security;
    private $router;

    public function __construct(EntityManagerInterface $entityManager, Security $security, RouterInterface $router)
    {
        $this->em = $entityManager;
        $this->security = $security;
        $this->router = $router;
    }

    public function getUserPermissions(User $user)
    {
        $permissions = [];

//        dump($user->getUserRoles()->getValues());die;

        $userPermissions = $this->em->getRepository(UserPermission::class)->findBy([
            'user' => $user,
            'deleted' => false,
        ]);
//        $userRoles = $user->getUserRoles()->getValues();
        $userRoles = $this->em->getRepository(UserRole::class)->findUserRole($user);
        //dump($userRoles); die();

        foreach ($userPermissions as $userPermission)
        {
            if ($userPermission->getPermission() != null)
            {
                $permissions[$userPermission->getPermission()->getId()] = $userPermission->getPermission();
            }
        }

        foreach ($userRoles as $userRole)
        {
                /**
                 * @var $userRole UserRole
                 */
            if ($userRole->getRole() != null)
            {
                $permissionsDuRole = $this->em->getRepository(RolePermission::class)->findBy([
                    'role' => $userRole->getRole()
                ]);

                //dump($userRole->getRole()->getName(),$user);die;

//                $permissionsDuRole = $userRole->getRole()->getRolePermissions()->getValues();

                foreach ($permissionsDuRole as $permissionDuRole)
                {
                    if ($permissionDuRole->getPermission() != null)
                    {
                        $permissions[$permissionDuRole->getPermission()->getId()] = $permissionDuRole->getPermission();
                    }
                }
            }else{}
        }

//        $permissions[3] = $this->em->getRepository(Permission::class)->find(3);
        //dump($permissions);die();

        return $permissions;
    }



    public function checkIfUserHasPermission($permission, $table)
    {
        /**
         * @var $user User
         */
        $user = $this->security->getUser();

        $userPermissions = $this->getUserPermissions($user);
        $arrayFilter = array_filter($userPermissions, function (Permission $element) use ($permission, $table){
            return $element->getPermission() == $permission . '_' .$table;
        });

        return !empty($arrayFilter);

    }


}