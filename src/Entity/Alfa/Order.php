<?php

namespace App\Entity\Alfa;

use App\Entity\Traits\DateTrait;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity
 * @ORM\Table(name="orders")
 */
class Order
{

    use DateTrait;
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * 
     * @ORM\Column(name="suyoolUserId")
     */
    private $suyoolUserId;

    /**
     * 
     * @ORM\Column(name="postpaid_Id")
     */
    private $postpaid_Id;

    /**
     * 
     * @ORM\Column(name="prepaid_Id")
     */
    private $prepaid_Id;

    /**
     * 
     * @ORM\Column(type="string")
     */
    private $status;

    /**
     * 
     * @ORM\Column(type="integer")
     */
    private $amount;

    /**
     * 
     * @ORM\Column(type="string")
     */
    private $currency;

    /**
     * 
     * @ORM\Column(type="integer")
     */
    private $transId;


    public function getId()
    {
        return $this->id;
    }

    public function getsuyoolUserId()
    {
        return $this->suyoolUserId;
    }

    public function setsuyoolUserId($suyoolUserId)
    {
        $this->suyoolUserId = $suyoolUserId;
        return $this;
    }

    public function getstatus()
    {
        return $this->status;
    }

    public function setstatus($status)
    {
        $this->status = $status;
        return $this;
    }

    public function getamount()
    {
        return $this->amount;
    }

    public function setamount($amount)
    {
        $this->amount = $amount;
        return $this;
    }

    public function setcurrency($currency)
    {
        $this->currency = $currency;
        return $this;
    }

    public function getcurrency()
    {
        return $this->currency;
    }

    public function setpostpaid_Id($postpaid_Id)
    {
        $this->postpaid_Id = $postpaid_Id;
        return $this;
    }

    public function getpostpaid_Id()
    {
        return $this->postpaid_Id;
    }

    public function setprepaid_Id($prepaid_Id)
    {
        $this->prepaid_Id = $prepaid_Id;
        return $this;
    }

    public function getprepaid_Id()
    {
        return $this->prepaid_Id;
    }

    public function gettransId()
    {
        return $this->transId;
    }

    public function settransId($transId)
    {
        $this->transId = $transId;
        return $this;
    }
}
