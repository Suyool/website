<?php

namespace App\Entity\Estore;

use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity
 * @ORM\Table(name="suyool_estore.product")
 */
class Product
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
     * @ORM\Column(name="productId",type="integer")
     */
    private $productId;

    /**
     * 
     * @ORM\Column(name="productName")
     */
    private $productName;

    /**
     * 
     * @ORM\Column(name="denominationId",type="string")
     */
    private $denominationId;
    
    /**
     * 
     * @ORM\Column(name="denominationDescription",type="integer")
     */
    private $denominationDescription;

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

    public function getproductId()
    {
        return $this->productId;
    }

    public function setproductId($productId)
    {
        $this->productId = $productId;
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

    function getproductName()
    {
        return $this->productName;
    }

    function setproductName($productName)
    {
        $this->productName = $productName;
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