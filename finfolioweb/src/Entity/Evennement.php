<?php

namespace App\Entity;

use App\Repository\EvennementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\UX\Turbo\Attribute\Broadcast;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: EvennementRepository::class)]
#[Broadcast]
class Evennement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom est obligatoire")]
    #[Assert\Regex(
        pattern: "/^[a-zA-Z\s]*$/",
        message: "Le nom doit contenir que des lettres"
    )]
    private ?string $nom_event = null;

    #[ORM\Column(nullable: true)]
    #[Assert\NotBlank(message: "Le montant est obligatoire")]
    #[Assert\Type(
        type: "numeric",
        message: "le montant doit etre une valeur numerique"
    )]
    #[Assert\Positive(

        message: "Le montant ne doit pas etre negatif"
    )]
    private ?float $montant = null;

    #[ORM\Column(type: "datetime")]
    #[Assert\NotBlank(message: "La date est obligatoire")]
    #[Assert\GreaterThanOrEqual(
        value: "today",
        message: "La date doit etre une date future"
    )]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "L adresse est obligatoire")]
    private ?string $adresse = null;

    #[ORM\Column(length: 5000, nullable: true)]
    #[Assert\NotBlank(message: "La descripton est obligatoire")]
    #[Assert\Length(
        min: 20,
        minMessage: "La description doit contenir  au moins 20 charateres"
    )]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    private ?float $rating = null;
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imageData = null;




    public function getImageData(): ?string
    {
        return $this->imageData;
    }

    public function setImageData(?string $imageData): void
    {
        $this->imageData = $imageData;
    }

    #[ORM\Column(type: 'decimal', precision: 10, scale: 8, nullable: true)]
    private ?float $latitude = null;

    #[ORM\Column(type: 'decimal', precision: 11, scale: 8, nullable: true)]
    private ?float $longitude = null;


    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(?float $latitude): static
    {
        $this->latitude = $latitude;
        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(?float $longitude): static
    {
        $this->longitude = $longitude;
        return $this;
    }

    #[ORM\OneToMany(mappedBy: 'evenement_id', targetEntity: Don::class)]
    private Collection $dons;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'evennements')]
    private Collection $user;




    public function __construct()
    {
        $this->dons = new ArrayCollection();
        $this->user = new ArrayCollection();


    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomEvent(): ?string
    {
        return $this->nom_event;
    }

    public function setNomEvent(string $nom_event): static
    {
        $this->nom_event = $nom_event;

        return $this;
    }

    public function getMontant(): ?float
    {
        return $this->montant;
    }

    public function setMontant(?float $montant): static
    {
        $this->montant = $montant;

        return $this;
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

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): static
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getRating(): ?float
    {
        return $this->rating;
    }

    public function setRating(?int $rating): static
    {
        $this->rating = $rating;

        return $this;
    }

    /**
     * @return Collection<int, Don>
     */
    public function getDons(): Collection
    {
        return $this->dons;
    }

    public function addDon(Don $don): static
    {
        if (!$this->dons->contains($don)) {
            $this->dons->add($don);
            $don->setEvenementId($this);
        }

        return $this;
    }

    public function removeDon(Don $don): static
    {
        if ($this->dons->removeElement($don)) {
            // set the owning side to null (unless already changed)
            if ($don->getEvenementId() === $this) {
                $don->setEvenementId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUser(): Collection
    {
        return $this->user;
    }

    public function setUser(Collection $user): void
    {
        $this->user = $user;
    }

    public function addUser(User $user): static
    {
        if (!$this->user->contains($user)) {
            $this->user->add($user);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        $this->user->removeElement($user);

        return $this;
    }



}
