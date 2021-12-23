<?php
/**
 * Created by PhpStorm.
 * User: LANGANFIN  Rogelio
 * Date: 28/04/2021
 * Time: 10:02
 */

namespace App\Service;


use App\Entity\Permission;
use App\Entity\RolePermission;
use App\Entity\User;
use App\Entity\UserPermission;
use App\Entity\UserRole;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class UserService
{

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var Security
     */
    private $security;

    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    public function setNewOrganisationAdmin(User $user)
    {

        try {
            $organisation = $user->getOrganisation();
            $organisationUsers = $organisation->getuser()->getValues();


            foreach ($organisationUsers as $organisationUser) {
                $organisationUser->setIsOrganisationAdmin(false);
            }

            $user->setIsOrganisationAdmin(true);

            return "ok";
        } catch (\Exception $exception) {

        }

//        $this->entityManager->persist($notif);

    }

    public function checkIfUserHasPermission($permission, $table)
    {
        /**
         * @var $user User
         */
        $user = $this->security->getUser();

        $userPermissions = $this->getUserPermissions($user);

        $arrayFilter = array_filter($userPermissions, function (RolePermission $element) use ($permission, $table) {
            return $element->getPermission()->getPermission() == $permission . '_' . $table;
        });

        return !empty($arrayFilter);
    }

    public function getUserPermissions(User $user)
    {
        $permissions = [];

        $rolePermissions = $this->entityManager->getRepository(RolePermission::class)->findBy([
            'role' => $user,
            'deleted' => false,
        ]);

        foreach ($rolePermissions as $permission) {
            if ($permission != null) {
                $permissions[$permission->getId()] = $permission;
            }
        }
        return $permissions;
    }


}