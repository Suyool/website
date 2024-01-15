<?php

namespace App\Entity\Sodetel;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SodetelOrdersRepository")
 * @ORM\Table(name="products")
 */
class Product
{
    public static $statusOrder = array("PENDING" => "pending", "HELD" => "held", "PURCHASED" => "purchased", "COMPLETED" => "completed", "CANCELED" => "canceled");

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $type;

    /**
     * @ORM\Column(name="plan_code", type="string")
     */
    private $planCode;

    /**
     * @ORM\Column(name="plan_description", type="string")
     */
    private $planDescription;

    /**
     * @ORM\Column(type="integer")
     */
    private $pricettc;

    /**
     * @ORM\Column(type="integer")
     */
    private $priceht;

    /**
     * @ORM\Column(type="integer")
     */
    private $price;

    /**
     * @ORM\Column(type="string")
     */
    private $currency;

    /**
     * @ORM\Column(type="integer")
     */
    private $sayrafa;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPlanCode()
    {
        return $this->planCode;
    }

    /**
     * @param mixed $planCode
     */
    public function setPlanCode($planCode)
    {
        $this->planCode = $planCode;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPlanDescription()
    {
        return $this->planDescription;
    }

    /**
     * @param mixed $planDescription
     */
    public function setPlanDescription($planDescription)
    {
        $this->planDescription = $planDescription;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPricettc()
    {
        return $this->pricettc;
    }

    /**
     * @param mixed $pricettc
     */
    public function setPricettc($pricettc)
    {
        $this->pricettc = $pricettc;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPriceHt()
    {
        return $this->priceht;
    }

    /**
     * @param mixed $priceht
     */
    public function setPriceHt($priceht)
    {
        $this->priceht = $priceht;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param mixed $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param mixed $currency
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSayrafa()
    {
        return $this->sayrafa;
    }

    /**
     * @param mixed $sayrafa
     */
    public function setSayrafa($sayrafa)
    {
        $this->sayrafa = $sayrafa;
        return $this;
    }
}
