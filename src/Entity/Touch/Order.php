<?php

namespace App\Entity\Touch;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TouchOrdersRepository")
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
     * @ORM\Column(name="errorInfo")
     */
    private $error;

    /**
     * @ORM\OneToOne(targetEntity="Postpaid",fetch="EAGER")
     * @ORM\JoinColumn(name="postpaid_id", referencedColumnName="id")
     */
    private $postpaid;

    /**
     * @ORM\OneToOne(targetEntity="Prepaid",fetch="EAGER")
     * @ORM\JoinColumn(name="prepaid_id", referencedColumnName="id")
     */
    private $prepaid;

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

    public function geterror()
    {
        return $this->error;
    }

    public function seterror($error)
    {
        $this->error = $error;
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

    public function getpostpaidId()
    {
        if ($this->postpaid) {
            return $this->postpaid->getId();
        }

        return null;
    }
    public function setpostpaidId(?Postpaid $postpaid_id): self
    {
        $this->postpaid = $postpaid_id;
        return $this;
    }

    public function getprepaidId()
    {
        if ($this->prepaid) {
            return $this->prepaid->getId();
        }

        return null;
    }
    public function setprepaidId(?Prepaid $prepaid_id): self
    {
        $this->prepaid = $prepaid_id;
        return $this;
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
