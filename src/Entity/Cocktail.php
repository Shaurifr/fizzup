<?php

namespace App\Entity;

use App\Repository\CocktailRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CocktailRepository::class)
 * @UniqueEntity("name", message="cocktail.name.unique.{{ value }}")
 */
class Cocktail
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"cocktail:read", "ingredient:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Groups({"cocktail:read", "ingredient:read"})
     * @Assert\NotBlank
     * @Assert\Length(min=2, max=255)
     * @Assert\Type("string")
     */
    private $name;

    /**
     * @ORM\Column(type="float")
     * @Groups({"cocktail:read", "ingredient:read"})
     * @Assert\Type("float", message="cocktail.price.type")
     * @Assert\GreaterThan(0)
     */
    private $price;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"cocktail:read", "ingredient:read"})
     * @Assert\Type("bool")
     * @Assert\NotNull()
     */
    private $hasAlcohol;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"cocktail:read", "ingredient:read"})
     */
    private $origin;

    /**
     * @ORM\OneToMany(targetEntity=CocktailIngredient::class, mappedBy="cocktail", orphanRemoval=true, cascade={"persist"})
     * @Groups({"cocktail:read"})
     * @Assert\Valid()
     */
    private $cocktailIngredients;

    /**
     * @ORM\OneToMany(targetEntity=CocktailOrder::class, mappedBy="cocktail")
     */
    private $cocktailOrders;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="cocktails")
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $coverFilename;

    public function __construct()
    {
        $this->cocktailIngredients = new ArrayCollection();
        $this->cocktailOrders = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getHasAlcohol(): ?bool
    {
        return $this->hasAlcohol;
    }

    public function setHasAlcohol(bool $hasAlcohol): self
    {
        $this->hasAlcohol = $hasAlcohol;

        return $this;
    }

    public function getOrigin(): ?string
    {
        return $this->origin;
    }

    public function setOrigin(?string $origin): self
    {
        $this->origin = $origin;

        return $this;
    }

    /**
     * @return Collection|CocktailIngredient[]
     */
    public function getCocktailIngredients(): Collection
    {
        return $this->cocktailIngredients;
    }

    public function addCocktailIngredient(CocktailIngredient $cocktailIngredient): self
    {
        if (!$this->cocktailIngredients->contains($cocktailIngredient)) {
            $this->cocktailIngredients[] = $cocktailIngredient;
            $cocktailIngredient->setCocktail($this);
        }

        return $this;
    }

    public function removeCocktailIngredient(CocktailIngredient $cocktailIngredient): self
    {
        if ($this->cocktailIngredients->removeElement($cocktailIngredient)) {
            // set the owning side to null (unless already changed)
            if ($cocktailIngredient->getCocktail() === $this) {
                $cocktailIngredient->setCocktail(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|CocktailOrder[]
     */
    public function getCocktailOrders(): Collection
    {
        return $this->cocktailOrders;
    }

    public function addCocktailOrder(CocktailOrder $cocktailOrder): self
    {
        if (!$this->cocktailOrders->contains($cocktailOrder)) {
            $this->cocktailOrders[] = $cocktailOrder;
            $cocktailOrder->setCocktail($this);
        }

        return $this;
    }

    public function removeCocktailOrder(CocktailOrder $cocktailOrder): self
    {
        if ($this->cocktailOrders->removeElement($cocktailOrder)) {
            // set the owning side to null (unless already changed)
            if ($cocktailOrder->getCocktail() === $this) {
                $cocktailOrder->setCocktail(null);
            }
        }

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

    public function getCoverFilename(): ?string
    {
        return $this->coverFilename;
    }

    public function setCoverFilename(?string $coverFilename): self
    {
        $this->coverFilename = $coverFilename;

        return $this;
    }
}
