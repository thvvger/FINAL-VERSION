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

class UserEmailService
{

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
     * @var ContainerInterface
     */
    private $container;
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(ResetPasswordHelperInterface $resetPasswordHelper, MailerInterface $mailer, EntityManagerInterface $container, UserRepository $userRepository
    )
    {
        $this->resetPasswordHelper = $resetPasswordHelper;
        $this->mailer = $mailer;
        $this->container = $container;
        $this->userRepository = $userRepository;
    }

    public function sendFirstResetPassword($emailFormData)
    {


        $user = $this->userRepository->findOneBy([
            'email' => $emailFormData,
        ]);

        if (!$user) {
            return false;
        }
        try {
            $resetToken = $this->resetPasswordHelper->generateResetToken($user);
        } catch (ResetPasswordExceptionInterface $e) {

            return $this->redirectToRoute('app_check_email');
        }
        $email = (new TemplatedEmail())
            ->from(new Address('contact@crypto-services.us', 'Iso sono'))
            ->to($user->getEmail())
            ->subject($user->getOrganisation()->getRaisonSocial() . " | Bienvenue sur ISO-SONO !")
            ->htmlTemplate('email/user/first_reset.html.twig')
            ->context([
                'resetToken' => $resetToken,
                'user' => $user,
            ]);

        $this->mailer->send($email);
    }

    public function sendResetPassword($emailFormData)
    {
        $user = $this->userRepository->findOneBy([
            'email' => $emailFormData,
        ]);

        if (!$user) {
            return false;
        }
        try {
            $resetToken = $this->resetPasswordHelper->generateResetToken($user);
        } catch (ResetPasswordExceptionInterface $e) {

            return $this->redirectToRoute('app_check_email');
        }
        $email = (new TemplatedEmail())
            ->from(new Address('contact@crypto-services.us', 'Iso sono'))
            ->to($user->getEmail())
            ->subject($user->getOrganisation()->getRaisonSocial() . " | Bienvenue sur ISO-SONO !")
            ->htmlTemplate('reset_password/email.html.twig')
            ->context([
                'resetToken' => $resetToken,
            ]);

        $this->mailer->send($email);


    }

    public function sendAllSonorisationDone($emailFormData,$annoncesHolder)
    {

        $user = $this->userRepository->findOneBy([
            'email' => $emailFormData,
        ]);


        $email = (new TemplatedEmail())
            ->from(new Address(self::SENDER_EMAIL, 'Iso sono'))
            ->to($user->getEmail())
            ->subject(" Sonorisation groupée")
            ->htmlTemplate('email/user/new_group_sonorisation.html.twig')
            ->context([
                "annonceSonnoriser" => $annoncesHolder,
                "user" => $user,
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
            ->to($user->getEmail())
            ->subject($user->getFullname() . " " . $user->getOrganisation()->getNumEntreprise() . " Nouvelle sonorisation!")
            ->htmlTemplate('email/user/new_sono.html.twig')
            ->context([
                'user' => $user,
                'annonceSonorise' => $annonceSonorise,
            ]);

        $this->mailer->send($email);

    }



}