<?php

namespace App\Entity;

use App\Repository\TaxRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: TaxRepository::class)]
class Tax
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    #[Assert\NotBlank]

    #[ORM\Column]
    private ?float $montant = null;
    #[Assert\NotBlank]

    #[ORM\Column(length: 50)]
    private ?string $type = null;

    #[ORM\Column(length: 10)]
    private ?string $optimisation = null;

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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getOptimisation(): ?string
    {
        return $this->optimisation;
    }

    public function setOptimisation(string $optimisation): static
    {
        $this->optimisation = $optimisation;

        return $this;
    }
}
