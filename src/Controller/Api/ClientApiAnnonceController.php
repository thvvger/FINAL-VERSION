<?php

namespace App\Controller\Api;

use App\Entity\Annonce;
use App\Entity\AnnonceSonorise;
use App\Entity\Organisation;
use Doctrine\ORM\EntityManager;
use App\Repository\AnnonceRepository;
use App\Repository\AnnonceSonoriseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Requests as sonoRequest;

class ClientApiAnnonceController extends AbstractController
{
    const UNFILLED = "non renseigné";
    const SONO_AUTO = "automatique";
    const SONO_MANUELLE = "manuelle";

    /**
     * @Route("/api/client/api/annonce", name="api_client_api_annonce")
     */
    public function index(AnnonceRepository $annconceRepository): Response
    {




//        UNIQ ID
        $uniqueId = md5(uniqid());
        $uniqueId = substr($uniqueId, 0, 7);
        $rand = substr($uniqueId, 0, 2);

        $user = $this->getUser();

        $entityManager = $this->getDoctrine()->getManager();



        $informations = sonoRequest::get($user->getOrganisation()->getApiLink());

        /**  * @var Organisation $organisation */
        $organisation = $user->getOrganisation();
        $requestBody = json_decode($informations->body);
        if (isset($requestBody->jobs)){
            $jobs =$requestBody->jobs ? $requestBody->jobs : [] ;

            foreach ($jobs as $job) {
                $annonceExist = $annconceRepository->findBy(['reference' => $job->ref,"organisation"=>$user->getOrganisation()]);
                if (!$annonceExist) {

                    $annonce = new Annonce();
                    $annonce->setReference($job->ref);
                    $annonce->setTitre($job->name);
                    $annonce->setDescription($job->mission);
                    $annonce->setProfilRecherche(self::UNFILLED);
                    $annonce->setOrganisation($organisation);
                    $annonce->setRemuneration(self::UNFILLED);
                    $annonce->setNiveauEtude(self::UNFILLED);
                    $annonce->setLieu($job->address);
                    $annonce->setSalaire($job->salary);
                    $annonce->setTypeContrat($job->salary);
                    $annonce->setDureeInterim(self::UNFILLED);
                    $annonce->setExperienceProfessionnel(self::UNFILLED);
                    $annonce->setDateExpiration(new \DateTime($annonce->getDateExpiration()));
                    $annonce->setStatus("pending");
                    $annonce->setFormat("c");
                    $annonce->setEstSonorise(false);
                    $annonce->setAnnonceSonoLinkHolder($job->audio);
                    $annonce->setUser($user);
                    $annonce->setStatus("pending");
                    $annonce->setEstSonorise(false);
                    $annonce->setEstValider(null);
//                    if ($organisation->getTypeSonorisation() == self::SONO_AUTO) {
////                    $annonceSonorise = new AnnonceSonorise();
////                    $annonceSonorise->setAnnonce($annonce);
////                    $annonceSonorise->setReference($job->id);
////                    $annonceSonorise->setUrlSono($job->audio);
////                    $annonceSonorise->setTypeBouton("audio");
////                    $annonceSonorise->setStatutValidation(" ");
////                    $annonceSonorise->setAnnonceUrl($job->audio);
////                    $annonceSonorise->setUser($user);
////                    $entityManager->persist($annonceSonorise);
//
//                        $annonce->setStatus("pending");
//                        $annonce->setValiderPar($this->getUser());
//                        $annonce->setEstSonorise(false);
//                        $annonce->setEstValider(true);
//
//                    }
                    $entityManager->persist($annonce);
                }


            }
            $entityManager->flush();
            $this->addFlash("info", count($jobs) . " annonce(s) uploadée(s) avec succès ");
        }

//        $this->addFlash('info', 'Article Created! Knowledge is power!');
        return $this->redirectToRoute('dashboard');
    }


//    /**
//     * @Route("/api/client/api/annonce", name="api_client_api_annonce")
//     */
//    public function updateAnnonceStat(AnnonceRepository $annconceRepository): Response
//    {
//
//
//    }
}
