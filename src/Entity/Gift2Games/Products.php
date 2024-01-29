<?php
// src/Entity/Gift2Games/Product.php

namespace App\Entity\Gift2Games;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="products")
 */
class Products
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @ORM\Column(type="integer", name="product_id", nullable=true)
     */
    private $productId;

    /**
     * @ORM\Column(type="integer", name="category_id", nullable=true)
     */
    private $categoryId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $image;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=3, nullable=true)
     */
    private $sellPrice;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=3, nullable=true)
     */
    private $price;

    /**
     * @ORM\Column(type="decimal", name="discount_rate", precision=10, scale=2, nullable=true)
     */
    private $discountRate;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $inStock;

    /**
     * @ORM\Column(type="string", length=3, nullable=true)
     */
    private $currency;

    // Getters and Setters for each property

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProductId(): ?int
    {
        return $this->productId;
    }

    public function setProductId(?int $productId): self
    {
        $this->productId = $productId;

        return $this;
    }

    public function getCategoryId(): ?int
    {
        return $this->categoryId;
    }

    public function setCategoryId(?int $categoryId): self
    {
        $this->categoryId = $categoryId;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getSellPrice(): ?float
    {
        return $this->sellPrice;
    }

    public function setSellPrice(?float $sellPrice): self
    {
        $this->sellPrice = $sellPrice;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getDiscountRate(): ?float
    {
        return $this->discountRate;
    }

    public function setDiscountRate(?float $discountRate): self
    {
        $this->discountRate = $discountRate;

        return $this;
    }

    public function getInStock(): ?bool
    {
        return $this->inStock;
    }

    public function setInStock(?bool $inStock): self
    {
        $this->inStock = $inStock;

        return $this;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(?string $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    public function toArray(int $depth = 1): array
    {
        if ($depth <= 0) {
            return [];
        }

        return [
            'id' => $this->id,
            'productId' => $this->productId,
            'categoryId' => $this->categoryId,
            'title' => $this->title,
            'price' => $this->price,
            'inStock' => $this->inStock,
            'image' => $this->image,
        ];
    }
}
