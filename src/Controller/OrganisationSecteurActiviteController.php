<?php

namespace App\Controller;

use App\Entity\OrganisationSecteurActivite;
use App\Form\OrganisationSecteurActiviteType;
use App\Repository\OrganisationSecteurActiviteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/organisation/secteur/activite")
 */
class OrganisationSecteurActiviteController extends AbstractController
{
    /**
     * @Route("/", name="organisation_secteur_activite_index", methods={"GET"})
     */
    public function index(OrganisationSecteurActiviteRepository $organisationSecteurActiviteRepository): Response
    {
        return $this->render('organisation_secteur_activite/index.html.twig', [
            'organisation_secteur_activites' => $organisationSecteurActiviteRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="organisation_secteur_activite_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $organisationSecteurActivite = new OrganisationSecteurActivite();
        $form = $this->createForm(OrganisationSecteurActiviteType::class, $organisationSecteurActivite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($organisationSecteurActivite);
            $entityManager->flush();

            return $this->redirectToRoute('organisation_secteur_activite_index');
        }

        return $this->render('organisation_secteur_activite/new.html.twig', [
            'organisation_secteur_activite' => $organisationSecteurActivite,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="organisation_secteur_activite_show", methods={"GET"})
     */
    public function show(OrganisationSecteurActivite $organisationSecteurActivite): Response
    {
        return $this->render('organisation_secteur_activite/show.html.twig', [
            'organisation_secteur_activite' => $organisationSecteurActivite,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="organisation_secteur_activite_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, OrganisationSecteurActivite $organisationSecteurActivite): Response
    {
        $form = $this->createForm(OrganisationSecteurActiviteType::class, $organisationSecteurActivite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('organisation_secteur_activite_index');
        }

        return $this->render('organisation_secteur_activite/edit.html.twig', [
            'organisation_secteur_activite' => $organisationSecteurActivite,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="organisation_secteur_activite_delete", methods={"POST"})
     */
    public function delete(Request $request, OrganisationSecteurActivite $organisationSecteurActivite): Response
    {
        if ($this->isCsrfTokenValid('delete'.$organisationSecteurActivite->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($organisationSecteurActivite);
            $entityManager->flush();
        }

        return $this->redirectToRoute('organisation_secteur_activite_index');
    }
}
