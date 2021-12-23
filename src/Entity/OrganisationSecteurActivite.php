<?php

namespace App\Entity;

use App\Repository\OrganisationSecteurActiviteRepository;
use App\TraitClass\EntityDateTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OrganisationSecteurActiviteRepository::class)
 */
class OrganisationSecteurActivite
{
    use EntityDateTrait;
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $designation;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDesignation(): ?string
    {
        return $this->designation;
    }

    public function setDesignation(string $designation): self
    {
        $this->designation = $designation;

        return $this;
    }

    public function __toString()
    {
        return (string)$this->getDesignation();
    }
}
