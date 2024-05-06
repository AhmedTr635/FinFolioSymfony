<?php

namespace App\Entity;

use App\Repository\DepenseRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Float_;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: DepenseRepository::class)]
class Depense
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank(message: "La date est obligatoire.")]
    private ?\DateTimeInterface $date = null ;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "le type est obligatoire.")]


    #[Assert\Regex(
        pattern: "/^[a-zA-Z\s]*$/",
        message: "Le type ne doit contenir que des lettres"
    )]
    private ?string $type = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "le montant est obligatoire.")]
    #[Assert\Length(
        max: 225,
        maxMessage: "Le montant ne doit pas exceder {{ limit }} caractére."
    )]
    #[Assert\Positive(
        message: "Le montant doit être supérieur à zéro"
    )]
    #[Assert\Type(
        type: "numeric",
        message: "Le montant ne doit contenir que des chiffres"
    )]
    private ?float $montant = null;

    #[ORM\ManyToOne(inversedBy: 'depenses')]
    private ?User $user;

    #[ORM\Column]
    private ?float $taux_tax ;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function gettaux_tax(): ?float
    {
        return $this->taux_tax;
    }

    public function settaux_tax(float $tax): static
    {
        $this->taux_tax = $tax;

        return $this;
    }

}
