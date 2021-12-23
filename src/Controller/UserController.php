<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\User\UserChangePasswordType;
use App\Form\User\UserProfileType;
use App\Form\UserType;
use App\Model\ChangePasswordModel;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use App\Service\AdminEmailService;
use App\Service\UserEmailService;
use App\Service\UserService;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{

    /**
     * @var UserEmailService
     */
    private $userEmailService;
    /**
     * @var ResetPasswordHelperInterface
     */
    private $resetPasswordHelper;
    /**
     * @var AdminEmailService
     */
    private $adminEmailService;

    public function __construct(UserEmailService             $userEmailService, EmailVerifier $emailVerifier,
                                ResetPasswordHelperInterface $resetPasswordHelper, AdminEmailService $adminEmailService)
    {
        $this->userEmailService = $userEmailService;
        $this->emailVerifier = $emailVerifier;
        $this->resetPasswordHelper = $resetPasswordHelper;
        $this->adminEmailService = $adminEmailService;
    }

    /**
     * @Route("/", name="user_index", methods={"GET"})
     */
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="user_new", methods={"GET","POST"})
     */
    public function new(Request $request, UserPasswordEncoderInterface $encoder, UserService $userService, UserRepository $userRepository): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $userExist = $userRepository->findOneBy(['email' => $user->getEmail()]);
            if ($userExist) {
                $this->addFlash("success", "Un utilisateur avec cet email existe deja ");
                goto RESUME;
            }

            $entityManager = $this->getDoctrine()->getManager();
//            foreach ($request->get('role_user') as $role) {
            $user->setRoles($request->get('role_user'));
            $user->setIsActive(true);
//            }
            $user->setPassword($encoder->encodePassword($user, "Passe1234"));
            if ($user->getIsOrganisationAdmin()) {
                $userService->setNewOrganisationAdmin($user);
            }
            $entityManager->persist($user);
            $entityManager->flush();

            $this->userEmailService->sendFirstResetPassword($user->getEmail(),$user);
            $this->adminEmailService->sendUserCreatedEmail($user->getEmail());
            return $this->redirectToRoute('user_index');
        }
        RESUME:
        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/profile", name="user_profile", methods={"GET","POST"})
     */
    public function profile(Request $request): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('user_profile');
        }

        return $this->render('user/profile.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{id}", name="user_show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="user_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, User $user, UserService $userService): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($user->getIsOrganisationAdmin()) {
                $userService->setNewOrganisationAdmin($user);
            }

            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_delete", methods={"DELETE"})
     */
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_index');
    }

    /**
     * @Route("/change/password", name="change_password", methods={"GET","POST"})
     */
    public function changePassword(Request $request, UserPasswordEncoderInterface $encoder): Response
    {

        $changePassword = new ChangePasswordModel();
        $form = $this->createForm(UserChangePasswordType::class, $changePassword);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            if (!$encoder->isPasswordValid($this->getUser(), $changePassword->getOldPassword())) {

                $form->get('oldPassword')->addError(new FormError('Ancien mot de passe erroné'));
                goto end;
            }

            $user = $this->getUser();
            $user->setPassword($encoder->encodePassword($user, $changePassword->getNewPassword()));
            $this->addFlash('success', 'Modification du mot de passe effectué avec succès');


            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('user_index');
        }

        end:
        return $this->render('security/changePassword.html.twig', [
            'changePasswwordForm' => $form->createView(),
        ]);

    }

    /**
     * @Route("/desactivation/{id}", name="user_desactivation", methods={"DELETE"})
     */
    public function desactivation_user(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('desactivation_utilisateur_user' . $user->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();
            $user->setIsActive(false);

            $this->addFlash("warning", "utilisateur desactiver avec succès");
            $entityManager->flush();
        }
        return $this->redirectToRoute('user_index');
    }


    /**
     * @Route("/administrateur/organisation/{id}", name="administrateur_organisation", methods={"DELETE"})
     */
    public function administrateur_organisation_user(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('administrateur_organisation_user' . $user->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();

            $organisation = $user->getOrganisation();
            $organisationUsers = $organisation->getuser()->getValues();


            foreach ($organisationUsers as $organisationUser) {
                $organisationUser->setIsOrganisationAdmin(false);
            }

            $user->setIsOrganisationAdmin(true);

            $this->addFlash("warning", "utilisateur  " . $user->getNom() . " " . $user->getPrenom() . " défini comme admnistrateur de l'organisation " . $organisation->getRaisonSocial());
            $entityManager->flush();
        }
        return $this->redirect($request->headers->get('referer'));
    }


    /**
     * @Route("/activation/{id}", name="user_activation", methods={"DELETE"})
     */
    public function activation_user(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('activation_utilisateur_user' . $user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $user->setIsActive(true);

            $this->addFlash("warning", "utilisateur activer avec succès");
            $entityManager->flush();
        }
        return $this->redirectToRoute('user_index');
    }

    private function processSendingPasswordResetEmail(string $emailFormData, MailerInterface $mailer): RedirectResponse
    {
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy([
            'email' => $emailFormData,
        ]);

        // Do not reveal whether a user account was found or not.
        if (!$user) {
            return $this->redirectToRoute('app_check_email');
        }

        try {
            $resetToken = $this->resetPasswordHelper->generateResetToken($user);
        } catch (ResetPasswordExceptionInterface $e) {
            // If you want to tell the user why a reset email was not sent, uncomment
            // the lines below and change the redirect to 'app_forgot_password_request'.
            // Caution: This may reveal if a user is registered or not.
            //
            // $this->addFlash('reset_password_error', sprintf(
            //     'There was a problem handling your password reset request - %s',
            //     $e->getReason()
            // ));

            return $this->redirectToRoute('app_check_email');
        }

        $email = (new TemplatedEmail())
            ->from(new Address('contact@crypto-services.us', 'Iso sono'))
            ->to($user->getEmail())
            ->subject('Your password reset request')
            ->htmlTemplate('reset_password/email.html.twig')
            ->context([
                'resetToken' => $resetToken,
            ]);

        $mailer->send($email);


        // Store the token object in session for retrieval in check-email route.
        $this->setTokenObjectInSession($resetToken);

        return $this->redirectToRoute('app_check_email');
    }
}
