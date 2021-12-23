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
use App\Security\PermissionName;
use App\Service\UserServices;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GrantAllPermissionsToARoleCommand extends Command
{
    protected static $defaultName = 'app:grant-permissions-role';

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

            ->setDescription("Affecter toutes les permissions à un role")

            ->setHelp("Cette commande permet de lier toutes les permissions à un role")

            ->addArgument('role', InputArgument::REQUIRED, "Role")

            ;

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $roleInput = trim($input->getArgument('role'));

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

        $allPermissions = $this->em->getRepository(Permission::class)->findAll();

        $nbReussis = 0;
        foreach ($allPermissions as $permission)
        {
            $rolePermissionExiste = $this->em->getRepository(RolePermission::class)->findOneBy([
                'role' => $role,
                'permission' => $permission,
                'deleted' => false,
            ]);
            if ($rolePermissionExiste == null)
            {
                $rolePermission = new RolePermission();
                $rolePermission->setRole($role)
                    ->setPermission($permission)
                    ;
                $this->em->persist($rolePermission);
                $nbReussis++;
            }
        }

        $this->em->flush();
        $output->writeln([
            "$nbReussis permission(s) accordée(s) avec succès",
        ]);


        return 0;
    }


}