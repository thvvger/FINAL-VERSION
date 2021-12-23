<?php

namespace App\Controller\Ajax;


use App\Entity\Permission;
use App\Entity\Role;
use App\Entity\RolePermission;
use App\Entity\User;
use App\Entity\UserPermission;
use App\Entity\UserRole;
use App\Service\UserServices;
use Symfony\Bundle\FrameworkBundle\Controller   \AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RoleAjaxController extends AbstractController
{

    private $userServices;


    public function __construct(UserServices $userServices)
    {
        $this->userServices = $userServices;
    }

    /**
     * @Route("/ajax/remove/role/permissions", name="ajax_remove_role_permissions", options={"expose"=true})
     */
    public function removeRolePermissions(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        if ($request->isXmlHttpRequest()) {
            try {
                $role = $em->getRepository(Role::class)->findOneBy(['name'=>$request->get('roleId')]);
                $permission = $em->getRepository(Permission::class)->findOneBy(['id'=>$request->get('permissionId')]);

                $rolePermission = $em->getRepository(RolePermission::class)->findOneBy([
                    'role' => $role,
                    'permission' => $permission,
                ]);
                $em->remove($rolePermission);
                $em->flush();
                return new Response("is done", 200);
            } catch (\Exception $exception) {

                return new Response("error", 400);
            }
        }
    }

    /**
     * @Route("/ajax/get/role/permissions", name="ajax_get_role_permissions", options={"expose"=true})
     */
    public function getRolePermissions(Request $request)
    {

        $rolePermissions = $permissions = [];
        $roleId = "";
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();

            $roleId = trim($request->get('role'));

            $role = $em->getRepository(Role::class)->findOneByName($roleId);

            $rolePermissions = $em->getRepository(RolePermission::class)->findBy([
                'role' => $role,
                'deleted' => false,
            ]);

            $allPermissions = $em->getRepository(Permission::class)->findAll();

            foreach ($rolePermissions as $permission) {
                $arrayData = [
                    "elementId" => rand(0, 999),
                    'roleCode' => $permission->getRole()->getName(),
                    'roleLib' => $permission->getRole()->getName(),
                    'permissionCode' => $permission->getPermission()->getId(),
                    'permissionLib' => $permission->getPermission()->getDescription()
                ];
                array_push($permissions, $arrayData);
            }

//            $encodeData  =  json_encode($permissions);

            return $this->json($permissions, 200);


        }

    }
}
