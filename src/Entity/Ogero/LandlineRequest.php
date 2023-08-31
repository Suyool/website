<?php

namespace App\Entity\Ogero;

use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity
 * @ORM\Table(name="landlinerequest")
 */
class LandlineRequest
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="suyoolUserId",type="integer")
     */
    private $suyoolUserId;

    /**
     * @ORM\Column(name="gsmNumber")
     */
    private $gsmNumber;

     /**
     * @ORM\Column(name="response")
     */
    private $response;

     /**
     * @ORM\Column(name="errorDesc")
     */
    private $errorDesc;

    /**
     * @ORM\Column(name="transactionId", nullable="true")
     */
    private $transactionId;

    /**
     * @ORM\Column(name="ogeroBills", nullable="true")
     */
    private $ogeroBills;

    /**
     * @ORM\Column(name="ogeroPenalty", nullable="true")
     */
    private $ogeroPenalty;

    /**
     * @ORM\Column(name="ogeroInitiationDate", nullable="true")
     */
    private $ogeroInitiationDate;

    /**
     * @ORM\Column(name="ogeroClientName", nullable="true")
     */
    private $ogeroClientName;

    /**
     * @ORM\Column(name="ogeroAddress", nullable="true")
     */
    private $ogeroAddress;

    /**
     * @ORM\Column(name="currency", nullable="true")
     */
    private $currency;

    /**
     * @ORM\Column(name="amount", nullable="true")
     */
    private $amount;

    /**
     * @ORM\Column(name="amount1", nullable="true")
     */
    private $amount1;

    /**
     * @ORM\Column(name="amount2", nullable="true")
     */
    private $amount2;

    /**
     * @ORM\Column(name="totalAmount", nullable="true")
     */
    private $totalAmount;

    /**
     * @ORM\Column(name="ogeroTotalAmount", nullable="true")
     */
    private $ogeroTotalAmount;

    /**
     * @ORM\Column(name="ogeroFees", nullable="true")
     */
    private $ogeroFees;

    /**
     * @ORM\Column(name="additionalFees", nullable="true")
     */
    private $additionalFees;

    /**
     * @ORM\Column(name="fees",type="integer", nullable="true")
     */
    private $fees;

    /**
     * @ORM\Column(name="fees1",type="integer", nullable="true")
     */
    private $fees1;

    /**
     * @ORM\Column(name="displayedFees", nullable="true")
     */
    private $displayedFees;
    /**
     * @ORM\Column(name="rounding",type="integer", nullable="true")
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

    public function getadditionalFees()
    {
        return $this->additionalFees;
    }

    public function setadditionalFees($additionalFees)
    {
        $this->additionalFees = $additionalFees;
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

    public function getresponse()
    {
        return $this->response;
    }

    public function setresponse($response)
    {
        $this->response = $response;
        return $this;
    }

    public function geterrordesc()
    {
        return $this->errorDesc;
    }

    public function seterrordesc($errorDesc)
    {
        $this->errorDesc = $errorDesc;
        return $this;
    }

    public function getogeroFees()
    {
        return $this->ogeroFees;
    }

    public function setogeroFees($ogeroFees)
    {
        $this->ogeroFees = $ogeroFees;
        return $this;
    }

    public function getogeroTotalAmount()
    {
        return $this->ogeroTotalAmount;
    }

    public function setogeroTotalAmount($ogeroTotalAmount)
    {
        $this->ogeroTotalAmount = $ogeroTotalAmount;
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

    public function getogeroClientName()
    {
        return $this->ogeroClientName;
    }

    public function setogeroClientName($ogeroClientName)
    {
        $this->ogeroClientName = $ogeroClientName;
        return $this;
    }

    public function getogeroAddress()
    {
        return $this->ogeroAddress;
    }

    public function setogeroAddress($ogeroAddress)
    {
        $this->ogeroAddress = $ogeroAddress;
        return $this;
    }

    public function gettotalAmount()
    {
        return $this->totalAmount;
    }

    public function settotalAmount($totalAmount)
    {
        $this->totalAmount = $totalAmount;
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

    public function getcurrency()
    {
        return $this->currency;
    }

    public function setcurrency($currency)
    {
        $this->currency = $currency;
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

    public function getogeroBills()
    {
        return $this->ogeroBills;
    }

    public function setogeroBills($ogeroBills)
    {
        $this->ogeroBills = $ogeroBills;
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

    function getogeroInitiationDate()
    {
        return $this->ogeroInitiationDate;
    }

    function setogeroInitiationDate($ogeroInitiationDate)
    {
        $this->ogeroInitiationDate = $ogeroInitiationDate;
        return $this;
    }

    function getogeroPenalty()
    {
        return $this->ogeroPenalty;
    }

    function setogeroPenalty($ogeroPenalty)
    {
        $this->ogeroPenalty = $ogeroPenalty;
        return $this;
    }
}
