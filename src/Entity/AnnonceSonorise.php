<?php

namespace App\Entity;

use App\Repository\AnnonceSonoriseRepository;
use App\Service\ConstantClass;
use App\TraitClass\EntityDateTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AnnonceSonoriseRepository::class)
 */
class AnnonceSonorise
{
    use EntityDateTrait;
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Annonce::class)
     */
    private $annonce;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $url_sono;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $reference;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type_bouton;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $annonce_url;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $statutValidation;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="annonceSonorises")
     */
    private $user;

    /**
     * @ORM\OneToOne(targetEntity=ApiAnnonceStats::class, mappedBy="annonceSonorise", cascade={"persist", "remove"})
     */
    private $apiAnnonceStats;

    public function __construct()
    {
        $this->url_sono ='';
        $this->statutValidation ='';
        $this->createdAt =new \DateTime();
        $this->updatedAt =new \DateTime();
        $this->deleted =false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAnnonce(): ?Annonce
    {
        return $this->annonce;
    }

    public function setAnnonce(?Annonce $annonce): self
    {
        $this->annonce = $annonce;

        return $this;
    }

    public function getUrlSono(): ?string
    {
        return $this->url_sono;
    }

    public function setUrlSono(?string $url_sono): self
    {
        $this->url_sono = $url_sono;

        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function getTypeBouton(): ?string
    {
        return $this->type_bouton;
    }

    public function setTypeBouton(string $type_bouton): self
    {
        $this->type_bouton = $type_bouton;

        return $this;
    }

    public function getAnnonceUrl(): ?string
    {
        return $this->annonce_url;
    }

    public function setAnnonceUrl(string $annonce_url): self
    {
        $this->annonce_url = $annonce_url;

        return $this;
    }

    public function getStatutValidation(): ?string
    {


        if ($this->statutValidation ==""){
            return ConstantClass::pending;
        }
        return $this->statutValidation;
    }

    public function setStatutValidation(string $statutValidation): self
    {
        $this->statutValidation = $statutValidation;

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

    public function getApiAnnonceStats(): ?ApiAnnonceStats
    {
        return $this->apiAnnonceStats;
    }

    public function setApiAnnonceStats(ApiAnnonceStats $apiAnnonceStats): self
    {
        // set the owning side of the relation if necessary
        if ($apiAnnonceStats->getAnnonceSonorise() !== $this) {
            $apiAnnonceStats->setAnnonceSonorise($this);
        }

        $this->apiAnnonceStats = $apiAnnonceStats;

        return $this;
    }
}
