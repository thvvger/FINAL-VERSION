<?php

namespace App\Entity;

use App\TraitClass\EntityDateTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Ignore;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 *  @Vich\Uploadable
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 */
class User implements UserInterface,\Serializable
{
    use EntityDateTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Organisation", inversedBy="fonction")
     */
    private $organisation;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $fonction;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $prenom;


    /**
     * @ORM\Column(type="string", length=255,nullable=true)
     */
    private $image;


    /**
     * @Vich\UploadableField(mapping="user_images", fileNameProperty="image")
     * @var File
     * @Ignore()
     */
    private $imageFile;


    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $is_active;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $telephone;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isOrganisationAdmin;

    /**
     * @ORM\Column(type="datetime",nullable=true)
     */
    private $dateDerniereConnexion;

    /**
     * @ORM\OneToMany(targetEntity=AnnonceSonorise::class, mappedBy="user")
     */
    private $annonceSonorises;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isVerified = false;

    /**
     * @ORM\ManyToOne(targetEntity=Role::class, inversedBy="users")
     */
    private $userRole;




    public function __construct()
    {

        $this->deleted  =  false;
        $this->createdAt  =  new \DateTime();
        $this->dateDerniereConnexion  =  new \DateTime();
        $this->isOrganisationAdmin = false;
        $this->annonceSonorises = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
//        $roles[] = 'ROLE_USER';
//        dd($roles);
        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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

    public function getFonction(): ?string
    {
        return $this->fonction;
    }

    public function setFonction(string $fonction): self
    {
        $this->fonction = $fonction;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function getFullname(): ?string
    {
        return $this->nom." ". $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }


    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }
    public function getIsActive(): ?bool
    {
        return $this->is_active;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->is_active = $isActive;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function setImageFile(?File $image = null)
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

    public function getImageFile()
    {
        return $this->imageFile;
    }


    /**
     * String representation of object
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     * @since 5.1.0
     */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->email,
            $this->password,
            $this->organisation,
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
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->email,
            $this->password,
            $this->organisation,
            // see section on salt below
            // $this->salt
            ) = unserialize($serialized);
    }

    public function getIsOrganisationAdmin(): ?bool
    {
        return $this->isOrganisationAdmin;
    }

    public function setIsOrganisationAdmin(bool $isOrganisationAdmin): self
    {
        $this->isOrganisationAdmin = $isOrganisationAdmin;

        return $this;
    }

    public function getDateDerniereConnexion(): ?\DateTimeInterface
    {
        return $this->dateDerniereConnexion;
    }

    public function setDateDerniereConnexion(\DateTimeInterface $dateDerniereConnexion): self
    {
        $this->dateDerniereConnexion = $dateDerniereConnexion;

        return $this;
    }

    /**
     * @return Collection|AnnonceSonorise[]
     */
    public function getAnnonceSonorises(): Collection
    {
        return $this->annonceSonorises;
    }

    public function addAnnonceSonorise(AnnonceSonorise $annonceSonorise): self
    {
        if (!$this->annonceSonorises->contains($annonceSonorise)) {
            $this->annonceSonorises[] = $annonceSonorise;
            $annonceSonorise->setUser($this);
        }

        return $this;
    }

    public function removeAnnonceSonorise(AnnonceSonorise $annonceSonorise): self
    {
        if ($this->annonceSonorises->removeElement($annonceSonorise)) {
            // set the owning side to null (unless already changed)
            if ($annonceSonorise->getUser() === $this) {
                $annonceSonorise->setUser(null);
            }
        }

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getUserRole(): ?Role
    {
        return $this->userRole;
    }

    public function setUserRole(?Role $userRole): self
    {
        $this->userRole = $userRole;

        return $this;
    }

}
