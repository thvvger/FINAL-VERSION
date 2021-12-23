<?php
/**
 * Created by PhpStorm.
 * User: OSSPO DEV
 * Date: 20/07/2020
 * Time: 15:18
 */

namespace App\Command;


use App\Entity\Permission;
use App\Entity\Role;
use App\Entity\RolePermission;
use App\Entity\User;
use App\Entity\UserRole;
use App\Security\PermissionName;
use App\Service\UserServices;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GrantARoleToUserCommand extends Command
{
    protected static $defaultName = 'app:grant-role-user';

    private $em;
    private $userServices;

    public function __construct(?string $name = null, EntityManagerInterface $entityManager, UserServices $userServices)
    {
        $this->em = $entityManager;
        $this->userServices = $userServices;
        parent::__construct($name);
    }


    protected function configure()
    {
        $this

            ->setDescription("Affecter un role à un utilisateur")

            ->setHelp("Cette commande permet de lier un role à un utilisateur")

            ->addArgument('email', InputArgument::REQUIRED, "Email de l'utilisateur")
            ->addArgument('role', InputArgument::REQUIRED, "Role")

            ;

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $email = trim($input->getArgument('email'));
        $roleInput = trim($input->getArgument('role'));

        /**
         * @var $user User
         */
        $user = $this->em->getRepository(User::class)->findOneBy([
            'email' => $email
        ]);
        if ($user == null)
        {
            $output->writeln([
                "Cet utilisateur n'existe pas",
            ]);
            return 1;
        }


        /**
         * @var $role Role
         */
        $role = $this->em->getRepository(Role::class)->findOneBy([
            'name' => $roleInput,
            'deleted' => false,
        ]);
        if ($role == null)
        {
            $output->writeln([
                "Ce role n'existe pas.",
            ]);
            return 1;
        }

        $userRoleExiste = $this->em->getRepository(UserRole::class)->findOneBy([
            'user' => $user,
            'role' => $role,
        ]);
        if ($userRoleExiste != null)
        {
            $userRoleExiste->setDeleted(false);
        }
        else
        {
            $userRole = new UserRole();
            $userRole
                ->setRole($role)
                ->setUser($user)
            ;

            $this->em->persist($userRole);
        }

        $this->em->flush();

        $output->writeln([
            "Opération réussie",
        ]);


        return 0;
    }


}