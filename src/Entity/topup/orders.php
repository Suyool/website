<?php

namespace App\Entity\topup;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PaymentRepository")
 * @ORM\Table(name="transactions")
 */
class orders
{

    public static $statusOrder = array("COMPLETED" => "completed", "PENDING" => "pending", "CANCELED" => "canceled","HELD"=>"held",'PAID'=>"paid");


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
     * @ORM\Column(name="transId")
     */
    private $transId;

    /**
     * @ORM\Column(name="amount")
     */
    private $amount;

    /**
     * @ORM\Column(type="string")
     */
    private $currency;

    /**
     * @ORM\Column(type="string")
     */
    private $type;

    /**
     * @ORM\Column(name="status")
     */
    private $status;

     /**
     * @ORM\Column(name="attempt")
     */
    private $attempt = 1;

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

    public function setstatus($status)
    {
        $this->status = $status;
        return $this;
    }

    public function getstatus()
    {
        return $this->status;
    }

    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    public function settransId($transId)
    {
        $this->transId = $transId;
        return $this;
    }

    public function getAttempt()
    {
        return $this->attempt;
    }

    public function setAttempt($attempt)
    {
        $this->attempt = $attempt;
        return $this;
    }

    public function getCreated()
    {
        return $this->created;
    }
}
