<?php

namespace App\Controller;

use App\Entity\Permission;
use App\Form\PermissionType;
use App\Repository\PermissionRepository;
use App\Security\PermissionName;
use App\Service\UserServices;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("admin/sitip/permission")
 */
class PermissionController extends AbstractController
{

    private $userServices;

    public function __construct(UserServices $userServices)
    {
        $this->userServices = $userServices;
    }

    /**
     * @Route("/", name="permission_index")
     */
    public function index(PermissionRepository $permissionRepository, Request $request): Response
    {
        if (!$this->userServices->checkIfUserHasPermission(PermissionName::SHOW_ALL, Permission::class))
        {
            $this->addFlash('danger', "Désolé, vous n'avez pas les droits requis sur cette page");
            return $this->redirectToRoute('admin_cms');
        }

        if ($request->isMethod('POST'))
        {
            $em = $this->getDoctrine()->getManager();

            $description = trim($request->get('description'));
            $permissionId = trim($request->get('permission'));

            $permission = $permissionRepository->find($permissionId);

            $permission->setDescription($description);

            $em->flush();
            $this->addFlash('success', "Permission modifée avec succès");

            return $this->redirectToRoute('permission_index');
        }


        return $this->render('admin/permission/index.html.twig', [
            'permissions' => $permissionRepository->findBy([
                'visible' => true
            ]),
        ]);
    }

    /**
     * @Route("/new", name="permission_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $permission = new Permission();
        $form = $this->createForm(PermissionType::class, $permission);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($permission);
            $entityManager->flush();

            return $this->redirectToRoute('permission_index');
        }

        return $this->render('permission/new.html.twig', [
            'permission' => $permission,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="permission_show", methods={"GET"})
     */
    public function show(Permission $permission): Response
    {
        return $this->render('permission/show.html.twig', [
            'permission' => $permission,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="permission_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Permission $permission): Response
    {
        $form = $this->createForm(PermissionType::class, $permission);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('permission_index');
        }

        return $this->render('permission/edit.html.twig', [
            'permission' => $permission,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="permission_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Permission $permission): Response
    {
        if ($this->isCsrfTokenValid('delete'.$permission->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($permission);
            $entityManager->flush();
        }

        return $this->redirectToRoute('permission_index');
    }
}
