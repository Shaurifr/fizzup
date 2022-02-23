<?php

namespace App\Entity;

use App\Repository\CocktailOrderRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CocktailOrderRepository::class)
 */
class CocktailOrder
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Cocktail::class, inversedBy="cocktailOrders")
     * @ORM\JoinColumn(nullable=false)
     */
    private $cocktail;

    /**
     * prix à l'unité du cocktail
     *
     * @ORM\Column(type="float")
     */
    private $price;

    /**
     * @ORM\ManyToOne(targetEntity=Order::class, inversedBy="cocktailOrders")
     * @ORM\JoinColumn(nullable=false)
     */
    private $linkOrder;

    /**
     * @ORM\Column(type="integer")
     */
    private $quantity;

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

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getLinkOrder(): ?Order
    {
        return $this->linkOrder;
    }

    public function setLinkOrder(?Order $linkOrder): self
    {
        $this->linkOrder = $linkOrder;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }
}
