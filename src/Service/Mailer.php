<?php
/**
 * Created by PhpStorm.
 * User: giorgiopagnoni
 * Date: 03/07/16
 * Time: 14:24
 */

namespace App\Service;

use App\Entity\User;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class Mailer
{
    protected $mailer;
    protected $router;
    protected $twig;
    protected $logger;
    protected $noreply;

    /**
     * Mailer constructor.
     * @param \Swift_Mailer $mailer
     * @param RouterInterface $router
     * @param \Twig_Environment $twig
     * @param LoggerInterface $logger
     * @param string $noreply
     */
    public function __construct(\Swift_Mailer $mailer, RouterInterface $router, \Twig_Environment $twig, LoggerInterface $logger, $noreply)
    {
        $this->mailer = $mailer;
        $this->router = $router;
        $this->twig = $twig;
        $this->logger = $logger;
        $this->noreply = $noreply;
    }

    /**
     * @param User $user
     * @throws \Exception
     * @throws \Throwable
     */
    public function sendActivationEmailMessage(User $user)
    {


        $email = (new TemplatedEmail())
            ->from(new Address('contact@crypto-services.us', 'Iso sono'))
            ->to($user->getEmail())
            ->subject('Your password reset request')
            ->htmlTemplate('reset_password/email.html.twig')
            ->context([
                'resetToken' => $resetToken,
            ]);

        $mailer->send($email);
        $url = $this->router->generate('user_activate', ['token' => $user->getToken()], UrlGeneratorInterface::ABSOLUTE_URL);

        $context = [
            'user' => $user,
            'activationUrl' => $url
        ];

        $this->sendMessage('user/email/register-done.html.twig', $context, $this->noreply, $user->getEmail());
    }

    protected function sendMessage($templateName, $context, $fromEmail, $toEmail)
    {
        $context = $this->twig->mergeGlobals($context);
        $template = $this->twig->load($templateName);
        $subject = $template->renderBlock('subject', $context);
        $textBody = $template->renderBlock('body_text', $context);
        $htmlBody = $template->renderBlock('body_html', $context);

        $message = (new \Swift_Message('Reinitialisation de mot de passe'))
            ->setSubject($subject)
            ->setFrom($fromEmail)
            ->setTo($toEmail);


        if (!empty($htmlBody)) {
            $message->setBody($htmlBody, 'text/html')->addPart($textBody, 'text/plain');
        } else {
            $message->setBody($textBody);
        }
        $result = $this->mailer->send($message);

        $log_context = ['to' => $toEmail, 'message' => $textBody, 'template' => $templateName];
        if ($result) {
            $this->logger->info('SMTP email sent', $log_context);
        } else {
            $this->logger->error('SMTP email error', $log_context);
        }
        return $result;
    }

    public function accesSendMessage($templateName, $context, $fromEmail, $toEmail)
    {
        $this->sendMessage($templateName, $context, $fromEmail, $toEmail);
    }

    /**
     * @param User $user
     * @throws \Exception
     * @throws \Throwable
     */
    public function sendResetPasswordEmailMessage(User $user)
    {
        $url = $this->router->generate('user_password_reset', ['token' => $user->getToken()], UrlGeneratorInterface::ABSOLUTE_URL);

        $context = [
            'user' => $user,
            'resetPasswordUrl' => $url,
        ];

        $this->sendMessage('user/email/request-password.html.twig', $context, $this->noreply, $user->getEmail());
    }

    public function sendMessageNew()
    {

        $message = (new \Swift_Message('Hello Email'))
            ->setFrom('renaudrogelio@gmail.com')
            ->setTo('renaudstrifler@gmail.com')
            ->setBody("Testsdsdsdsds Email", 'text/html');
        $this->mailer->send($message);

    }
}