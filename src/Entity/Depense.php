<?php

namespace App\Entity;

use App\Repository\DepenseRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: DepenseRepository::class)]
class Depense
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank(message: "The date cannot be empty.")]
    private ?\DateTimeInterface $date = null ;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "le Type ne doit pas être null.")]

    #[Assert\Length(
        max: 225,
        maxMessage: "Le type ne doit pas exceder {{ limit }} caractere."
    )]
    private ?string $type = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "le montant ne doit pas être null.")]
    #[Assert\Length(
        max: 225,
        maxMessage: "Le montant ne doit pas exceder {{ limit }} caractére."
    )]
    private ?float $montant = null;

    #[ORM\ManyToOne(inversedBy: 'depenses')]
    private ?User $user;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: 'taux_tax', nullable: false)]
    private ?Tax $tax ;


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

    public function getTax(): ?Tax
    {
        return $this->tax;
    }

    public function setTax(Tax $tax): static
    {
        $this->tax = $tax;

        return $this;
    }

}
