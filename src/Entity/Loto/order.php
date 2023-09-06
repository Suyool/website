<?php

namespace App\Entity\Loto;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OrdersRepository")
 * @ORM\Table(name="orders")
 */
class order
{
    public static $statusOrder = array("COMPLETED" => "completed", "PENDING" => "pending", "HELD" => "held", "PURCHASED" => "purchased", "CANCELED" => "canceled");

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
     * @ORM\Column(name="status",type="string")
     */
    private $status;

    /**
     * @ORM\Column(type="string")
     */
    private $subscription;

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

    /**
     * @ORM\Column(name="created",type="datetime",nullable=true)
     */
    private $created;

    public function __construct()
    {
        $this->created = new \DateTime();
    }

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

    public function getsubscription()
    {
        return $this->subscription;
    }

    public function setsubscription($subscription)
    {
        $this->subscription = $subscription;
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

    public function gettransId()
    {
        return $this->transId;
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

    public function seterror($error)
    {
        $this->error = $error;
        return $this;
    }

    public function geterror()
    {
        return $this->error;
    }

    public function settransId($transId)
    {
        $this->transId = $transId;
        return $this;
    }

    public function getCreated()
    {
        return $this->created;
    }
}
