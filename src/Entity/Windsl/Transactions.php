<?php

namespace App\Entity\Windsl;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="transactions")
 */
class Transactions
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
     * @ORM\ManyToOne(targetEntity="Users",fetch="EAGER")
     * @ORM\JoinColumn(name="users_id", referencedColumnName="id")
     */
    private $users;

    /**
     * @ORM\Column(type="string")
     */
    private $status;

    /**
     * @ORM\Column()
     */
    private $amount;

    /**
     * @ORM\Column()
     */
    private $fees;

    /**
     * @ORM\Column(type="string")
     */
    private $currency;

    /**
     * @ORM\Column(type="integer")
     */
    private $transId;

    /**
     * @ORM\Column(name="errorInfo")
     */
    private $error;
    
    /**
     * @ORM\Column(name="created")
     */
    private DateTime $created;

    public function __construct()
    {
        $this->created = new DateTime();
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

    public function getamount()
    {
        return $this->amount;
    }

    public function setamount($amount)
    {
        $this->amount = $amount;
        return $this;
    }

    public function getfees()
    {
        return $this->fees;
    }

    public function setfees($fees)
    {
        $this->fees = $fees;
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

    public function getUsersId()
    {
        if ($this->users) {
            return $this->users->getId();
        }

        return null;
    }
    public function setUserId(?Users $users): self
    {
        $this->users = $users;
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

    public function seterror($error)
    {
        $this->error = $error;
        return $this;
    }

    public function geterror()
    {
        return $this->error;
    }
}
