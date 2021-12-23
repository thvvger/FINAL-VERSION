<?php
/**
 * Created by PhpStorm.
 * User: LANGANFIN  Rogelio
 * Date: 28/04/2021
 * Time: 10:02
 */

namespace App\Service;


use App\Entity\User;
use App\Repository\AnnonceRepository;
use App\Repository\AnnonceSonoriseRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;

class AdminEmailService
{

    const  ADMIN_EMAIL = "renaudstrifler@gmail.com";
    const  SENDER_EMAIL = "contact@crypto-services.us";
    /**
     * @var ResetPasswordHelperInterface
     */
    private $resetPasswordHelper;
    /**
     * @var MailerInterface
     */
    private $mailer;
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(ResetPasswordHelperInterface $resetPasswordHelper, MailerInterface $mailer, UserRepository $userRepository
    )
    {
        $this->resetPasswordHelper = $resetPasswordHelper;
        $this->mailer = $mailer;
        $this->userRepository = $userRepository;
    }

    public function sendUserCreatedEmail($emailFormData)
    {
        $user = $this->userRepository->findOneBy([
            'email' => $emailFormData,
        ]);
        if (!$user) {
            return false;
        }

        $email = (new TemplatedEmail())
            ->from(new Address(self::SENDER_EMAIL, 'Iso sono'))
            ->to(self::ADMIN_EMAIL)
            ->subject(" Nouvelle inscription " . $user->getOrganisation() . " " . $user->getOrganisation()->getNumEntreprise())
            ->htmlTemplate('email/admin/user_created.html.twig')
            ->context([
                'user' => $user,
            ]);

        $this->mailer->send($email);


    }


    public function sendNewRequestPasswordEmailAdmin($emailFormData)
    {
        $user = $this->userRepository->findOneBy([
            'email' => $emailFormData,
        ]);
        $email = (new TemplatedEmail())
            ->from(new Address(self::SENDER_EMAIL, 'Iso sono'))
            ->to(self::ADMIN_EMAIL)
            ->subject($user->getFullname() . " " . $user->getOrganisation()->getNumEntreprise() . " Nouvelle demande de reinitialisat")
            ->htmlTemplate('email/admin/new_request_password.html.twig')
            ->context([
                'user' => $user,
            ]);

        $this->mailer->send($email);

    }

    public function sendAllSonorisationDonePercentage($annoncesHolder)
    {


        $email = (new TemplatedEmail())
            ->from(new Address(self::SENDER_EMAIL, 'Iso sono'))
            ->to(self::ADMIN_EMAIL)
            ->subject(" Sonorisation groupée")
            ->htmlTemplate('email/admin/new_group_sonorisation.html.twig')
            ->context([
                "annonceSonnoriser" => $annoncesHolder
            ]);

        $this->mailer->send($email);

    }

    public function sendNewSonorisation($emailFormData, $annonceSonorise)
    {
        $user = $this->userRepository->findOneBy([
            'email' => $emailFormData,
        ]);
        $email = (new TemplatedEmail())
            ->from(new Address(self::SENDER_EMAIL, 'Iso sono'))
            ->to(self::ADMIN_EMAIL)
            ->subject($user->getFullname() . " " . $user->getOrganisation()->getNumEntreprise() . " Nouvelle sonorisation!")
            ->htmlTemplate('email/admin/new_sono.html.twig')
            ->context([
                'user' => $user,
                'annonceSonorise' => $annonceSonorise,
            ]);

        $this->mailer->send($email);

    }


}