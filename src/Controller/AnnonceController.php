<?php

namespace App\Controller;

use App\Entity\Annonce;
use App\Form\Model\AnnonceDeleteModelType;
use App\Form\AnnonceType;
use App\Model\AnnonceDeleteModel;
use App\Repository\AnnonceRepository;
use App\Repository\AnnonceSonoriseRepository;
use App\Security\PermissionName;
use App\Service\NotificationService;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/annonce")
 */
class AnnonceController extends AbstractController
{

    /**
     * @var NotificationService
     */
    private $notificationService;
    /**
     * @var UserService
     */
    private $service;

    public function __construct(NotificationService $notificationService,UserService $service)
    {
        $this->notificationService = $notificationService;
        $this->service = $service;
    }

    /**
     * @Route("/admin", name="annonce_admin", methods={"GET"})
     */
    public function annonce_admin(AnnonceRepository $annonceRepository): Response
    {
        return $this->render('annonce/annonce_admin.html.twig', [
            'annonces' => $annonceRepository->findBy(['organisation' => $this->getUser()->getOrganisation()]),
        ]);
    }

    /**
     * @Route("/rejete", name="annonce_rejete", methods={"GET"})
     */
    public function annonce_rejete(AnnonceRepository $annonceRepository): Response
    {
        return $this->render('annonce/annonce_rejete_or_valider.html.twig', [
            'annonces' => $annonceRepository->findBy(['estValider' => false, 'organisation' => $this->getUser()->getOrganisation()], ["id" => 'desc']),
            'titre' => "Annonces rejetées",
            'libelle' => "annonces rejetées",
        ]);
    }

    /**
     * @Route("/valider", name="annonce_valider", methods={"GET"})
     */
    public function annonce_valider(AnnonceRepository $annonceRepository): Response
    {
        return $this->render('annonce/annonce_rejete_or_valider.html.twig', [
            'annonces' => $annonceRepository->findBy(['estValider' => true, 'organisation' => $this->getUser()->getOrganisation()], ["id" => 'desc']),
            'titre' => "Annonces validées",
            'libelle' => "annonces validées",
        ]);
    }

    /**
     * @Route("/texte", name="annonce_texte", methods={"GET"})
     */
    public function annonce_texte(AnnonceRepository $annonceRepository): Response
    {
        if (!$this->service->checkIfUserHasPermission(PermissionName::SHOW_ALL, Annonce::class))
        {
            $this->addFlash('error', "Désolé, vous n'avez pas les droits requis sur cette page");
            return $this->redirectToRoute('dashboard');
        }
        return $this->render('annonce/annonce_texte.html.twig', [
            'annonces' => $annonceRepository->findBy(['organisation' => $this->getUser()->getOrganisation()]),
        ]);
    }

    /**
     * @Route("/", name="annonce_index", methods={"GET"})
     */
    public function index(AnnonceRepository $annonceRepository): Response
    {
        if (!$this->service->checkIfUserHasPermission(PermissionName::SHOW_ALL, Annonce::class))
        {
            $this->addFlash('error', "Désolé, vous n'avez pas les droits requis sur cette page");
            return $this->redirectToRoute('dashboard');
        }
        return $this->render('annonce/index.html.twig', [
            'annonces' => $annonceRepository->findBy(['organisation' => $this->getUser()->getOrganisation()]),
        ]);
    }

    /**
     * @Route("/new", name="annonce_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        if (!$this->service->checkIfUserHasPermission(PermissionName::CREATE, Annonce::class))
        {
            $this->addFlash('error', "Désolé, vous n'avez pas les droits requis sur cette page");
            return $this->redirectToRoute('dashboard');
        }

        $annonce = new Annonce();
        $form = $this->createForm(AnnonceType::class, $annonce);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $annonce->setDateExpiration(new \DateTime($annonce->getDateExpiration()));
            $annonce->setStatus("pending");
            $annonce->setFormat("c");
            $annonce->setUser($this->getUser());
            $entityManager->persist($annonce);

            // new notification
            $noti_contenu = "Une  nouvelle annonce  avec  " . $annonce->getReference() . " à été correctement ajouté";
            $this->notificationService->new_notification('Nouvelle annonce', $noti_contenu);


            $entityManager->flush();
            $this->addFlash("success", "l'annonce " . $annonce->getReference() . " a été correctement ajoutée");

            return $this->redirectToRoute('annonce_texte');
        }

        return $this->render('annonce/new.html.twig', [
            'annonce' => $annonce,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="annonce_show", methods={"GET"})
     */
    public function show(Annonce $annonce, AnnonceSonoriseRepository $annonceSonoriseRepository): Response
    {
        if (!$this->service->checkIfUserHasPermission(PermissionName::SHOW, Annonce::class))
        {
            $this->addFlash('error', "Désolé, vous n'avez pas les droits requis sur cette page");
            return $this->redirectToRoute('dashboard');
        }
        $annonceSonnorise = null;
        if ($annonce->getEstSonorise()) {
            $annonceSonnorise = $annonceSonoriseRepository->findOneBy(['annonce' => $annonce]);
        }
        return $this->render('annonce/show.html.twig', [
            'annonce' => $annonce,
            'annonceSonorise' => $annonceSonnorise,
        ]);
    }


    /**
     * @Route("/delete/all/org/annonce", name="delete_all_annonce_org", methods={"GET","POST"})
     */
    public function delete_all_annonce_org(Request $request, AnnonceRepository $annonceRepository, AnnonceSonoriseRepository $annonceSonoriseRepository): Response
    {
//        if (!$this->service->checkIfUserHasPermission(PermissionName::DELETE, Annonce::class))
//        {
//            $this->addFlash('error', "Désolé, vous n'avez pas les droits requis sur cette page");
//            return $this->redirectToRoute('dashboard');
//        }
        $deletableAnnonce = new AnnonceDeleteModel();
        $form = $this->createForm(AnnonceDeleteModelType::class, $deletableAnnonce);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $organisation = $deletableAnnonce->getOrganisation();

            $annonces = $annonceRepository->findBy(['organisation' => $organisation]);
            foreach ($annonces as $annonce) {
                $annonceSonorises = $annonceSonoriseRepository->findOneBy(["annonce" => $annonce]);

                $em->remove($annonce);
                if (!empty($annonceSonorises)) {
                    if ($annonceSonorises)
                        $em->remove($annonceSonorises);
                }

            }

            $this->addFlash("success", "Suppression effectuer avec succès");
            $em->flush();
            return $this->render('annonce/full_management.html.twig', [
                'form' => $form->createView(),
                'deleted' => 1,
            ]);
        }
        return $this->render('annonce/full_management.html.twig', [
            'form' => $form->createView(),
            'deleted' => 0,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="annonce_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Annonce $annonce): Response
    {
        if (!$this->service->checkIfUserHasPermission(PermissionName::UPDATE, Annonce::class))
        {
            $this->addFlash('error', "Désolé, vous n'avez pas les droits requis sur cette page");
            return $this->redirectToRoute('dashboard');
        }
        $form = $this->createForm(AnnonceType::class, $annonce);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('annonce_index');
        }

        return $this->render('annonce/edit.html.twig', [
            'annonce' => $annonce,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{id}/change/validation/status", name="change_validation_status", methods={"GET","POST"})
     */
    public function validateOrNot(Request $request, Annonce $annonce)
    {
        if (!$this->service->checkIfUserHasPermission(PermissionName::UPDATE, Annonce::class))
        {
            $this->addFlash('error', "Désolé, vous n'avez pas les droits requis sur cette page");
            return $this->redirectToRoute('dashboard');
        }
        $entityManager = $this->getDoctrine()->getManager();
        if ($request->isMethod("POST")) {
            $typeSubmit = $request->get('typeSubmit');

            if ($typeSubmit == "valider") {
                $annonce->setEstValider(true);
            } else {
                $annonce->setEstValider(false);
            }

            $annonce->setValiderPar($this->getUser());
            $entityManager->flush();

            return $this->redirectToRoute("annonce_show", ['id' => $annonce->getId()]);
        }
    }

    /**
     * @Route("/{id}", name="annonce_delete", methods={"POST"})
     */
    public function delete(Request $request, Annonce $annonce): Response
    {
        if (!$this->service->checkIfUserHasPermission(PermissionName::DELETE, Annonce::class))
        {
            $this->addFlash('error', "Désolé, vous n'avez pas les droits requis sur cette page");
            return $this->redirectToRoute('dashboard');
        }
        if ($this->isCsrfTokenValid('delete' . $annonce->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($annonce);
            $entityManager->flush();
        }

        return $this->redirectToRoute('annonce_index');
    }
}
