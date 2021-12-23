<?php
/**
 * Created by PhpStorm.
 * User: LANGANFIN  Rogelio
 * Date: 28/04/2021
 * Time: 09:51
 */

namespace App\Twig;

use App\Entity\User;
use App\Repository\NotificationRepository;
use App\Service\UserService;
use App\Utils\Constants;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class NotificationExtension  extends AbstractExtension
{

    /**
     * @var NotificationRepository
     */
    private $notificationRepository;

    public function __construct(NotificationRepository $notificationRepository,UserService $userService)
    {
        $this->notificationRepository = $notificationRepository;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('get_notifications', [$this, 'getNotifications']),
            new TwigFunction('checkIfUserHasPermission', [$this, 'checkIfUserHasPermission']),
        ];
    }

    public function getNotifications()
    {
        return $this->notificationRepository->findBy([], ['id' => 'DESC'], 4, null);
    }
    public function checkIfUserHasPermission(User $user,$menu)
    {
        return  in_array($menu,Constants::MENU_MANAGER[$user->getUserRole()->getCodeRole()]  );

    }

}