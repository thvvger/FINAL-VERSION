<?php
/**
 * Created by PhpStorm.
 * User: OSSPO DEV
 * Date: 20/07/2020
 * Time: 15:18
 */

namespace App\Command;


use App\Entity\Permission;
use App\Security\PermissionName;
use App\Service\UserServices;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GeneratePermissionsCommand extends Command
{
    protected static $defaultName = 'app:generate-permissions';

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

            ->setDescription("Generer les permissions des tables")

            ->setHelp("Cette commande permet de générer automatiquement les permissions des tables de la base de données")

            ;

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $entities = $this->em->getConfiguration()->getMetadataDriverImpl()->getAllClassNames();

        $output->writeln([
            'Génération des permissions pour ' . count($entities) . ' table(s)',
            '============',
            '',
        ]);

        foreach ($entities as $entity)
        {
          //  if ($entity instanceof )
            $this->createPermissionsForTable($entity);
        }

        $output->writeln('Gérération terminée!');


        return 0;
    }

    public function createPermissionsForTable($table)
    {
        $cruds = [PermissionName::CREATE, PermissionName::UPDATE, PermissionName::SHOW, PermissionName::SHOW_ALL, PermissionName::DELETE];

        foreach ($cruds as $crud)
        {
            $permissionString = $crud . '_' . $table;

            if (!$this->checkIfPermissionExists($table, $crud))
            {
                $permission = new Permission();
                $permission->setPermission($permissionString);
                $permission->setPermission($permissionString);
                $permission->setDescription($this->getDescriptionCrud($crud) . ' ' . $this->getClassName($table));
                $permission->setVisible(true);

                $this->em->persist($permission);
            }

        }

        $this->em->flush();
    }

    public function checkIfPermissionExists($table, $crud)
    {
        $permissionString = $crud . '_' . $table ;
        $permission = $this->em->getRepository(Permission::class)->findOneBy([
            'permission' => $permissionString
        ]);

        return ($permission != null);
    }

    public function getDescriptionCrud($crud)
    {
        $description = "";
        switch ($crud)
        {
            case PermissionName::CREATE:
                $description = 'Création';
                break;

            case PermissionName::UPDATE:
                $description = 'Modification';
                break;

            case PermissionName::SHOW:
                $description = 'Affichage';
                break;

            case PermissionName::SHOW_ALL:
                $description = 'Liste';
                break;

            case PermissionName::DELETE:
                $description = 'Suppression';
                break;
        }

        return $description;

    }

    public function getClassName($className)
    {
        $classNameArray = explode("\\", $className);

        if (!empty($classNameArray))
        {
            return $classNameArray[ count($classNameArray) -1 ];
        }
        else
        {
            return '';
        }
    }

}