<?php

namespace App\Entity;

use App\Repository\CocktailIngredientRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CocktailIngredientRepository::class)
 */
class CocktailIngredient
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"cocktail:read", "ingredient:read"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Cocktail::class, inversedBy="cocktailIngredients")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"ingredient:read"})
     * @Assert\NotNull(message="cocktailIngredient.cocktail.notNull")
     */
    private $cocktail;

    /**
     * @var Ingredient
     *
     * @ORM\ManyToOne(targetEntity=Ingredient::class, inversedBy="cocktailIngredients")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"cocktail:read"})
     * @Assert\NotNull(message="cocktailIngredient.ingredient.notNull")
     */
    private $ingredient;

    /**
     * @ORM\Column(type="float")
     * @Groups({"cocktail:read", "ingredient:read"})
     * @Assert\Type("float", message="cocktailIngredient.quantity.type")
     * @Assert\NotNull(message="cocktailIngredient.quantity.notNull")
     */
    private $quantity;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"cocktail:read", "ingredient:read"})
     * @Assert\Length(min=2, minMessage="cocktailIngredient.length.min.{{ value }}")
     * @Assert\NotNull(message="cocktailIngredient.quantityType.notNull")
     */
    private $quantityType;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCocktail(): ?Cocktail
    {
        return $this->cocktail;
    }

    public function setCocktail(?Cocktail $cocktail): self
    {
        $this->cocktail = $cocktail;

        return $this;
    }

    public function getIngredient(): ?Ingredient
    {
        return $this->ingredient;
    }

    public function setIngredient(?Ingredient $ingredient): self
    {
        $this->ingredient = $ingredient;

        return $this;
    }

    public function getQuantity(): ?float
    {
        return $this->quantity;
    }

    public function setQuantity(float $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getQuantityType(): ?string
    {
        return $this->quantityType;
    }

    public function setQuantityType(string $quantityType): self
    {
        $this->quantityType = $quantityType;

        return $this;
    }
}
