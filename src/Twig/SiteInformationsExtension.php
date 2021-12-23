<?php
/**
 * Created by PhpStorm.
 * User: LANGANFIN  Rogelio
 * Date: 28/04/2021
 * Time: 09:51
 */

namespace App\Twig;

use App\Entity\Annonce;
use App\Entity\AnnonceSonorise;
use App\Entity\ApiAnnonceStats;
use App\Entity\User;
use App\Repository\AnnonceRepository;
use App\Repository\AnnonceSonoriseRepository;
use App\Repository\ApiAnnonceStatsRepository;
use App\Repository\NotificationRepository;
use App\Service\AnnonceService;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class SiteInformationsExtension extends AbstractExtension
{

    /**
     * @var NotificationRepository
     */
    private $notificationRepository;
    /**
     * @var AnnonceService
     */
    private $annonceService;
    /**
     * @var AnnonceRepository
     */
    private $annonceRepository;
    /**
     * @var RequestStack
     */
    private $requestStack;
    /**
     * @var AnnonceSonoriseRepository
     */
    private $annonceSonoriseRepository;
    /**
     * @var ApiAnnonceStatsRepository
     */
    private $annonceStatsRepository;

    public function __construct(NotificationRepository $notificationRepository,
                                AnnonceService         $annonceService,
                                ApiAnnonceStatsRepository $annonceStatsRepository,
                                AnnonceSonoriseRepository $annonceSonoriseRepository, AnnonceRepository $annonceRepository
        , RequestStack                                 $requestStack)
    {
        $this->notificationRepository = $notificationRepository;
        $this->annonceService = $annonceService;
        $this->annonceRepository = $annonceRepository;
        $this->requestStack = $requestStack;
        $this->annonceSonoriseRepository = $annonceSonoriseRepository;
        $this->annonceStatsRepository = $annonceStatsRepository;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('getBestUserUploader', [$this, 'getBestUserUploader']),
            new TwigFunction('getSiteHttpLink', [$this, 'getSiteHttpLink']),
            new TwigFunction('get_notifications', [$this, 'getNotifications']),
            new TwigFunction('get_percentage_annonce_sonorise_vs_annonce_uploader', [$this, 'getPercentageAnnonceSonoriseVsAnnonceUploader']),
            new TwigFunction('get_annonce_uploader', [$this, 'getAnnonceUploader']),
            new TwigFunction('get_annonce_valide', [$this, 'getAnnonceValide']),
            new TwigFunction('get_annonce_rejete', [$this, 'getAnnonceRejete']),
            new TwigFunction('get_annonce_pending', [$this, 'getAnnoncePending']),
            new TwigFunction('get_percentage_annonce_sonorise_vs_annonce_sonorise_refuse', [$this, 'getPercentageAnnonceSonoriseVsAnnonceSonoriseRefuse']),
            new TwigFunction('get_annone_stats', [$this, 'getAnnoneStats']),
            new TwigFunction('get_annonce_sonoriser_stat', [$this, 'getAnnonceSonoriserStat']),
        ];
    }

    public function getAnnoneStats(Annonce $annonce)
    {
        $annonceSonorise = $this->annonceSonoriseRepository->findOneBy(['annonce' => $annonce]);
//         dump($annonceSonorise);
//         return "ok";
        if ($annonceSonorise)
            return $annonceSonorise->getApiAnnonceStats();
        else
            return null;
    }

    public function getSiteHttpLink()
    {
        return $this->requestStack->getCurrentRequest()->getSchemeAndHttpHost();
    }

    public function getBestUserUploader()
    {

        return $this->annonceService->getBestUserUploader();
    }

    public function getNotifications()
    {
        return $this->notificationRepository->findBy([], ['id' => 'DESC'], 4, null);
    }

    public function getPercentageAnnonceSonoriseVsAnnonceUploader(User $user)
    {
        return round($this->annonceService->getPercentageAnnonceSonoriseVsAnnonceUploader($user), 2);
    }


    public function getPercentageAnnonceSonoriseVsAnnonceSonoriseRefuse()
    {
        return round($this->annonceService->getPercentageAnnonceSonoriseVsAnnonceSonoriseRefuse(), 2);
    }


    public function getAnnonceUploader(User $user)
    {
        return $this->annonceRepository->count(['deleted' => false,'organisation'=>$user->getOrganisation()]);
    }

    public function getAnnonceValide(User $user)
    {
        return $this->annonceRepository->count(['deleted' => false, "estValider" => true,'organisation'=>$user->getOrganisation()]);
    }

    public function getAnnonceRejete()
    {
        return $this->annonceRepository->count(['deleted' => false, "estValider" => false]);
    }

    public function getAnnoncePending(User $user)
    {
        return $this->annonceRepository->count(['status' => 'pending',"organisation"=>$user->getOrganisation()]);
    }
    public function getAnnonceSonoriserStat(AnnonceSonorise $annonceSonorise)
    {
        return $this->annonceStatsRepository->findOneBy(['annonceSonorise' => $annonceSonorise]);
    }

}