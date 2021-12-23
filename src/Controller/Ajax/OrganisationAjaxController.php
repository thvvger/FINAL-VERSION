<?php
/**
 * Created by PhpStorm.
 * User: LANGANFIN  Rogelio
 * Date: 27/04/2021
 * Time: 22:31
 */

namespace App\Controller\Ajax;


use App\Repository\AnnonceRepository;
use Requests as sonoRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;


/**
 * @Route("/annonce/ajax")
 */
class OrganisationAjaxController extends AbstractController
{
    /**
     * @Route("/generate/token", name="generate_token", methods={"POST"})
     */
    public function get_token(Request $request, AnnonceRepository $annonceRepository, SerializerInterface $serializer)
    {
        if ($request->isXmlHttpRequest()) {
//
            $email = $request->get("username");
            $route = $request->get("route");
            $prenom = $request->get("password");

            $informations = sonoRequest::post($route, [], [
                "email" => $email,
                "password" => $prenom,
            ]);
            dd($informations);
            $body = json_decode($informations->body);
           
            if ($body->success){
                $url =  "https://api.test.gotaf.fr/api/joblist_company?companyId=2&token=".$body->token;
            }
            return new Response($url);

        }

    }
}