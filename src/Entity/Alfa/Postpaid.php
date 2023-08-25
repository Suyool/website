<?php

namespace App\Entity\Alfa;

use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity
 * @ORM\Table(name="postpaid")
 */
class Postpaid
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * 
     * @ORM\Column(name="suyoolUserId",type="integer")
     */
    private $suyoolUserId;

    /**
     * 
     * @ORM\Column(name="gsmNumber")
     */
    private $gsmNumber;

    /**
     * 
     * @ORM\Column(name="currency",type="string")
     */
    private $currency;

    /**
     * 
     * @ORM\Column(name="pin",type="integer")
     */
    private $pin;

    /**
     * 
     * @ORM\Column(name="transactionId")
     */
    private $transactionId;

    /**
     * 
     * @ORM\Column(name="transactionDescription")
     */
    private $transactionDescription;

    /**
     * 
     * @ORM\Column(name="status")
     */
    private $status;

    /**
     * 
     * @ORM\Column(name="fees",type="integer")
     */
    private $fees;

    /**
     * 
     * @ORM\Column(name="fees1",type="integer")
     */
    private $fees1;
    
    /**
     * 
     * @ORM\Column(name="additionalfees",type="integer")
     */
    private $additionalfees;

    /**
     * 
     * @ORM\Column(name="displayedFees")
     */
    private $displayedFees;

    /**
     * 
     * @ORM\Column(name="amount")
     */
    private $amount;

    /**
     * 
     * @ORM\Column(name="amount1")
     */
    private $amount1;

    /**
     * 
     * @ORM\Column(name="amount2")
     */
    private $amount2;

    /**
     * 
     * @ORM\Column(name="referenceNumber",type="integer")
     */
    private $referenceNumber;

    /**
     * 
     * @ORM\Column(name="informativeOriginalWSamount")
     */
    private $informativeOriginalWSamount;

    /**
     * 
     * @ORM\Column(name="totalamount")
     */
    private $totalamount;

    /**
     * 
     * @ORM\Column(name="rounding",type="integer")
     */
    private $rounding;






    public function getId()
    {
        return $this->id;
    }

    public function getdisplayedFees()
    {
        return $this->displayedFees;
    }

    public function setdisplayedFees($displayedFees)
    {
        $this->displayedFees = $displayedFees;
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

    public function getfees1()
    {
        return $this->fees1;
    }

    public function setfees1($fees1)
    {
        $this->fees1 = $fees1;
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


    public function getamount1()
    {
        return $this->amount1;
    }

    public function setamount1($amount1)
    {
        $this->amount1 = $amount1;
        return $this;
    }

    public function getamount2()
    {
        return $this->amount2;
    }

    public function setamount2($amount2)
    {
        $this->amount2 = $amount2;
        return $this;
    }

    public function getreferenceNumber()
    {
        return $this->referenceNumber;
    }

    public function setreferenceNumber($referenceNumber)
    {
        $this->referenceNumber = $referenceNumber;
        return $this;
    }

    public function getinformativeOriginalWSamount()
    {
        return $this->informativeOriginalWSamount;
    }

    public function setinformativeOriginalWSamount($informativeOriginalWSamount)
    {
        $this->informativeOriginalWSamount = $informativeOriginalWSamount;
        return $this;
    }

    public function gettotalamount()
    {
        return $this->totalamount;
    }

    public function settotalamount($totalamount)
    {
        $this->totalamount = $totalamount;
        return $this;
    }

    public function getcurrency()
    {
        return $this->currency;
    }

    public function setcurrency($currency)
    {
        $this->currency = $currency;
        return $this;
    }

    public function getrounding()
    {
        return $this->rounding;
    }

    public function setrounding($rounding)
    {
        $this->rounding = $rounding;
        return $this;
    }

    public function getadditionalfees()
    {
        return $this->additionalfees;
    }

    public function setadditionalfees($additionalfees)
    {
        $this->additionalfees = $additionalfees;
        return $this;
    }

    public function getSuyoolUserId()
    {
        return $this->suyoolUserId;
    }

    public function setSuyoolUserId($suyoolUserId)
    {
        $this->suyoolUserId = $suyoolUserId;
        return $this;
    }

    public function getPin()
    {
        return $this->pin;
    }

    public function setPin($pin)
    {
        $this->pin = $pin;
        return $this;
    }

    function getGsmNumber()
    {
        return $this->gsmNumber;
    }

    function setGsmNumber($gsmNumber)
    {
        $this->gsmNumber = $gsmNumber;
        return $this;
    }

    function getTransactionId()
    {
        return $this->transactionId;
    }

    function setTransactionId($transactionId)
    {
        $this->transactionId = $transactionId;
        return $this;
    }

    function getstatus()
    {
        return $this->status;
    }

    function setstatus($status)
    {
        $this->status = $status;
        return $this;
    }

    function gettransactionDescription()
    {
        return $this->transactionDescription;
    }

    function settransactionDescription($transactionDescription)
    {
        $this->transactionDescription = $transactionDescription;
        return $this;
    }
}
