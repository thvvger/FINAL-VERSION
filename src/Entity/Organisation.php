<?php

namespace App\Entity;

use App\TraitClass\EntityDateTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OrganisationRepository")
 * @Vich\Uploadable
 */
class Organisation implements \Serializable
{
    use EntityDateTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=200,nullable=true )
     */
    private $raison_social;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $num_entreprise;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $url;

    /**
     * @ORM\Column(type="integer")
     */
    private $limite_requete;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type_bouton;

    /**
     * @ORM\Column(type="integer")
     */
    private $nombre_utilisateur;


    /**
     * @ORM\Column(type="string", length=255,nullable=true)
     */
    private $logo_image;

    /**
     * @ORM\Column(type="text")
     */
    private $biographie;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\User", mappedBy="organisation")
     */
    private $user;


    /**
     * @Vich\UploadableField(mapping="organisation_images", fileNameProperty="logo_image")
     * @var File
     */
    private $imageFile;
    /**
     * @Vich\UploadableField(mapping="background_organisation_images", fileNameProperty="background_organisation")
     * @var File
     */
    private $backgroundImageFile;

    /**
     * @ORM\OneToMany(targetEntity=Annonce::class, mappedBy="organisation")
     */
    private $annonces;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $reference;

    /**
     * @ORM\ManyToOne(targetEntity=OrganisationSecteurActivite::class)
     */
    private $secteur_activite;

    /**
     * @ORM\Column(type="string", length=255,nullable=true)
     */
    private $background_organisation;

    /**
     * @ORM\Column(type="text", length=255)
     */
    private $selecteur;

    /**
     * @ORM\OneToMany(targetEntity=Partenaire::class, mappedBy="organisation", orphanRemoval=true)
     */
    private $partenaires;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $typeSonorisation;

    /**
     * @ORM\Column(type="string", length=255,nullable=true)
     */
    private $apiLink;

    public function __construct()
    {

        $this->deleted = false;
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
        $this->deleted = false;
        $this->user = new ArrayCollection();
        $this->annonces = new ArrayCollection();
        $this->partenaires = new ArrayCollection();
        $this->nombre_utilisateur = 0;
        $this->selecteur = "document.querySelector('#ref').textContent";
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRaisonSocial(): ?string
    {
        return $this->raison_social;
    }

    public function setRaisonSocial(string $raison_social): self
    {
        $this->raison_social = $raison_social;

        return $this;
    }

    public function getNumEntreprise(): ?string
    {
        return $this->num_entreprise;
    }

    public function setNumEntreprise(string $num_entreprise): self
    {
        $this->num_entreprise = $num_entreprise;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getLimiteRequete(): ?int
    {
        return $this->limite_requete;
    }

    public function setLimiteRequete(int $limite_requete): self
    {
        $this->limite_requete = $limite_requete;

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


    public function getLogoImage(): ?string
    {
        return $this->logo_image;
    }

    public function setLogoImage(?string $logo_image): self
    {
        $this->logo_image = $logo_image;

        return $this;
    }

    public function getBiographie(): ?string
    {
        return $this->biographie;
    }

    public function setBiographie(string $biographie): self
    {
        $this->biographie = $biographie;

        return $this;
    }

    public function adduser(User $user): self
    {
        if (!$this->user->contains($user)) {
            $this->user[] = $user;
            $user->setOrganisation($this);
        }

        return $this;
    }

    public function removeuser(User $user): self
    {
        if ($this->user->contains($user)) {
            $this->user->removeElement($user);
            // set the owning side to null (unless already changed)
            if ($user->getOrganisation() === $this) {
                $user->setOrganisation(null);
            }
        }

        return $this;
    }

    public function getCurrentAdmin()
    {

        $users = $this->getuser()->getValues();

        foreach ($users as $element) {
            if (true === $element->getIsOrganisationAdmin()) {
                return $element;
            }
        }
        return false;

    }

    /**
     * @return Collection|User[]
     */
    public function getuser(): Collection
    {
        return $this->user;
    }

    public function __toString()
    {
        return (string)$this->raison_social;
    }

    /**
     * @return Collection|Annonce[]
     */
    public function getAnnonces(): Collection
    {
        return $this->annonces;
    }

    /**
     *
     */
    public function getAnnoncesTextes()
    {
        return $this->annonces->getValues();
    }

    /**
     *
     */
    public function getAnnoncesSonnorises()
    {
        return $this->annonces->filter(function (Annonce $annonce) {
            return $annonce->getEstSonorise() == true;
        })->getValues();

    }

    public function addAnnonce(Annonce $annonce): self
    {
        if (!$this->annonces->contains($annonce)) {
            $this->annonces[] = $annonce;
            $annonce->setOrganisation($this);
        }

        return $this;
    }

    public function removeAnnonce(Annonce $annonce): self
    {
        if ($this->annonces->removeElement($annonce)) {
            // set the owning side to null (unless already changed)
            if ($annonce->getOrganisation() === $this) {
                $annonce->setOrganisation(null);
            }
        }

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

    public function getSecteurActivite(): ?OrganisationSecteurActivite
    {
        return $this->secteur_activite;
    }

    public function setSecteurActivite(?OrganisationSecteurActivite $secteur_activite): self
    {
        $this->secteur_activite = $secteur_activite;

        return $this;
    }

    public function getBackgroundOrganisation(): ?string
    {
        return $this->background_organisation;
    }

    public function setBackgroundOrganisation(?string $background_organisation): self
    {
        $this->background_organisation = $background_organisation;

        return $this;
    }

    public function getImageFile()
    {
        return $this->imageFile;
    }

    public
    function setImageFile(?File $image = null)
    {
        $this->imageFile = $image;

        // VERY IMPORTANT:
        // It is required that at least one field changes if you are using Doctrine,
        // otherwise the event listeners won't be called and the file is lost
        if ($image) {
            // if 'updatedAt' is not defined in your entity, use another property
            $this->updatedAt = new \DateTime('now');
        }
    }

    /**
     * @return File
     */
    public
    function getBackgroundImageFile(): ?File
    {
        return $this->backgroundImageFile;
    }

    /**
     * @param File $backgroundImageFile
     */
    public
    function setBackgroundImageFile(?File $backgroundImageFile = null)
    {
        if ($backgroundImageFile) {
            // if 'updatedAt' is not defined in your entity, use another property
            $this->updatedAt = new \DateTime('now');
        }
        $this->backgroundImageFile = $backgroundImageFile;
    }

    public function getSelecteur(): ?string
    {
        return $this->selecteur;
    }

    public
    function setSelecteur(string $selecteur): self
    {
        $this->selecteur = $selecteur;

        return $this;
    }

    /**
     * String representation of object
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     * @since 5.1.0
     */
    public
    function serialize()
    {
        return serialize(array(
            $this->id,
            $this->raison_social,
            // see section on salt below
            // $this->salt,
        ));
    }

    /**
     * Constructs the object
     * @link http://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized <p>
     * The string representation of the object.
     * </p>
     * @return void
     * @since 5.1.0
     */
    public
    function unserialize($serialized)
    {
        list (
            $this->id,
            $this->raison_social,
            ) = unserialize($serialized);
    }

    /**
     * @return Collection|Partenaire[]
     */
    public function getPartenaires(): Collection
    {
        return $this->partenaires;
    }

    public function addPartenaire(Partenaire $partenaire): self
    {
        if (!$this->partenaires->contains($partenaire)) {
            $this->partenaires[] = $partenaire;
            $partenaire->setOrganisation($this);
        }

        return $this;
    }

    public function removePartenaire(Partenaire $partenaire): self
    {
        if ($this->partenaires->removeElement($partenaire)) {
            // set the owning side to null (unless already changed)
            if ($partenaire->getOrganisation() === $this) {
                $partenaire->setOrganisation(null);
            }
        }

        return $this;
    }

    public function getTypeSonorisation(): ?string
    {
        return $this->typeSonorisation;
    }

    public function setTypeSonorisation(string $typeSonorisation): self
    {
        $this->typeSonorisation = $typeSonorisation;

        return $this;
    }

    public function getNombreUtilisateur(): ?int
    {
        return $this->nombre_utilisateur;
    }

    public function setNombreUtilisateur(int $nombre_utilisateur): self
    {
        $this->nombre_utilisateur = $nombre_utilisateur;

        return $this;
    }

    public function getApiLink(): ?string
    {
        return $this->apiLink;
    }

    public function setApiLink(string $apiLink): self
    {
        $this->apiLink = $apiLink;

        return $this;
    }

}
