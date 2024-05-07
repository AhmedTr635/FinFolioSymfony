<?php

namespace App\Entity;

use App\Repository\OffreRepository;
use Cassandra\Float_;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: OffreRepository::class)]
class Offre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'offres')]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "id", nullable: false)]
    private ?User $user;

    #[ORM\Column(type: 'float', nullable: true)]
    #[Assert\NotNull(message: 'The montant cannot be null')]
    #[Assert\Type(type: 'float', message: 'The montant must be a floating-point number')]
    private ?float $montant = null;

    #[ORM\Column(type: 'float', nullable: true)]

    private ?float $credit_id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'Offre')]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "id", nullable: false)]
    private ?User $user_id = null;

    #[ORM\Column(name: "interet", type: Types::FLOAT)]
    #[Assert\NotNull(message: 'The interet cannot be null')]
    #[Assert\Type(type: 'float', message: 'The interet must be a floating-point number')]
    private ?float $interet = null;




    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMontant(): ?float
    {
        return $this->montant;
    }

    public function setMontant(float $montant): static
    {
        $this->montant = $montant;

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

    public function getCreditId(): ?float
    {
        return $this->credit_id;
    }

    public function setCreditId(?Float $credit_id): static
    {
        $this->credit_id = $credit_id;

        return $this;
    }

    public function getInteret(): ?float
    {
        return $this->interet;
    }

    public function setInteret(float $interet): static
    {
        $this->interet = $interet;

        return $this;
    }
}