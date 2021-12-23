<?php

namespace App\Entity;

use App\Repository\AnnonceRepository;
use App\TraitClass\EntityDateTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AnnonceRepository::class)
 */
class Annonce
{
    use EntityDateTrait;
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Organisation::class, inversedBy="annonces")
     */
    private $organisation;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $titre;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="text")
     */
    private $profil_recherche;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $reference;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     */
    private $user;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_expiration;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $status;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $format;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $typeContrat;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $remuneration;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $niveauEtude;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lieu;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $salaire;

    /**
     * @ORM\Column(type="string", length=255,nullable=true)
     */
    private $dureeInterim;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $experienceProfessionnel;

    /**
     * @ORM\Column(type="boolean")
     */
    private $estSonorise;

    /**
     * @ORM\Column(type="boolean",nullable=true)
     */
    private $estValider;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=true)
     */
    private $validerPar;

    /**
     * @ORM\Column(type="string", length=255,nullable=true)
     */
    private $annonceSonoLinkHolder;


    public function __construct()
    {
        $this->status ="pending";
        $this->format ="";
        $this->createdAt  =  new \DateTime();
        $this->updatedAt  =  new \DateTime();
        $this->deleted  =  false;
        $this->estValider  =  null;
        $this->estSonorise  =  false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrganisation(): ?Organisation
    {
        return $this->organisation;
    }

    public function setOrganisation(?Organisation $organisation): self
    {
        $this->organisation = $organisation;

        return $this;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getProfilRecherche(): ?string
    {
        return $this->profil_recherche;
    }

    public function setProfilRecherche(string $profil_recherche): self
    {
        $this->profil_recherche = $profil_recherche;

        return $this;
    }
    public function __toString()
    {
        return  (string)$this->titre;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(?string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getDateExpiration()
    {
        return $this->date_expiration;
    }

    public function setDateExpiration( $date_expiration): self
    {
        $this->date_expiration = $date_expiration;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getFormat(): ?string
    {
        return $this->format;
    }

    public function setFormat(string $format): self
    {
        $this->format = $format;

        return $this;
    }

    public function getTypeContrat(): ?string
    {
        return $this->typeContrat;
    }

    public function setTypeContrat(string $typeContrat): self
    {
        $this->typeContrat = $typeContrat;

        return $this;
    }

    public function getRemuneration(): ?string
    {
        return $this->remuneration;
    }

    public function setRemuneration(string $remuneration): self
    {
        $this->remuneration = $remuneration;

        return $this;
    }

    public function getNiveauEtude(): ?string
    {
        return $this->niveauEtude;
    }

    public function setNiveauEtude(string $niveauEtude): self
    {
        $this->niveauEtude = $niveauEtude;

        return $this;
    }

    public function getLieu(): ?string
    {
        return $this->lieu;
    }

    public function setLieu(string $lieu): self
    {
        $this->lieu = $lieu;

        return $this;
    }

    public function getSalaire(): ?string
    {
        return $this->salaire;
    }

    public function setSalaire(string $salaire): self
    {
        $this->salaire = $salaire;

        return $this;
    }

    public function getDureeInterim(): ?string
    {
        return $this->dureeInterim;
    }

    public function setDureeInterim(string $dureeInterim): self
    {
        $this->dureeInterim = $dureeInterim;

        return $this;
    }

    public function getExperienceProfessionnel(): ?string
    {
        return $this->experienceProfessionnel;
    }

    public function setExperienceProfessionnel(?string $experienceProfessionnel): self
    {
        $this->experienceProfessionnel = $experienceProfessionnel;

        return $this;
    }

    public function getEstSonorise(): ?bool
    {
        return $this->estSonorise;
    }

    public function setEstSonorise(bool $estSonorise): self
    {
        $this->estSonorise = $estSonorise;

        return $this;
    }

    public function getEstValider(): ?bool
    {
        return $this->estValider;
    }

    public function setEstValider(?bool $estValider): self
    {
        $this->estValider = $estValider;

        return $this;
    }

    public function getValiderPar(): ?User
    {
        return $this->validerPar;
    }

    public function setValiderPar(?User $validerPar): self
    {
        $this->validerPar = $validerPar;

        return $this;
    }

    public function getAnnonceSonoLinkHolder(): ?string
    {
        return $this->annonceSonoLinkHolder;
    }

    public function setAnnonceSonoLinkHolder(string $annonceSonoLinkHolder): self
    {
        $this->annonceSonoLinkHolder = $annonceSonoLinkHolder;

        return $this;
    }
}
