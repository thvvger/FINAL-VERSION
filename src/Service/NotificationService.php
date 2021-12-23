<?php
/**
 * Created by PhpStorm.
 * User: LANGANFIN  Rogelio
 * Date: 28/04/2021
 * Time: 10:02
 */

namespace App\Service;


use App\Entity\Notification;
use Doctrine\ORM\EntityManagerInterface;

class NotificationService
{

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function new_notification($titre, $contenu)
    {

        $notif = (new Notification())
            ->setTitre($titre)
            ->setContenu($contenu);

        $this->entityManager->persist($notif);

    }

}