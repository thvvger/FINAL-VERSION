<?php

namespace App\Controller;

use App\Entity\Permission;
use App\Entity\User;
use App\Entity\UserPermission;
use App\Form\UserPermissionType;
use App\Repository\UserPermissionRepository;
use App\Security\PermissionName;
use App\Service\UserServices;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/sitip/user/permission")
 */
class UserPermissionController extends AbstractController
{

    private $userServices;

    public function __construct(UserServices $userServices)
    {
        $this->userServices = $userServices;
    }

    /**
     * @Route("/", name="user_permission_index", methods={"GET"})
     */
    public function index(UserPermissionRepository $userPermissionRepository): Response
    {
        return $this->render('user_permission/index.html.twig', [
            'user_permissions' => $userPermissionRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="user_permission_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {

        if ($request->isMethod('POST'))
        {

            if (!$this->userServices->checkIfUserHasPermission(PermissionName::CREATE, UserPermission::class))
            {
                $this->addFlash('danger', "Désolé, vous n'avez pas les droits requis sur cette page");
                return $this->redirectToRoute('admin_cms');
            }

            $em = $this->getDoctrine()->getManager();

            $userId = trim($request->get('user'));
            $permissionId = trim($request->get('permission'));

            $user = $em->getRepository(User::class)->find($userId);
            $permission = $em->getRepository(Permission::class)->find($permissionId);

            $userPermissionExiste = $em->getRepository(UserPermission::class)->findOneBy([
                'user' => $user,
                'permission' => $permission,
            ]);
            if ($userPermissionExiste != null)
            {
                $userPermissionExiste->setDeleted(false);
            }
            else
            {
                $userPermission = new UserPermission();
                $userPermission
                    ->setPermission($permission)
                    ->setUser($user)
                ;

                $em->persist($userPermission);
            }

            $em->flush();
            $this->addFlash('success', 'Permission ajoutée avec succès');


        }

        return $this->redirectToRoute('user_index');

    }

    /**
     * @Route("/{id}", name="user_permission_show", methods={"GET"})
     */
    public function show(UserPermission $userPermission): Response
    {
        return $this->render('user_permission/show.html.twig', [
            'user_permission' => $userPermission,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="user_permission_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, UserPermission $userPermission): Response
    {
        $form = $this->createForm(UserPermissionType::class, $userPermission);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_permission_index');
        }

        return $this->render('user_permission/edit.html.twig', [
            'user_permission' => $userPermission,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id}", name="user_permission_delete")
     */
    public function delete(Request $request, UserPermission $userPermission): Response
    {
        if (!$this->userServices->checkIfUserHasPermission(PermissionName::DELETE, UserPermission::class))
        {
            $this->addFlash('danger', "Désolé, vous n'avez pas les droits requis sur cette page");
            return $this->redirectToRoute('admin_cms');
        }

        $entityManager = $this->getDoctrine()->getManager();
        $userPermission->setDeleted(true);
        $entityManager->flush();

        $this->addFlash('success', 'Permission supprimée avec succès');

        return $this->redirectToRoute('user_index');
    }
}
