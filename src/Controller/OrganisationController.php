<?php

namespace App\Controller;

use App\Entity\Organisation;
use App\Form\OrganisationType;
use App\Repository\OrganisationRepository;
use App\Security\PermissionName;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/organisation")
 */
class OrganisationController extends AbstractController
{
    /**
     * @var UserService
     */
    private $service;

    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    /**
     * @Route("/", name="organisation_index", methods={"GET"})
     */
    public function index(OrganisationRepository $organisationRepository): Response
    {
        if (!$this->service->checkIfUserHasPermission(PermissionName::SHOW_ALL, Organisation::class))
        {
            $this->addFlash('error', "Désolé, vous n'avez pas les droits requis sur cette page");
            return $this->redirectToRoute('dashboard');
        }
        return $this->render('organisation/index.html.twig', [
            'organisations' => $organisationRepository->findBy(['deleted'=>false],['id'=>"ASC"],null,null),
        ]);
    }

    /**
     * @Route("/new", name="organisation_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        if (!$this->service->checkIfUserHasPermission(PermissionName::CREATE, Organisation::class))
        {
            $this->addFlash('error', "Désolé, vous n'avez pas les droits requis sur cette page");
            return $this->redirectToRoute('dashboard');
        }
        $organisation = new Organisation();
        $form = $this->createForm(OrganisationType::class, $organisation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($organisation);
            $entityManager->flush();
            $this->addFlash("success",'Enregistrement réussi  ');
            return $this->redirectToRoute("organisation_index");
        }

        return $this->render('organisation/new.html.twig', [
            'organisation' => $organisation,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="organisation_show", methods={"GET"})
     */
    public function show(Organisation $organisation): Response
    {

        return $this->render('organisation/show.html.twig', [
            'organisation' => $organisation,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="organisation_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Organisation $organisation): Response
    {
        $form = $this->createForm(OrganisationType::class, $organisation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('organisation_index');
        }

        return $this->render('organisation/edit.html.twig', [
            'organisation' => $organisation,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="organisation_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Organisation $organisation): Response
    {
        if ($this->isCsrfTokenValid('delete'.$organisation->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $organisation->setDeleted(false);
            $entityManager->remove($organisation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('organisation_index');
    }
}
