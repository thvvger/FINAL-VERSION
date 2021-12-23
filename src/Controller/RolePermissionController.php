<?php

namespace App\Controller;

use App\Entity\Permission;
use App\Entity\Role;
use App\Entity\RolePermission;
use App\Form\RolePermissionType;
use App\Repository\RolePermissionRepository;
use App\Security\PermissionName;
use App\Service\UserServices;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/sitip/role/permission")
 */
class RolePermissionController extends AbstractController
{
    private $userServices;

    public function __construct(UserServices $userServices)
    {
        $this->userServices = $userServices;
    }


    /**
     * @Route("/", name="role_permission_index", methods={"GET"})
     */
    public function index(RolePermissionRepository $rolePermissionRepository): Response
    {
        return $this->render('role_permission/index.html.twig', [
            'role_permissions' => $rolePermissionRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="role_permission_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        if ($request->isMethod('POST'))
        {

            if (!$this->userServices->checkIfUserHasPermission(PermissionName::CREATE, RolePermission::class))
            {
                $this->addFlash('danger', "Désolé, vous n'avez pas les droits requis sur cette page");
                return $this->redirectToRoute('admin_cms');
            }

            $em = $this->getDoctrine()->getManager();

            $roleId = trim($request->get('role'));
            $permissionId = trim($request->get('permission'));

            $role = $em->getRepository(Role::class)->find($roleId);
            $permission = $em->getRepository(Permission::class)->find($permissionId);

            $rolePermissionExiste = $em->getRepository(RolePermission::class)->findOneBy([
                'role' => $role,
                'permission' => $permission,
            ]);
            if ($rolePermissionExiste != null)
            {
                $rolePermissionExiste->setDeleted(false);
            }
            else
            {
                $rolePermission = new RolePermission();
                $rolePermission
                    ->setPermission($permission)
                    ->setRole($role)
                ;

                $em->persist($rolePermission);
            }

            $em->flush();
            $this->addFlash('success', 'Permission ajoutée avec succès');


        }

        return $this->redirectToRoute('role_index');

    }

    /**
     * @Route("/{id}", name="role_permission_show", methods={"GET"})
     */
    public function show(RolePermission $rolePermission): Response
    {
        return $this->render('role_permission/show.html.twig', [
            'role_permission' => $rolePermission,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="role_permission_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, RolePermission $rolePermission): Response
    {
        $form = $this->createForm(RolePermissionType::class, $rolePermission);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('role_permission_index');
        }

        return $this->render('role_permission/edit.html.twig', [
            'role_permission' => $rolePermission,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id}", name="role_permission_delete")
     */
    public function delete(Request $request, RolePermission $rolePermission): Response
    {
        if (!$this->userServices->checkIfUserHasPermission(PermissionName::DELETE, Role::class))
        {
            $this->addFlash('danger', "Désolé, vous n'avez pas les droits requis sur cette page");
            return $this->redirectToRoute('admin_cms');
        }


        $entityManager = $this->getDoctrine()->getManager();
        $rolePermission->setDeleted(true);
        $entityManager->flush();

        $this->addFlash('success', 'Permission supprimée avec succès');

        return $this->redirectToRoute('role_index');

    }
}
