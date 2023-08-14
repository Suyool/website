<?php

namespace App\Entity\Estore;

use App\Entity\Traits\DateTrait;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity
 * @ORM\Table(name="suyool_estore.company")
 */
class Company
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
     * @ORM\Column(name="companyId",type="integer")
     */
    private $companyId;

    /**
     * 
     * @ORM\Column(name="companyDescription")
     */
    private $companyDescription;

    /**
     * 
     * @ORM\Column(name="internalCode",type="string")
     */
    private $internalCode;
    
    /**
     * 
     * @ORM\Column(name="productId",type="integer")
     */
    private $productId;

    /**
     * 
     * @ORM\Column(name="productDescription")
     */
    private $productDescription;

    /**
     * 
     * @ORM\Column(name="servicesCode",type="integer")
     */
    private $servicesCode;

    /**
     * 
     * @ORM\Column(name="servicesProviderCode",type="integer")
     */
    private $servicesProviderCode;




    public function getId()
    {
        return $this->id;
    }


    public function getservicesCode()
    {
        return $this->servicesCode;
    }

    public function setservicesCode($servicesCode)
    {
        $this->servicesCode = $servicesCode;
        return $this;
    }

    public function getinternalCode()
    {
        return $this->internalCode;
    }

    public function setinternalCode($internalCode)
    {
        $this->internalCode = $internalCode;
        return $this;
    }

    public function getservicesProviderCode()
    {
        return $this->servicesProviderCode;
    }

    public function setservicesProviderCode($servicesProviderCode)
    {
        $this->servicesProviderCode = $servicesProviderCode;
        return $this;
    }

    public function getcompanyId()
    {
        return $this->companyId;
    }

    public function setcompanyId($companyId)
    {
        $this->companyId = $companyId;
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

    function getcompanyDescription()
    {
        return $this->companyDescription;
    }

    function setcompanyDescription($companyDescription)
    {
        $this->companyDescription = $companyDescription;
        return $this;
    }

    function getproductDescription()
    {
        return $this->productDescription;
    }

    function setproductDescription($productDescription)
    {
        $this->productDescription = $productDescription;
        return $this;
    }
}