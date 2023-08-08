<?php

namespace App\Entity\Estore;

use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity
 * @ORM\Table(name="price")
 */
class Price
{
    //use DateTrait;
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * 
     * @ORM\Column(name="denominationId")
     */
    private $denominationId;

    /**
     * 
     * @ORM\Column(name="denominationDescription",type="integer")
     */
    private $denominationDescription;

    /**
     * 
     * @ORM\Column(name="Amount",type="integer")
     */
    private $Amount;

    /**
     * 
     * @ORM\Column(name="Commission")
     */
    private $Commission;


    /**
     * 
     * @ORM\Column(name="parentConfigurationDataId")
     */
    private $parentConfigurationDataId;

    /**
     * 
     * @ORM\Column(name="serviceCode",type="integer")
     */
    private $serviceCode;

    /**
     * 
     * @ORM\Column(name="serviceProviderCode",type="integer")
     */
    private $serviceProviderCode;




    public function getId()
    {
        return $this->id;
    }


    public function getserviceCode()
    {
        return $this->serviceCode;
    }

    public function setserviceCode($serviceCode)
    {
        $this->serviceCode = $serviceCode;
        return $this;
    }

    public function getdenominationId()
    {
        return $this->denominationId;
    }

    public function setdenominationId($denominationId)
    {
        $this->denominationId = $denominationId;
        return $this;
    }

    public function getserviceProviderCode()
    {
        return $this->serviceProviderCode;
    }

    public function setserviceProviderCode($serviceProviderCode)
    {
        $this->serviceProviderCode = $serviceProviderCode;
        return $this;
    }

    public function getAmount()
    {
        return $this->Amount;
    }

    public function setAmount($Amount)
    {
        $this->Amount = $Amount;
        return $this;
    }

    public function getdenominationDescription()
    {
        return $this->denominationDescription;
    }

    public function setdenominationDescription($denominationDescription)
    {
        $this->denominationDescription = $denominationDescription;
        return $this;
    }

    function getCommission()
    {
        return $this->Commission;
    }

    function setCommission($Commission)
    {
        $this->Commission = $Commission;
        return $this;
    }

    function getparentConfigurationDataId()
    {
        return $this->parentConfigurationDataId;
    }

    function setparentConfigurationDataId($parentConfigurationDataId)
    {
        $this->parentConfigurationDataId = $parentConfigurationDataId;
        return $this;
    }
}
