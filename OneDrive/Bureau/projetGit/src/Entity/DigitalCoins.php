<?php

namespace App\Entity;

use App\Repository\DigitalCoinsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DigitalCoinsRepository::class)]
class DigitalCoins
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $recentValue = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateAchat = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateVente = null;

    #[ORM\Column]
    private ?int $montant = null;

    #[ORM\Column]
    private ?int $leverage = null;

    #[ORM\Column]
    private ?float $stopLoss = null;

    #[ORM\Column]
    private ?int $userId = null;

    #[ORM\Column]
    private ?float $ROI = null;

    #[ORM\Column]
    private ?float $prixAchat = null;

    #[ORM\Column]
    private ?float $tax = null;

    #[ORM\Column(length: 255)]
    private ?string $code = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRecentValue(): ?float
    {
        return $this->recentValue;
    }

    public function setRecentValue(float $recentValue): static
    {
        $this->recentValue = $recentValue;

        return $this;
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

    public function getDateVente(): ?\DateTimeInterface
    {
        return $this->dateVente;
    }

    public function setDateVente(?\DateTimeInterface $dateVente): static
    {
        $this->dateVente = $dateVente;

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

    public function getLeverage(): ?int
    {
        return $this->leverage;
    }

    public function setLeverage(int $leverage): static
    {
        $this->leverage = $leverage;

        return $this;
    }

    public function getStopLoss(): ?float
    {
        return $this->stopLoss;
    }

    public function setStopLoss(float $stopLoss): static
    {
        $this->stopLoss = $stopLoss;

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

    public function getROI(): ?float
    {
        return $this->ROI;
    }

    public function setROI(float $ROI): static
    {
        $this->ROI = $ROI;

        return $this;
    }

    public function getPrixAchat(): ?float
    {
        return $this->prixAchat;
    }

    public function setPrixAchat(float $prixAchat): static
    {
        $this->prixAchat = $prixAchat;

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

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }
}
