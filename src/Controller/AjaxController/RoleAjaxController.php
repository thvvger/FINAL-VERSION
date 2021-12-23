<?php

namespace App\Controller\AjaxController;


use App\Entity\Permission;
use App\Entity\Role;
use App\Entity\RolePermission;
use App\Entity\User;
use App\Entity\UserPermission;
use App\Entity\UserRole;
use App\Service\UserServices;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class RoleAjaxController extends AbstractController
{

    private $userServices;


    public function __construct(UserServices $userServices)
    {
        $this->userServices = $userServices;
    }


    /**
     * @Route("azeeeeeeeeeeeeeee/ajax/get/role/permissions", name="sdqqqqqqqqqqqqqqqqqajax_get_role_permissions", options={"expose"=true})
     */
    public function getRolePermissions(Request $request)
    {

        $rolePermissions = $permissions = [];
        $roleId = "";
        if ($request->isXmlHttpRequest())
        {
            $em = $this->getDoctrine()->getManager();

            $roleId = trim($request->get('role'));

            $role = $em->getRepository(Role::class)->find($roleId);

            $rolePermissions = $em->getRepository(RolePermission::class)->findBy([
                'role' => $role,
                'deleted' => false,
            ]);

            $allPermissions = $em->getRepository(Permission::class)->findAll();
            foreach ($allPermissions as $permission)
            {
                $rolePermissionExiste = $em->getRepository(RolePermission::class)->findBy([
                    'role' => $role,
                    'deleted' => false,
                    'permission' => $permission
                ]);

                if ($rolePermissionExiste == null)
                {
                    $permissions[] = $permission;
                }

            }
        }


        return $this->render('admin/role/ajax/role_permission_ajax.html.twig', [
            'roleId' => $roleId,
            'rolePermissions' => $rolePermissions,
            'permissions' => $permissions,
        ]);
    }



}
