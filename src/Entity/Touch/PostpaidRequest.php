<?php

namespace App\Entity\Touch;

use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity
 * @ORM\Table(name="suyool_touch.postpaidRequest")
 */
class PostpaidRequest
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
     * @ORM\Column(name="token")
     */
    private $token;

    /**
     * @ORM\Column(name="error")
     */
    private $error;

    /**
     * @ORM\Column(name="s2error")
     */
    private $s2error;

    /**
     * @ORM\Column(name="requestId")
     */
    private $requestId;

    /**
     * @ORM\Column(name="currency")
     */
    private $currency;

    /**
     * @ORM\Column(name="pin")
     */
    private $pin;

    /**
     * @ORM\Column(name="transactionId")
     */
    private $transactionId;

    /**
     * @ORM\Column(name="fees")
     */
    private $fees;

    /**
     * @ORM\Column(name="fees1")
     */
    private $fees1;

    /**
     * @ORM\Column(name="amount")
     */
    private $amount;

    /**
     * @ORM\Column(name="amount1")
     */
    private $amount1;

    /**
     * @ORM\Column(name="amount2")
     */
    private $amount2;

    /**
     * @ORM\Column(name="referenceNumber")
     */
    private $referenceNumber;

    /**
     * @ORM\Column(name="informativeOriginalWSamount")
     */
    private $informativeOriginalWSamount;

    /**
     * @ORM\Column(name="totalamount")
     */
    private $totalamount;

    /**
     * @ORM\Column(name="rounding")
     */
    private $rounding;

    /**
     * @ORM\Column(name="additionalfees")
     */
    private $additionalfees;

    /**
     * @ORM\Column(name="invoiceNumber")
     */
    private $invoiceNumber;

    /**
     * @ORM\Column(name="paymentId")
     */
    private $paymentId;


    public function getId()
    {
        return $this->id;
    }


    public function getinvoiceNumber()
    {
        return $this->invoiceNumber;
    }

    public function setinvoiceNumber($invoiceNumber)
    {
        $this->invoiceNumber = $invoiceNumber;
        return $this;
    }

    public function getpaymentId()
    {
        return $this->paymentId;
    }

    public function setpaymentId($paymentId)
    {
        $this->paymentId = $paymentId;
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

    public function geterror()
    {
        return $this->error;
    }

    public function seterror($error)
    {
        $this->error = $error;
        return $this;
    }

    public function gets2error()
    {
        return $this->s2error;
    }

    public function sets2error($s2error)
    {
        $this->s2error = $s2error;
        return $this;
    }

    public function getrequestId()
    {
        return $this->requestId;
    }

    public function setrequestId($requestId)
    {
        $this->requestId = $requestId;
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

    public function gettoken()
    {
        return $this->token;
    }

    public function settoken($token)
    {
        $this->token = $token;
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
}
