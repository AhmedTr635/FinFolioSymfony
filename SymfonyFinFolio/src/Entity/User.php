<?php

namespace App\Entity;

use App\Controller\MailService;
use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    #[ORM\Column(length: 50)]
    private ?string $nom = null;

    #[ORM\Column(length: 50)]
    private ?string $prenom = null;

    #[ORM\Column(length: 100, unique: true)]
    private ?string $email = null;

    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column(length: 100)]
    private ?string $password = null;
    #[ORM\Column(length: 20)]
    private ?string $numtel = null;




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


    private ?string $code = null;

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): void
    {
        $this->code = $code;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->prenom. " " .$this->nom;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $this->roles = [];
        $this->roles[0] = $this->role;
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles[0] = $this->role;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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


    public function getNumtel(): ?string
    {
        return $this->numtel;
    }

    public function setNumtel(string $numtel): static
    {
        $this->numtel = $numtel;

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
       // $this->roles = $this->role;
        return $this->role;    }

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
}
