<?php

namespace App\Controller;

use App\Repository\AnnonceRepository;
use App\Repository\AnnonceSonoriseRepository;
use App\Repository\UserRepository;
use App\Service\AnnonceService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class DefaultController extends AbstractController
{


    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var AnnonceSonoriseRepository
     */
    private $annonceSonoriseRepository;
    /**
     * @var AnnonceRepository
     */
    private $annonceRepository;

    public function __construct(UserRepository            $userRepository,
                                AnnonceSonoriseRepository $annonceSonoriseRepository,
                                AnnonceRepository         $annonceRepository)
    {
        $this->userRepository = $userRepository;
        $this->annonceSonoriseRepository = $annonceSonoriseRepository;
        $this->annonceRepository = $annonceRepository;
    }



    /**
     * @Route("/test", name="test")
     */
    public function test(Request $request,UserRepository $userRepository)
    {
        $user = $userRepository->find(1);

        return  $this->render('registration/confirmation_email.html.twig',['user'=>$user,'signedUrl'=>"ff"]);

    }

    /**
     * @Route("/email/test", name="test_email")
     */
    public function test_email(Request $request,UserRepository $userRepository,AnnonceSonoriseRepository $annonceSonoriseRepository)
    {
        $user = $userRepository->find(1);

        return  $this->render('email/admin/user_created.html.twig',['user'=>$this->getUser()]);

    }
    /**
     * @Route("/", name="dashboard")
     */
    public function index( AnnonceService $annonceService): Response
    {
        $user =  $this->getUser();
//            $anon = $this->annonceRepository->findBy(["estValider" => true], null, 5);
//        $annonceSonorise = $this->annonceSonoriseRepository->findOneBy(['annonce' => $anon[0]]);

//        dump($annonceSonorise->getApiAnnonceStats());
//        die;
//        return $this->render('default/index.html.twig', [
        $organisation = $user->getOrganisation();
//        dump($this->annonceSonoriseRepository->getAllSonorise($organisation));
//        die;


        $organisation = $user->getOrganisation();
        return $this->render('dashboard.html.twig', [
            'lastUsers' => $this->userRepository->findBy(['is_active' => true,'organisation'=>$organisation], null, 5),
            'lastAnnonces' => $this->annonceRepository->findBy(["deleted" => false ,'organisation'=> $organisation], null,100),
            'lastSonorisations' => $this->annonceSonoriseRepository->getLatest($organisation),
            'lastSonorisationsValidated' => $this->annonceSonoriseRepository->getLatestValide($organisation),
            'lastSonorisationsRejected' => $this->annonceSonoriseRepository->getLatestRejete($organisation),

            'sonorisationsValider' => $this->annonceSonoriseRepository->getAllSonorise($organisation),
            'sonorisationsRejected' => $this->annonceRepository->getAnnonceRejete($organisation),
        ]);
    }

    /**
     * @Route("/sonoriser", name="sonoriser")
     */
    public function sonoriser(Request $request)
    {
        if ($request->isMethod('POST')) {
            $this->addFlash("success", 'Enregistrement réussi  ');
            return $this->redirectToRoute('sonoriser');
        }
        return $this->render('deletable/sonorisation.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }
}
