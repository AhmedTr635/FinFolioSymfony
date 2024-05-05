<?php

namespace App\Entity;

use App\Repository\InvestissementRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: InvestissementRepository::class)]
class Investissement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateAchat = null;

    #[ORM\Column]
    private ?int $prixAchat = null;

    #[ORM\Column]
    private ?float $ROI = null;

    #[ORM\Column]
    private ?int $montant = null;

    #[ORM\Column]
    #[Assert\NotBlank(message:'La valeur est obligatoire')]
    #[Assert\Type(type: 'float',message: 'La valeur doit contenir que des chiffres')]
    private ?float $tax = null;

    #[ORM\Column]
    private ?int $reId = null;

    #[ORM\Column]
    private ?int $userId = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateAchat(): ?\DateTimeInterface
    {
        return $this->dateAchat;
    }

    public function setDateAchat(\DateTimeInterface $dateAchat): static
    {
        $this->dateAchat = $dateAchat;

        return $this;
    }

    public function getPrixAchat(): ?int
    {
        return $this->prixAchat;
    }

    public function setPrixAchat(int $prixAchat): static
    {
        $this->prixAchat = $prixAchat;

        return $this;
    }

    public function getROI(): ?float
    {
        return $this->ROI;
    }

    public function setROI(float $ROI): static
    {
        $this->ROI = $ROI;

        return $this;
    }

    public function getMontant(): ?int
    {
        return $this->montant;
    }

    public function setMontant(int $montant): static
    {
        $this->montant = $montant;

        return $this;
    }

    public function getTax(): ?float
    {
        return $this->tax;
    }

    public function setTax(float $tax): static
    {
        $this->tax = $tax;

        return $this;
    }

    public function getReId(): ?int
    {
        return $this->reId;
    }

    public function setReId(int $reId): static
    {
        $this->reId = $reId;

        return $this;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): static
    {
        $this->userId = $userId;

        return $this;
    }
}