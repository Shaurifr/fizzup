<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OrderRepository::class)
 * @ORM\Table(name="`order`")
 */
class Order
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $orderAt;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $street;

    /**
     * @ORM\Column(type="string", length=5)
     */
    private $postalCode;

    /**
     * @ORM\Column(type="string", length=45)
     */
    private $city;

    /**
     * @ORM\Column(type="float")
     */
    private $priceTotal;

    /**
     * @ORM\OneToMany(targetEntity=CocktailOrder::class, mappedBy="linkOrder", orphanRemoval=true)
     */
    private $cocktailOrders;

    public function __construct()
    {
        $this->cocktailOrders = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrderAt(): ?\DateTimeImmutable
    {
        return $this->orderAt;
    }

    public function setOrderAt(?\DateTimeImmutable $orderAt): self
    {
        $this->orderAt = $orderAt;

        return $this;
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

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(string $street): self
    {
        $this->street = $street;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(string $postalCode): self
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getPriceTotal(): ?float
    {
        return $this->priceTotal;
    }

    public function setPriceTotal(float $priceTotal): self
    {
        $this->priceTotal = $priceTotal;

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
            $cocktailOrder->setLinkOrder($this);
        }

        return $this;
    }

    public function removeCocktailOrder(CocktailOrder $cocktailOrder): self
    {
        if ($this->cocktailOrders->removeElement($cocktailOrder)) {
            // set the owning side to null (unless already changed)
            if ($cocktailOrder->getLinkOrder() === $this) {
                $cocktailOrder->setLinkOrder(null);
            }
        }

        return $this;
    }
}
