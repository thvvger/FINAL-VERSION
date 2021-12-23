<?php

namespace App\Controller;

use App\Entity\Annonce;
use App\Entity\AnnonceSonorise;
use App\Form\AnnonceSonoriseType;
use App\Repository\AnnonceRepository;
use App\Repository\AnnonceSonoriseRepository;
use App\Service\AdminEmailService;
use App\Service\ConstantClass;
use App\Service\UploadService;
use App\Service\UserEmailService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/annonce/sonorise")
 */
class AnnonceSonoriseController extends AbstractController
{
    /**
     * @var UploadService
     */
    private $uploadService;
    /**
     * @var AdminEmailService
     */
    private $adminEmailService;
    /**
     * @var UserEmailService
     */
    private $userEmailService;

    public function __construct(UploadService $uploadService, AdminEmailService $adminEmailService, UserEmailService $userEmailService)
    {
        $this->uploadService = $uploadService;
        $this->adminEmailService = $adminEmailService;
        $this->userEmailService = $userEmailService;
    }


    /**
     * @Route("/liste", name="annonce_sonorise_index", methods={"GET"})
     */
    public function index(AnnonceSonoriseRepository $annonceSonoriseRepository): Response
    {
        return $this->render('annonce_sonorise/index.html.twig', [
            'annonce_sonorises' => $annonceSonoriseRepository->findAll(),
        ]);
    }


    /**
     * @Route("/for/all", name="annonce_sonorise_all", methods={"POST"})
     */
    public function sonoriser_all_annonce(Request $request, AnnonceRepository $annonceRepository): Response
    {
        if ($this->isCsrfTokenValid('sono_all_of_them', $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $annonces = $annonceRepository->findBy(['organisation' => $this->getUser()->getOrganisation(), 'estSonorise' => false]);
            $annoncesHolder = [];
            foreach ($annonces as $annonce) {

                $annonceSonorise = new AnnonceSonorise();
                $annonceSonorise->setAnnonce($annonce);
                $annonceSonorise->setReference($annonce->getReference());
                $annonceSonorise->setUrlSono($annonce->getAnnonceSonoLinkHolder());
                $annonceSonorise->setTypeBouton("audio");
                $annonceSonorise->setStatutValidation(" ");
                $annonceSonorise->setAnnonceUrl($annonce->getAnnonceSonoLinkHolder());
                $annonceSonorise->setUser($this->getUser());

                $annonce->setStatus("sonorise");
                $annonce->setEstSonorise(true);

                if ($annonce->getEstValider() == false) {
                    $annonce->setEstValider(true);
                    $annonce->setValiderPar($this->getUser());
                }

                $entityManager->persist($annonceSonorise);
                $annoncesHolder[] = $annonceSonorise;
            }

            try {
                $entityManager->flush();
                $this->userEmailService->sendAllSonorisationDone($this->getUser()->getEmail(), $annoncesHolder);
                $this->adminEmailService->sendAllSonorisationDonePercentage($annoncesHolder);


            } catch (\Exception $exception) {

            }

        }

        return $this->redirectToRoute("dashboard");
    }


    /**
     * @Route("/new", name="annonce_sonorise_new", methods={"GET","POST"})
     */
    public function new(Request $request, AnnonceRepository $annonceRepository, AnnonceSonoriseRepository $annonceSonoriseRepository): Response
    {

        $annonceIsSono = false;

        if ($request->isMethod("POST")) {

            $entityManager = $this->getDoctrine()->getManager();
//          $music_file = $request->files->get('annonce_sonorise_url_sono');
            $music_link = $request->get('annonce_sonorise_url_sono');
            if ($music_link) {
                $annonce_reference = $request->get('annonce_reference');
                $annonce_url = $request->get('annonce_url');
                $type_bouton = $request->get('type_bouton');
                $annonce = $annonceRepository->findOneBy(['reference' => $annonce_reference, 'organisation' => $this->getUser()->getOrganisation()]);
                $annonceIsSonorise = $annonceSonoriseRepository->findOneBy(["annonce" => $annonce]);

                if ($annonceIsSonorise) {
                    $annonceIsSonorise->setUrlSono($music_link);
                    $entityManager->flush();
                    $this->addFlash("success", "l'annonce " . $annonce->getReference() . " a été correctement enregistrée");
                    return $this->redirectToRoute('annonce_sonorise_index');
                } else {
                    if ($annonce && !$annonce->getEstSonorise()) {
                        $annonceSonorise = (new AnnonceSonorise())
                            ->setAnnonce($annonce)
                            ->setTypeBouton($annonce->getOrganisation()->getTypeBouton())
                            ->setUrlSono($music_link)
                            ->setAnnonceUrl("");

                        $annonce->setEstSonorise(true);
                        $annonce->setStatus('sonorise');

                        $entityManager->persist($annonceSonorise);
                        $entityManager->flush();

                        $this->adminEmailService->sendNewSonorisation($this->getUser()->getEmail(), $annonceSonorise);
                        $this->userEmailService->sendNewSonorisation($this->getUser()->getEmail(), $annonceSonorise);

                        $this->addFlash("success", "l'annonce " . $annonce->getReference() . " a été correctement enregistrée");
                        return $this->redirectToRoute('annonce_sonorise_index');
                    }
                }

            } else {
                $annonceIsSono = true;

            }
        }


        return $this->render('annonce_sonorise/new.html.twig', [
            "annonceIsSono" => $annonceIsSono
        ]);
    }


    /**
     * @Route("/redirect/{id}", name="annonce_sonorise_redirect", methods={"GET","POST"})
     */
    public function redirect_from(Annonce $annonce)
    {

        return $this->render('annonce_sonorise/redirect.html.twig', [
            'annonce' => $annonce,
        ]);
    }

    /**
     * @Route("/{id}", name="annonce_sonorise_show", methods={"GET"})
     */
    public function show(AnnonceSonorise $annonceSonorise, Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        if ($request->isMethod("POST")) {
            if ($this->isCsrfTokenValid('statut_annonce_sono_change', $request->request->get('_token'))) {
                $newStatut = $request->get("submit_button");
                $annonceSonorise->setStatutValidation($newStatut);

                if ($newStatut == ConstantClass::rejected) {
                    $annonce = $annonceSonorise->getAnnonce();
                    $annonce->setEstSonorise(false);
                }
                $em->flush();
            }
        }

        return $this->render('annonce_sonorise/show.html.twig', [
            'annonce_sonorise' => $annonceSonorise,
        ]);
    }


    /**
     * @Route("/{id}/edit", name="annonce_sonorise_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, AnnonceSonorise $annonceSonorise): Response
    {
        $form = $this->createForm(AnnonceSonoriseType::class, $annonceSonorise);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('annonce_sonorise_index');
        }

        return $this->render('annonce_sonorise/edit.html.twig', [
            'annonce_sonorise' => $annonceSonorise,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="annonce_sonorise_delete", methods={"POST"})
     */
    public function delete(Request $request, AnnonceSonorise $annonceSonorise): Response
    {
        if ($this->isCsrfTokenValid('delete' . $annonceSonorise->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($annonceSonorise);
            $entityManager->flush();
        }

        return $this->redirectToRoute('annonce_sonorise_index');
    }
}
