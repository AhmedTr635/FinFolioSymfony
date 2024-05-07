<?php

namespace App\Entity;

use App\Controller\MailService;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    #[ORM\Column(length: 50)]
    /**
     * @Assert\NotBlank(message="Le nom est obligatoire")
     * @Assert\Length(min=3, minMessage="Le nom doit avoir au moins {{ limit }} caractères")
     */
    private ?string $nom = null;

    #[ORM\Column(length: 50)]
    /**
     * @Assert\NotBlank(message="Le prénom est obligatoire")
     * @Assert\Length(min=3, minMessage="Le prénom doit avoir au moins {{ limit }} caractères")
     */
    private ?string $prenom = null;

    #[ORM\Column(length: 100, unique: true)]
    /**
     * @Assert\NotBlank(message="L'email est obligatoire")
     * @Assert\Email(message="L'email n'est pas valide")
     */
    private ?string $email = null;

    #[ORM\Column(length: 20)]
    /**
     * @Assert\NotBlank(message="Merci de saisir votre numéro")
     * @Assert\Regex(
     *     pattern="/^[2459]\d{7}$/",
     *     message="Entrez un numéro valide"
     * )
     */
    private ?string $numtel = null;

    #[ORM\Column(length: 100)]
    /**
     * @Assert\NotBlank(message="Le mot de passe est obligatoire")
     * @Assert\Regex(
     *     pattern="/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[^\w\d\s:])([^\s]){8,}$/",
     *     message="minimum 8 caractères, un majuscule, un minuscule, un chiffre et un caractère spécial"
     * )
     */
    private ?string $password = null;

    #[ORM\OneToMany(mappedBy: 'user_id', targetEntity: Commande::class)]
    private Collection $commandes;

    public function __construct()
    {
        $this->commandes = new ArrayCollection();
        $this->dons = new ArrayCollection();
        $this->evennements = new ArrayCollection();
    }

    public function getCommandes(): Collection
    {
        return $this->commandes;
    }

    public function addCommandes(Commande $commande): static
    {
        if (!$this->commandes->contains($commande)) {
            $this->commandes->add($commande);
            $commande->setUser($this);
        }

        return $this;
    }

    public function removeDepense(Commande $commande): static
    {
        if ($this->commandes->removeElement($commande)) {
            // set the owning side to null (unless already changed)
            if ($commande->getUser() === $this) {
                $commande->setUser(null);
            }
        }

        return $this;
    }




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

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $datepunition = null;

    #[ORM\Column]
    private ?float $total_tax = null;





    public function getDatepunition(): ?\DateTimeInterface
    {
        return $this->datepunition;
    }

    public function setDatepunition(?\DateTimeInterface $datepunition): void
    {
        $this->datepunition = $datepunition;
    }


    private ?string $code = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $typeCompte = null;

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




    public function getTotalTax(): ?float
    {
        return $this->total_tax;
    }

    public function setTotalTax(float $total_tax): static
    {
        $this->total_tax = $total_tax;

        return $this;
    }

    public function getTypeCompte(): ?string
    {
        return $this->typeCompte;
    }

    public function setTypeCompte(?string $typeCompte): static
    {
        $this->typeCompte = $typeCompte;

        return $this;
    }
    #[ORM\OneToMany(mappedBy: 'user_id', targetEntity: Don::class)]
    private Collection $dons;

    #[ORM\ManyToMany(targetEntity: Evennement::class, mappedBy: 'user')]
    private Collection $evennements;

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

    /**
     * @return Collection<int, Evennement>
     */
    public function getEvennements(): Collection
    {
        return $this->evennements;
    }

    public function addEvennement(Evennement $evennement): static
    {
        if (!$this->evennements->contains($evennement)) {
            $this->evennements->add($evennement);
            $evennement->addUser($this);
        }

        return $this;
    }

    public function removeEvennement(Evennement $evennement): static
    {
        if ($this->evennements->removeElement($evennement)) {
            $evennement->removeUser($this);
        }

        return $this;
    }


}
