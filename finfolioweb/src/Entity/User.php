<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $nom = null;

    #[ORM\Column(length: 50)]
    private ?string $prenom = null;

    #[ORM\Column(length: 100)]
    private ?string $email = null;

    #[ORM\Column(length: 20)]
    private ?string $numtel = null;

    #[ORM\Column(length: 100)]
    private ?string $password = null;

    #[ORM\Column(length: 100)]
    private ?string $adresse = null;

    #[ORM\Column]
    private ?int $nbcredit = null;

    #[ORM\Column]
    private ?float $rate = null;

    #[ORM\Column(length: 20)]
    private ?string $role = null;

    #[ORM\Column(length: 200)]
    private ?string $solde = null;

    #[ORM\Column(length: 30)]
    private ?string $statut = null;

    #[ORM\Column(length: 500)]
    private ?string $image = null;

    #[ORM\Column(length: 50)]
    private ?string $datepunition = null;

    #[ORM\Column]
    private ?float $total_tax = null;

    #[ORM\OneToMany(mappedBy: 'user_id', targetEntity: Don::class)]
    private Collection $dons;

    public function __construct()
    {
        $this->dons = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getNumtel(): ?string
    {
        return $this->numtel;
    }

    public function setNumtel(string $numtel): static
    {
        $this->numtel = $numtel;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

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

    public function getNbcredit(): ?int
    {
        return $this->nbcredit;
    }

    public function setNbcredit(int $nbcredit): static
    {
        $this->nbcredit = $nbcredit;

        return $this;
    }

    public function getRate(): ?float
    {
        return $this->rate;
    }

    public function setRate(float $rate): static
    {
        $this->rate = $rate;

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): static
    {
        $this->role = $role;

        return $this;
    }

    public function getSolde(): ?string
    {
        return $this->solde;
    }

    public function setSolde(string $solde): static
    {
        $this->solde = $solde;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getDatepunition(): ?string
    {
        return $this->datepunition;
    }

    public function setDatepunition(string $datepunition): static
    {
        $this->datepunition = $datepunition;

        return $this;
    }

    public function getTotalTax(): ?float
    {
        return $this->total_tax;
    }

    public function setTotalTax(float $total_tax): static
    {
        $this->total_tax = $total_tax;

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
            $don->setUserId($this);
        }

        return $this;
    }

    public function removeDon(Don $don): static
    {
        if ($this->dons->removeElement($don)) {
            // set the owning side to null (unless already changed)
            if ($don->getUserId() === $this) {
                $don->setUserId(null);
            }
        }

        return $this;
    }
}
