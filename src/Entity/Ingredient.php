<?php

namespace App\Entity;

use App\Repository\IngredientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=IngredientRepository::class)
 */
class Ingredient
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"ingredient:read", "cocktail:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"ingredient:read", "cocktail:read"})
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"ingredient:read", "cocktail:read"})
     */
    private $type;

    /**
     * @ORM\OneToMany(targetEntity=CocktailIngredient::class, mappedBy="ingredient", cascade={"persist", "remove"})
     * @Groups({"ingredient:read"})
     */
    private $cocktailIngredients;

    public function __construct()
    {
        $this->cocktailIngredients = new ArrayCollection();
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

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
            $cocktailIngredient->setIngredient($this);
        }

        return $this;
    }

    public function removeCocktailIngredient(CocktailIngredient $cocktailIngredient): self
    {
        if ($this->cocktailIngredients->removeElement($cocktailIngredient)) {
            // set the owning side to null (unless already changed)
            if ($cocktailIngredient->getIngredient() === $this) {
                $cocktailIngredient->setIngredient(null);
            }
        }

        return $this;
    }
}
