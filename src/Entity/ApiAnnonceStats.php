<?php

namespace App\Entity;

use App\Repository\ApiAnnonceStatsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ApiAnnonceStatsRepository::class)
 */
class ApiAnnonceStats
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $nombreAppelApi;

    /**
     * @ORM\Column(type="integer")
     */
    private $tempsLecture;

    /**
     * @ORM\OneToOne(targetEntity=AnnonceSonorise::class, inversedBy="apiAnnonceStats", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $annonceSonorise;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombreAppelApi(): ?int
    {
        return $this->nombreAppelApi;
    }

    public function setNombreAppelApi(int $nombreAppelApi): self
    {
        $this->nombreAppelApi = $nombreAppelApi;

        return $this;
    }

    public function getTempsLecture(): ?int
    {
        return $this->tempsLecture;
    }

    public function setTempsLecture(int $tempsLecture): self
    {
        $this->tempsLecture = $tempsLecture;

        return $this;
    }

    public function getAnnonceSonorise(): ?AnnonceSonorise
    {
        return $this->annonceSonorise;
    }

    public function setAnnonceSonorise(AnnonceSonorise $annonceSonorise): self
    {
        $this->annonceSonorise = $annonceSonorise;

        return $this;
    }
}
