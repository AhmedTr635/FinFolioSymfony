<?php



namespace App\Entity;

use App\Entity\Traits\StripeTrait;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: "App\Repository\CommandeRepository")]
#[ORM\Table(name: "`commande`")]
class Commande
{
    const DEVISE = 'eur';

    use StripeTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private $id;

    #[ORM\Column(type: "string", length: 255)]
    private $reference;

    #[ORM\Column(type: "float")]
    private $price;
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'commande')]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "id", nullable: false)]
    private $user;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): self
    {
        $this->reference = $reference;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }



    public function getPrice()
    {
        return $this->price;
    }

    public function setPrice($price): void
    {
        $this->price = $price;
    }
}

