<?php

namespace App\Controller;

use App\Entity\Role;
use App\Form\Role1Type;
use App\Form\RoleType;
use App\Repository\PermissionRepository;
use App\Repository\RoleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/role")
 */
class RoleController extends AbstractController
{
    /**
     * @Route("/", name="role_index", methods={"GET"})
     */
    public function index(RoleRepository $roleRepository): Response
    {
        return $this->render('role/index.html.twig', [
            'roles' => $roleRepository->findAll(),
        ]);
    }

    /**
     * @Route("/permission", name="role_permission", methods={"GET"})
     */
    public function role_permission(RoleRepository $roleRepository, PermissionRepository $permissionRepository, Request $request): Response
    {
        $role_id = $request->get('role_id');



//        return $this->render('role/role_permission.html.twig', [
        return $this->render('role/gestionRole.html.twig', [
            'roles' => $roleRepository->findAll(),
            "role_id" =>$role_id,
            'permissions' => $permissionRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="role_new", methods={"GET","POST"})
     */
    public function new(Request $request, RoleRepository $roleRepository): Response
    {
        $role = new Role();
        $form = $this->createForm(RoleType::class, $role);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $existObject = $roleRepository->findBy(['name' => $role->getName()]);
            if ($existObject) {
                return $this->redirectToRoute("role_new");
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($role);
            $entityManager->flush();

            return $this->redirectToRoute('role_new');
        }

        return $this->render('role/new.html.twig', [
            'role' => $role,
            'roles' => $roleRepository->findAll(),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="role_show", methods={"GET"})
     */
    public function show(Role $role): Response
    {
        return $this->render('role/show.html.twig', [
            'role' => $role,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="role_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Role $role): Response
    {
        $form = $this->createForm(Role1Type::class, $role);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('role_index');
        }

        return $this->render('role/edit.html.twig', [
            'role' => $role,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="role_delete", methods={"POST"})
     */
    public function delete(Request $request, Role $role): Response
    {
        if ($this->isCsrfTokenValid('delete' . $role->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($role);
            $entityManager->flush();
        }

        return $this->redirectToRoute('role_index');
    }
}
