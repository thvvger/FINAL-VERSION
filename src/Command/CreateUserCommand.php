<?php
/**
 * Created by PhpStorm.
 * User: OSSPO DEV
 * Date: 20/07/2020
 * Time: 15:18
 */

namespace App\Command;


use App\Entity\User;
use App\Service\UserServices;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CreateUserCommand extends Command
{
    protected static $defaultName = 'app:create-user';

    private $em;
    private $userServices;
    private $passwordEncoder;

    public function __construct(?string $name = null, EntityManagerInterface $entityManager,
                                UserServices $userServices, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->em = $entityManager;
        $this->userServices = $userServices;
        $this->passwordEncoder = $passwordEncoder;
        parent::__construct($name);
    }


    protected function configure()
    {
        $this

            ->setDescription("Créer un nouveau utilisateur")

            ->setHelp("Cette commande permet de créer un nouveau utilisateur")

            ->addArgument('email', InputArgument::REQUIRED, "Email")
            ->addArgument('password', InputArgument::REQUIRED, "Mot de passe")
            ->addArgument('passwordConfirm', InputArgument::REQUIRED, "Confirmez le mot de passe")
            ->addArgument('nom', InputArgument::REQUIRED, "Nom")
            ->addArgument('prenom', InputArgument::REQUIRED, "Prénom")

            ;

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $email = trim($input->getArgument('email')) ;
        $password = trim($input->getArgument('password')) ;
        $passwordConfirm = trim($input->getArgument('passwordConfirm')) ;
        $nom = trim($input->getArgument('nom')) ;
        $prenom = trim($input->getArgument('prenom')) ;

        if ($password != $passwordConfirm)
        {
            $output->writeln([
                'Le mots de passe ne correspondent pas. Veuillez réésayer',
            ]);
            return 1;
        }

        $userExiste = $this->em->getRepository(User::class)->findOneBy([
            'email' => $email,
        ]);
        if ($userExiste != null)
        {
            $output->writeln([
                'Un utilisateur existe déjà avec ce même email',
            ]);
            return 1;
        }

        $user = new User();
        $user->setEmail($email)
            ->setNom($nom)
            ->setPrenom($prenom)
            ->setPassword(
                $this->passwordEncoder->encodePassword(
                    $user,
                    $password
                )
            )

            ;

        $this->em->persist($user);
        $this->em->flush();

        $output->writeln('Utilisateur créé avec succès');

        return 0;
    }

}