<?php

namespace App\Controller;

use App\Entity\Role;
use App\Entity\User;
use App\Entity\UserRole;
use App\Form\UserRoleType;
use App\Repository\UserRoleRepository;
use App\Security\PermissionName;
use App\Service\UserServices;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/sitip/user/role")
 */
class UserRoleController extends AbstractController
{

    private $userServices;

    public function __construct(UserServices $userServices)
    {
        $this->userServices = $userServices;
    }


    /**
     * @Route("/", name="user_role_index", methods={"GET"})
     */
    public function index(UserRoleRepository $userRoleRepository): Response
    {
        return $this->render('user_role/index.html.twig', [
            'user_roles' => $userRoleRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="user_role_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {

       if ($request->isMethod('POST'))
       {
           if (!$this->userServices->checkIfUserHasPermission(PermissionName::CREATE, UserRole::class))
           {
               $this->addFlash('danger', "Désolé, vous n'avez pas les droits requis sur cette page");
               return $this->redirectToRoute('admin_cms');
           }



           $em = $this->getDoctrine()->getManager();

           $userId = trim($request->get('user'));
           $roleId = trim($request->get('role'));

           $user = $em->getRepository(User::class)->find($userId);
           $role = $em->getRepository(Role::class)->find($roleId);

           $userRoleExiste = $em->getRepository(UserRole::class)->findOneBy([
               'user' => $user,
               'role' => $role,
           ]);
           if ($userRoleExiste != null)
           {
               $userRoleExiste->setDeleted(false);
           }
           else
           {
                $userRole = new UserRole();
                $userRole
                    ->setRole($role)
                    ->setUser($user)
                    ;

                $em->persist($userRole);
           }

           $em->flush();
           $this->addFlash('success', 'Role ajouté avec succès');


       }

        return $this->redirectToRoute('user_index');
    }

    /**
     * @Route("/{id}", name="user_role_show", methods={"GET"})
     */
    public function show(UserRole $userRole): Response
    {
        return $this->render('user_role/show.html.twig', [
            'user_role' => $userRole,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="user_role_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, UserRole $userRole): Response
    {
        $form = $this->createForm(UserRoleType::class, $userRole);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_role_index');
        }

        return $this->render('user_role/edit.html.twig', [
            'user_role' => $userRole,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id}", name="user_role_delete")
     */
    public function delete(Request $request, UserRole $userRole): Response
    {

        if (!$this->userServices->checkIfUserHasPermission(PermissionName::DELETE, UserRole::class))
        {
            $this->addFlash('danger', "Désolé, vous n'avez pas les droits requis sur cette page");
            return $this->redirectToRoute('admin_cms');
        }

        $entityManager = $this->getDoctrine()->getManager();
        $userRole->setDeleted(true);
        $entityManager->flush();

        $this->addFlash('success', 'Role supprimé avec succès');

        return $this->redirectToRoute('user_index');
    }
}
