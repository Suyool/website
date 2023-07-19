<?php

namespace App\Entity\Shopify;


use App\Entity\Traits\DateTrait;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="requested_data")
 */
class RequestedData
{
    use DateTrait;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="shop", type="string", length=250, nullable=false)
     * @Assert\NotBlank
     */
    private $shop;

    /**
     * @var string
     *
     * @ORM\Column(name="data", type="text", length=250, nullable=false)
     * @Assert\Length(max=4096)
     */
    private $data;

    /**
     * Set the value of id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * Get the value of id
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set the value of shop
     */
    public function setShop(string $shop): void
    {
        $this->shop = $shop;
    }

    /**
     * Get the value of shop
     */
    public function getShop(): ?string
    {
        return $this->shop;
    }

    /**
     * Set the value of accessToken
     */
    public function setData(string $data): void
    {
        $this->data = $data;
    }

    /**
     * Get the value of accessToken
     */
    public function getData(): ?string
    {
        return $this->data;
    }

}
