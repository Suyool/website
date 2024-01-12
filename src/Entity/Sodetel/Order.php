<?php

namespace App\Entity\Sodetel;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
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
    private $UtilityMerchantId;

    /**
     * @ORM\Column(type="string")
     */
    private $status;

    /**
     * @ORM\Column(type="integer")
     */
    private $amount;

    /**
     * @ORM\Column(type="string")
     */
    private $currency;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $transId;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Sodetel\Product",fetch="EAGER")
     * @ORM\JoinColumn(name="productId", referencedColumnName="id")
     */
    private $product;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $identifier;


//    /**
//     * @ORM\OneToOne(targetEntity="App\Entity\Sodetel\Product",fetch="EAGER")
//     * @ORM\JoinColumn(name="products_id", referencedColumnName="id")
//     */
//    private $products;
//    /**
//     * @ORM\Column(type="integer", name="product_id", nullable=true)
//     */
//    private $productId;


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

    public function getUtilityMerchantId()
    {
        return $this->UtilityMerchantId;
    }

    public function setUtilityMerchantId($UtilityMerchantId)
    {
        $this->UtilityMerchantId = $UtilityMerchantId;
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


    public function getProduct()
    {
        if ($this->product) {
            return $this->product;
        }
        return null;
    }
    public function setProduct(?Product $product): self
    {
        $this->product = $product;
        return $this;
    }

    public function getIdentifier()
    {
        return $this->identifier;
    }

    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
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


}
