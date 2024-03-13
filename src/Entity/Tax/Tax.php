<?php

namespace App\Entity\Tax;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="tax")
 */
class Tax
{
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
     * @ORM\Column(type="string", length=30)
     */
    private $documentNumber;

    /**
     * @ORM\Column(name="transactionId")
     */
    private $transactionId;

    /**
     * @ORM\Column(name="transactionDescription")
     */
    private $transactionDescription;

    /**
     * @ORM\Column(name="referenceNumber")
     */
    private $referenceNumber;

    /**
     * @ORM\Column(name="currency")
     */
    private $currency;

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
     * @ORM\Column(name="totalAmount")
     */
    private $totalAmount;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $moFTotalAmount;

    /**
     * @ORM\Column(name="additionalFees")
     */
    private $additionalFees;

    /**
     * @ORM\Column(name="fees",type="integer")
     */
    private $fees;

    /**
     * @ORM\Column(name="fees1",type="integer")
     */
    private $fees1;

    /**
     * @ORM\Column(name="displayedFees")
     */
    private $displayedFees;

    /**
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

    public function getadditionalFees()
    {
        return $this->additionalFees;
    }

    public function setadditionalFees($additionalFees)
    {
        $this->additionalFees = $additionalFees;
        return $this;
    }

    public function gettransactionDescription()
    {
        return $this->transactionDescription;
    }

    public function settransactionDescription($transactionDescription)
    {
        $this->transactionDescription = $transactionDescription;
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

    public function getfees()
    {
        return $this->fees;
    }

    public function setfees($fees)
    {
        $this->fees = $fees;
        return $this;
    }

    public function getMoFTotalAmount(): ?string
    {
        return $this->moFTotalAmount;
    }

    public function setMoFTotalAmount(string $moFTotalAmount): self
    {
        $this->moFTotalAmount = $moFTotalAmount;
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


    public function getDocumentNumber(): ?string
    {
        return $this->documentNumber;
    }

    public function setDocumentNumber(string $documentNumber): self
    {
        $this->documentNumber = $documentNumber;
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
