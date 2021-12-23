<?php

namespace App\Controller\AjaxController;


use App\Entity\Permission;
use App\Entity\Role;
use App\Entity\RolePermission;
use App\Entity\User;
use App\Entity\UserPermission;
use App\Entity\UserRole;
use App\Repository\PermissionRepository;
use App\Repository\RolePermissionRepository;
use App\Repository\RoleRepository;
use App\Service\UserServices;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserAjaxController extends AbstractController
{

    private $userServices;


    public function __construct(UserServices $userServices)
    {
        $this->userServices = $userServices;
    }


    /**
     * @Route("/ajax/get/user/roles", name="ajax_get_user_roles", options={"expose"=true})
     */
    public function getUserRoles(Request $request)
    {

        $userRoles = $roles = [];
        $userId = "";
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();

            $userId = trim($request->get('id'));

            $user = $em->getRepository(User::class)->find($userId);

            $userRoles = $em->getRepository(UserRole::class)->findUserRole($user);

            $allRoles = $em->getRepository(Role::class)->findBy([
                'deleted' => false
            ]);
            foreach ($allRoles as $role) {
                $userHasRole = $em->getRepository(UserRole::class)->findOneBy([
                    'user' => $user,
                    'role' => $role,
                    'deleted' => false,
                ]);
                if ($userHasRole == null) {
                    $roles[] = $role;
                }
            }
        }

        return $this->render('admin/user/ajax/user_role_ajax.html.twig', [
            'userId' => $userId,
            'userRoles' => $userRoles,
            'roles' => $roles,
        ]);
    }


    /**
     * @Route("/ajax/get/user/permissions", name="ajax_get_user_permissions", options={"expose"=true})
     */
    public function getUserPermissions(Request $request)
    {

        $userPermissions = $permissions = [];
        $userId = "";
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();

            $userId = trim($request->get('id'));

            $user = $em->getRepository(User::class)->find($userId);

            $userPermissions = $em->getRepository(UserPermission::class)->findBy([
                'user' => $user,
                'deleted' => false,
            ]);

            $allPermissions = $em->getRepository(Permission::class)->findAll();
            foreach ($allPermissions as $permission) {
                $userPermissionExiste = $em->getRepository(UserPermission::class)->findBy([
                    'user' => $user,
                    'deleted' => false,
                    'permission' => $permission
                ]);

                if ($userPermissionExiste == null) {
                    $permissions[] = $permission;
                }

            }
        }


        return $this->render('admin/user/ajax/user_permission_ajax.html.twig', [
            'userId' => $userId,
            'userPermissions' => $userPermissions,
            'permissions' => $permissions,
        ]);
    }

    /**
     * @Route("/set/role/permissions", name="set_role_permissions", options={"expose"=true})
     */
    public function set_role_permissions(Request $request, RolePermissionRepository $rolePermissionRepository, RoleRepository $roleRepository, PermissionRepository $permissionRepository)
    {
        $em = $this->getDoctrine()->getManager();
        if ($request->isXmlHttpRequest()) {
            try{
                $rolesPermissions = $request->get('permissionsRoles');
                $decodedRolePermission = json_decode($rolesPermissions);


                foreach ($decodedRolePermission as $rolePermission) {

                    $role = $roleRepository->findOneBy(['name' => $rolePermission->roleCode]);
                    $permission = $permissionRepository->findOneBy(['id' => $rolePermission->permissionCode]);

                    $exist = $rolePermissionRepository->findOneBy(['role' => $role, 'permission' => $permission]);
                    if (!$exist) {

                        $rolePermissionObj = new RolePermission();
                        $rolePermissionObj->setPermission($permission);
                        $rolePermissionObj->setRole($role);
                        $em->persist($rolePermissionObj);
                    }
                }
                $em->flush();

                return new Response("ok",200);
            }catch (\Exception $exception){
                return new Response("ok",400);
            }

        }
    }

}
