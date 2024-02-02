<?php

namespace App\Entity\Gift2Games;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Gift2GamesOrdersRepository")
 * @ORM\Table(name="orders")
 */
class Order
{
    public static $statusOrder = array("PENDING" => "pending", "HELD" => "held", "PURCHASED" => "purchased", "COMPLETED" => "completed", "CANCELED" => "canceled");

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="suyoolUserId")
     */
    private $suyoolUserId;

    /**
     * @ORM\Column(type="string")
     */
    private $status;

    /**
     * @ORM\Column(type="string")
     */
    private $productId;

    /**
     * @ORM\Column(name="category", type="string")
     */
    private $category;

    /**
     * @ORM\Column(type="string")
     */
    private $description;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=3, nullable=true)
     */
    private $amount;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=3, nullable=true)
     */
    private $originalAmount;

    /**
     * @ORM\Column(type="string")
     */
    private $currency;

    /**
     * @ORM\Column(type="integer")
     */
    private $transId;

    /**
     * @ORM\Column(name="serialCode", type="string", nullable=true)
     */
    private $serialCode;

    /**
     * @ORM\Column(name="serialNumber", type="string", nullable=true)
     */
    private $serialNumber;


    /**
     * @ORM\Column(name="orderFake", type="string", nullable=true)
     */
    private $orderFake;

    /**
     * @ORM\Column(name="errorInfo")
     */
    private $error;

    public function getId()
    {
        return $this->id;
    }

    public function getSuyoolUserId()
    {
        return $this->suyoolUserId;
    }

    public function setSuyoolUserId($suyoolUserId)
    {
        $this->suyoolUserId = $suyoolUserId;
        return $this;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function getOriginalAmount(): ?float
    {
        return $this->originalAmount;
    }

    public function setOriginalAmount(?float $originalAmount): self
    {
        $this->originalAmount = $originalAmount;

        return $this;
    }

    public function getProductId(): ?string
    {
        return $this->productId;
    }

    public function setProductId(string $productId): self
    {
        $this->productId = $productId;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }
    public function setAmount($amount)
    {
        $this->amount = $amount;
        return $this;
    }



    public function setCurrency($currency)
    {
        $this->currency = $currency;
        return $this;
    }

    public function getCurrency()
    {
        return $this->currency;
    }

    public function getTransId()
    {
        return $this->transId;
    }

    public function setTransId($transId)
    {
        $this->transId = $transId;
        return $this;
    }

    public function setError($error)
    {
        $this->error = $error;
        return $this;
    }

    public function getError()
    {
        return $this->error;
    }

    public function getSerialCode()
    {
        return $this->serialCode;
    }

    public function setSerialCode($serialCode)
    {
        $this->serialCode = $serialCode;
        return $this;
    }

    public function getSerialNumber()
    {
        return $this->serialNumber;
    }

    public function setSerialNumber($serialNumber)
    {
        $this->serialNumber = $serialNumber;
        return $this;
    }

    public function getSerialExpiryDate()
    {
        return $this->serialExpiryDate;
    }

    public function getOrderFake()
    {
        return $this->orderFake;
    }

    public function setOrderFake($orderFake)
    {
        $this->orderFake = $orderFake;
        return $this;
    }

    public function getCategory()
    {
        return $this->category;
    }

    public function setCategory($category)
    {
        $this->category = $category;
        return $this;
    }
}
