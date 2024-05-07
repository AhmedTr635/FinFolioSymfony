<?php

namespace App\Entity;

use App\Repository\RealEstateRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: RealEstateRepository::class)]
class RealEstate
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:'Le nom est obligatoire')]
    #[Assert\Type(type: 'string',message: 'Le nom doit contenir que des lettres')]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:'Le champ emplacement est obligatoire')]
    #[Assert\Type(type: 'string',message: 'Le champ emplacement doit contenir que des lettres')]
    private ?string $emplacement = null;

    #[ORM\Column]
    #[Assert\NotBlank(message:'ROI est obligatoire')]
    #[Assert\Type(type: 'float',message: 'ROI doit contenir que des chiffres')]
    private ?float $ROI = null;

    #[ORM\Column]
    #[Assert\NotBlank(message:'La valeur est obligatoire')]
    #[Assert\Type(type: 'float',message: 'La valeur doit contenir que des chiffres')]
    private ?float $valeur = null;

    #[ORM\Column]
    #[Assert\NotBlank(message:'Le nombre de chambres est obligatoire')]
    #[Assert\Type(type: 'int',message: 'Le nombre de chambres doit contenir que des chiffres')]
    private ?int $nbrchambres = null;

    #[ORM\Column]
    #[Assert\NotBlank(message:'La superficie est obligatoire')]
    #[Assert\Type(type: 'float',message: 'La superficie doit contenir que des chiffres')]
    private ?float $superficie = null;

    #[ORM\Column(nullable: true)]
    #[Assert\NotBlank(message:'Le nombre de click est obligatoire')]
    #[Assert\Type(type: 'integer',message: 'Le nombre de click doit contenir que des chiffres')]
    private ?int $nbrclick = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imageData = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $virtualTourLink = null;

    #[ORM\Column]
    #[Assert\Type(type: 'float',message: 'La growth doit contenir que des chiffres')]
    private ?float $growth = null;

    #[ORM\Column]
    #[Assert\Type(type: 'float',message: 'La longitude doit contenir que des chiffres')]
    private ?float $longitude = null;

    #[ORM\Column]
    #[Assert\Type(type: 'float',message: 'La latitude doit contenir que des chiffres')]
    private ?float $latitude = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getEmplacement(): ?string
    {
        return $this->emplacement;
    }

    public function setEmplacement(string $emplacement): static
    {
        $this->emplacement = $emplacement;

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

    public function getValeur(): ?float
    {
        return $this->valeur;
    }

    public function setValeur(float $valeur): static
    {
        $this->valeur = $valeur;

        return $this;
    }

    public function getNbrchambres(): ?int
    {
        return $this->nbrchambres;
    }

    public function setNbrchambres(int $nbrchambres): static
    {
        $this->nbrchambres = $nbrchambres;

        return $this;
    }

    public function getSuperficie(): ?float
    {
        return $this->superficie;
    }

    public function setSuperficie(float $superficie): static
    {
        $this->superficie = $superficie;

        return $this;
    }

    public function getNbrclick(): ?int
    {
        return $this->nbrclick;
    }

    public function setNbrclick(?int $nbrclick): static
    {
        $this->nbrclick = $nbrclick;

        return $this;
    }

    public function getImageData(): ?string
    {
        return $this->imageData;
    }

    public function setImageData(?string $imageData): static
    {
        $this->imageData = $imageData;

        return $this;
    }

    public function getVirtualTourLink(): ?string
    {
        return $this->virtualTourLink;
    }

    public function setVirtualTourLink(?string $virtualTourLink): static
    {
        $this->virtualTourLink = $virtualTourLink;

        return $this;
    }

    public function getGrowth(): ?float
    {
        return $this->growth;
    }

    public function setGrowth(?float $growth): void
    {
        $this->growth = $growth;
    }


    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(?float $longitude): void
    {
        $this->longitude = $longitude;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(?float $latitude): void
    {
        $this->latitude = $latitude;
    }

}
