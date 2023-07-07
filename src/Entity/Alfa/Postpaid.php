<?php

namespace App\Entity\Alfa;

use App\Entity\Traits\DateTrait;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity
 * @ORM\Table(name="postpaid")
 */
class Postpaid
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
     * @ORM\Column(name="transactionId",type="integer")
     */
    private $transactionId;

    /**
     * 
     * @ORM\Column(name="Fees",type="integer")
     */
    private $Fees;

    /**
     * 
     * @ORM\Column(name="Fees1",type="integer")
     */
    private $Fees1;

    /**
     * 
     * @ORM\Column(name="Amount")
     */
    private $Amount;

    /**
     * 
     * @ORM\Column(name="Amount1")
     */
    private $Amount1;

    /**
     * 
     * @ORM\Column(name="Amount2")
     */
    private $Amount2;

    /**
     * 
     * @ORM\Column(name="ReferenceNumber",type="integer")
     */
    private $ReferenceNumber;

    /**
     * 
     * @ORM\Column(name="InformativeOriginalWSAmount")
     */
    private $InformativeOriginalWSAmount;

    /**
     * 
     * @ORM\Column(name="TotalAmount")
     */
    private $TotalAmount;

    /**
     * 
     * @ORM\Column(name="Currency",type="string")
     */
    private $Currency;

    /**
     * 
     * @ORM\Column(name="Rounding",type="integer")
     */
    private $Rounding;

    /**
     * 
     * @ORM\Column(name="AdditionalFees",type="integer")
     */
    private $AdditionalFees;

    /**
     * 
     * @ORM\Column(name="suyoolUserId",type="integer")
     */
    private $suyoolUserId;

    /**
     * 
     * @ORM\Column(name="pin",type="integer")
     */
    private $pin;



    public function getId()
    {
        return $this->id;
    }


    public function getTransactionId()
    {
        return $this->transactionId;
    }

    public function setTransactionId($transactionId)
    {
        $this->transactionId = $transactionId;
    }

    public function getFees()
    {
        return $this->Fees;
    }

    public function setFees($Fees)
    {
        $this->Fees = $Fees;
    }

    public function getFees1()
    {
        return $this->Fees1;
    }

    public function setFees1($Fees1)
    {
        $this->Fees1 = $Fees1;
    }

    public function getAmount()
    {
        return $this->Amount;
    }

    public function setAmount($Amount)
    {
        $this->Amount = $Amount;
    }


    public function getAmount1()
    {
        return $this->Amount1;
    }

    public function setAmount1($Amount1)
    {
        $this->Amount1 = $Amount1;
    }

    public function getAmount2()
    {
        return $this->Amount2;
    }

    public function setAmount2($Amount2)
    {
        $this->Amount2 = $Amount2;
    }

    public function getReferenceNumber()
    {
        return $this->ReferenceNumber;
    }

    public function setReferenceNumber($ReferenceNumber)
    {
        $this->ReferenceNumber = $ReferenceNumber;
    }

    public function getInformativeOriginalWSAmount()
    {
        return $this->InformativeOriginalWSAmount;
    }

    public function setInformativeOriginalWSAmount($InformativeOriginalWSAmount)
    {
        $this->InformativeOriginalWSAmount = $InformativeOriginalWSAmount;
    }

    public function getTotalAmount()
    {
        return $this->TotalAmount;
    }

    public function setTotalAmount($TotalAmount)
    {
        $this->TotalAmount = $TotalAmount;
    }

    public function getCurrency()
    {
        return $this->Currency;
    }

    public function setCurrency($Currency)
    {
        $this->Currency = $Currency;
    }

    public function getRounding()
    {
        return $this->Rounding;
    }

    public function setRounding($Rounding)
    {
        $this->Rounding = $Rounding;
    }

    public function getAdditionalFees()
    {
        return $this->AdditionalFees;
    }

    public function setAdditionalFees($AdditionalFees)
    {
        $this->AdditionalFees = $AdditionalFees;
    }

    public function getSuyoolUserId()
    {
        return $this->suyoolUserId;
    }

    public function setSuyoolUserId($suyoolUserId)
    {
        $this->suyoolUserId = $suyoolUserId;
    }

    public function getPin()
    {
        return $this->pin;
    }

    public function setPin($pin)
    {
        $this->pin = $pin;
    }
}