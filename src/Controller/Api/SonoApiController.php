<?php
/**
 * Created by PhpStorm.
 * User: LANGANFIN  Rogelio
 * Date: 20/04/2021
 * Time: 09:15
 */

namespace App\Controller\Api;


use App\Entity\ApiAnnonceStats;
use App\Repository\AnnonceRepository;
use App\Repository\AnnonceSonoriseRepository;
use App\Repository\ApiAnnonceStatsRepository;
use App\Repository\OrganisationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api")
 */
class SonoApiController extends AbstractController
{
    /**
     * @var ApiAnnonceStatsRepository
     */
    private $annonceStatsRepository;

    public function __construct(ApiAnnonceStatsRepository $annonceStatsRepository
    )
    {
        $this->annonceStatsRepository = $annonceStatsRepository;
    }

    /**
     * @Route("/get/sono", name="api_get_sono", methods={"POST"})
     */
    public function get_sono(Request $request, OrganisationRepository $organisationRepository, AnnonceRepository $annonceRepository, AnnonceSonoriseRepository $annonceSonoriseRepository)
    {
        $content = $request->getContent();
        $organisation_reference = "";
        $annonce_reference = "";
        if ($content) {

            $decodedData = json_decode($content);

            $organisation_reference = $decodedData->organisation_reference;
            $annonce_reference = $decodedData->annonce_reference;
            $annonce_reference = trim(str_replace(array("\r\n", "\n", "\r"), ' ', $annonce_reference));


        } else {
            $organisation_reference = $request->get('organisation_reference');
            $annonce_reference = $request->get('annonce_reference');
            $annonce_reference = trim(str_replace(array("\r\n", "\n", "\r"), ' ', $annonce_reference));


        }

        $exist_organisation = $organisationRepository->findOneBy(['reference' => $organisation_reference]);

        $exist_annonce = $annonceRepository->findOneBy(['reference' => $annonce_reference]);
        if ($exist_annonce && $exist_organisation) {


            $annonceSonorise = $annonceSonoriseRepository->findOneBy([
                "annonce" => $exist_annonce
            ]);

            $em = $this->getDoctrine()->getManager();

            //mise a jour des stats
            $annonceStat = $this->annonceStatsRepository->findOneBy(["annonceSonorise" => $annonceSonorise]);

            if ($annonceStat) {
                $annonceStat->setNombreAppelApi($annonceStat->getNombreAppelApi() + 1);
            } else {
                $annonceStatNew = new ApiAnnonceStats();
                $annonceStatNew->setAnnonceSonorise($annonceSonorise);
                $annonceStatNew->setTempsLecture(0);
                $annonceStatNew->setNombreAppelApi($annonceStatNew->getNombreAppelApi() + 1);
                $em->persist($annonceStatNew);
            }
            $em->flush();

            return new JsonResponse([
                'code' => 200,
                'content' => [
                    'url_music' => $annonceSonorise->getUrlSono()
                ]
            ]);

        }

        return new JsonResponse([
            'code' => 400,
            'content' => [
                'url_music' => "annonce non sonorisée"
            ]

        ]);
    }

    /**
     * @Route("/update/sono/stats", name="api_get_sono_stats", methods={"POST"})
     */
    public function update_sono_stats(Request $request, AnnonceSonoriseRepository $annonceSonoriseRepository, ApiAnnonceStatsRepository $annonceStatsRepository, EntityManagerInterface $entityManager)
    {

        $content = $request->getContent();
        if ($content) {

            $decodedData = json_decode($content);
            $annonce_reference = $decodedData->annonce_reference;
            $tempsAdditionnel = $decodedData->temps_lecture;
            $annonce_reference = trim(str_replace(array("\r\n", "\n", "\r"), ' ', $annonce_reference));


        } else {
            $annonce_reference = $request->get('annonce_reference');
            $tempsAdditionnel = $request->get('temps_lecture');

            $annonce_reference = trim(str_replace(array("\r\n", "\n", "\r"), ' ', $annonce_reference));
        }
        $annonceSonoriser = $annonceSonoriseRepository->findOneBy(['reference' => $annonce_reference]);

        if (!$annonceSonoriser) {
            return new JsonResponse([
                'code' => 400,
                'content' => [
                    "message" => "annonce non existant "
                ]
            ]);
        }

        $annonceStat = $annonceStatsRepository->findOneBy(['annonceSonorise' => $annonceSonoriser]);

        $annonceStat->setTempsLecture($annonceStat->getTempsLecture() + (int)$tempsAdditionnel);
        $entityManager->flush();
        return new JsonResponse([
            'code' => 200,
            'content' => [
                "message" => "Temps de lecture mis a jour avec succès"
            ]
        ]);


    }


}