<?php

namespace App\Entity;

use App\Repository\CreditRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CreditRepository::class)]
class Credit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    #[ORM\Column(name: "montant", type: Types::STRING)]
    #[Assert\NotNull(message: 'Le montant ne doit  pas etre vide ')]
    #[Assert\Type(type: 'string', message: 'Le montant doit être une chaîne de caractères')]
    private ?string $montant ;

    #[ORM\Column(name: "interetMax", type: Types::FLOAT)]
    #[Assert\NotNull(message: 'L\'intérêt maximal ne peut pas être vide')]
    #[Assert\Type(type: 'float', message: 'L\'intérêt maximal doit être un nombre décimal')]
    private ?float $interetMax = null;

    #[ORM\Column(name: "interetMin", type: Types::FLOAT)]
    #[Assert\NotNull(message: 'L\'intérêt minimal ne peut pas être vide')]
    #[Assert\Type(type: 'float', message: 'L\'intérêt minimal doit être un nombre décimal')]
    private ?float $interetMin = null;

    #[ORM\Column(name: "dateD", type: Types::DATE_MUTABLE)]
    #[Assert\NotNull(message: 'La date de début ne peut pas être vide')]
    #[Assert\Type(type: '\DateTimeInterface', message: 'La date de début doit être de type DateTime')]
    private ?\DateTimeInterface $dateD = null;

    #[ORM\Column(name: "dateF", type: Types::DATE_MUTABLE)]
    #[Assert\NotNull(message: 'La date de fin ne peut pas être vide')]
    #[Assert\Type(type: '\DateTimeInterface', message: 'La date de fin doit être de type DateTime')]
    private ?\DateTimeInterface $dateF = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'credit')]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "id", nullable: false)]
    private ?User $user_id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMontant(): ?string
    {
        return $this->montant;
    }

    public function setMontant(?string $montant): static
    {
        $this->montant = $montant;

        return $this;
    }

    public function getInteretMax(): ?float
    {
        return $this->interetMax;
    }

    public function setInteretMax(?float $interetMax): static
    {
        $this->interetMax = $interetMax;

        return $this;
    }

    public function getInteretMin(): ?float
    {
        return $this->interetMin;
    }

    public function setInteretMin(?float $interetMin): static
    {
        $this->interetMin = $interetMin;

        return $this;
    }

    public function getDateD(): ?\DateTimeInterface
    {
        return $this->dateD;
    }

    public function setDateD(?\DateTimeInterface $dateD): static
    {
        $this->dateD = $dateD;

        return $this;
    }

    public function getDateF(): ?\DateTimeInterface
    {
        return $this->dateF;

    }

    public function setDateF(?\DateTimeInterface $dateF): static
    {
        $this->dateF = $dateF;

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

    public function __toString(): string
    {
        return $this->montant; // Or any other property you want to use as a string representation
    }
}