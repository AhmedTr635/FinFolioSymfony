<?php

namespace App\Entity;

use App\Repository\DonRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\UX\Turbo\Attribute\Broadcast;

#[ORM\Entity(repositoryClass: DonRepository::class)]
#[Broadcast]
class Don
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?float $montant_user = null;

    #[ORM\ManyToOne(inversedBy: 'dons')]
    private ?User $user_id
        = null;

    #[ORM\ManyToOne(inversedBy: 'dons')]
    private ?Evennement $evenement_id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMontantUser(): ?float
    {
        return $this->montant_user;
    }

    public function setMontantUser(?float $montant_user): static
    {
        $this->montant_user = $montant_user;

        return $this;
    }

    public function getUserId(): ?User
    {
        return $this->user_id;
    }

    public function setUserId(?User $user_id): static
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getEvenementId(): ?Evennement
    {
        return $this->evenement_id;
    }

    public function setEvenementId(?Evennement $evenement_id): static
    {
        $this->evenement_id = $evenement_id;

        return $this;
    }
}
