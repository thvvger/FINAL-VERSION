<?php
/**
 * Created by PhpStorm.
 * User: LANGANFIN  Rogelio
 * Date: 27/04/2021
 * Time: 22:31
 */

namespace App\Controller\Ajax;


use App\Repository\AnnonceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;


/**
 * @Route("/annonce/ajax")
 */
class AnnonceAjaxControler extends AbstractController
{
    /**
     * @Route("/load/data", name="annonce_load_data", methods={"POST"})
     */
    public function load_annonce_data(Request $request, AnnonceRepository $annonceRepository, SerializerInterface $serializer)
    {
        if ($request->isXmlHttpRequest()) {

            $reference = $request->get('ref');

            $annonce = $annonceRepository->findOneBy(['reference' => $reference]);

            if ($annonce) {

                $datas = [
                    "titre_annonce" => $annonce->getTitre(),
                    "organisation" => $annonce->getOrganisation()->getRaisonSocial(),
                    "link_sono" => $annonce->getAnnonceSonoLinkHolder(),
                ];
                return new JsonResponse($datas, 200, []);
            }else{
//                return new JsonResponse([], 200, []);
            }

        }

    }
}