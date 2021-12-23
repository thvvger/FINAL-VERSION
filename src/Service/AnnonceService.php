<?php
/**
 * Created by PhpStorm.
 * User: LANGANFIN  Rogelio
 * Date: 28/04/2021
 * Time: 10:02
 */

namespace App\Service;


use App\Repository\AnnonceRepository;
use App\Repository\AnnonceSonoriseRepository;
use Doctrine\ORM\EntityManagerInterface;

class AnnonceService
{

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var AnnonceRepository
     */
    private $annonceRepository;
    /**
     * @var AnnonceSonoriseRepository
     */
    private $annonceSonoriseRepository;

    public function __construct(AnnonceRepository $annonceRepository, AnnonceSonoriseRepository $annonceSonoriseRepository
    )
    {
        $this->annonceRepository = $annonceRepository;
        $this->annonceSonoriseRepository = $annonceSonoriseRepository;
    }

    public function getPercentageAnnonceSonoriseVsAnnonceUploader($user)
    {
        $allAnnonce = $this->annonceRepository->count(['deleted' => 0 ,'organisation' => $user->getOrganisation()]);
        $annonceSonorise = $this->annonceRepository->count(['deleted' => 0, "estSonorise" => true,  'organisation' => $user->getOrganisation()]);

        return $allAnnonce > 0 ? ($annonceSonorise * 100) / $allAnnonce : 0;

    }

    public function getPercentageAnnonceSonoriseVsAnnonceSonoriseRefuse()
    {
        $annonceSonorise = $this->annonceRepository->count(['deleted' => 0, "estSonorise" => true]);
        $annonceSonoriseRejete = $this->annonceRepository->count(['deleted' => 0, "estSonorise" => true, "estValider" => false]);
        return $annonceSonorise > 0 ? ($annonceSonoriseRejete * 100) / $annonceSonorise : 0;

    }

    public function getBestUserUploader()
    {
        $allUserWork = $this->annonceSonoriseRepository->findOneBySomeField();

        $savedMaxNumber = 0;
        $userName = '';
        $prenom=" ";
        foreach ($allUserWork as $currentUser) {
            if ($currentUser[1] > $savedMaxNumber) {
                $savedMaxNumber = $currentUser[1];
                $userName = $currentUser["nom"];
                $prenom = $currentUser["prenom"];
            }
        }

        return $userName." ".$prenom[0].".";
    }

}