<?php

namespace App\Entity\Vat;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OgeroRepository")
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
     * @ORM\OneToOne(targetEntity="Vat",fetch="EAGER")
     * @ORM\JoinColumn(name="vat_id", referencedColumnName="id")
     */
    private $vat;

    /**
     * @ORM\Column(type="string")
     */
    private $status;

    /**
     * @ORM\Column(name="errorInfo")
     */
    private $error;

    /**
     * @ORM\Column(type="integer")
     */
    private $amount;

    /**
     * @ORM\Column(type="integer")
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

    public function getVatId()
    {
        if ($this->vat) {
            return $this->vat->getId();
        }

        return null;
    }
    public function setVatId(?Vat $vat): self
    {
        $this->vat = $vat;
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

    public function getfees()
    {
        return $this->fees;
    }

    public function setfees($fees)
    {
        $this->fees = $fees;
        return $this;
    }
}
