<?php

namespace App\Entity\Vat;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="vatrequest")
 */
class VatRequest
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $suyoolUserId;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $documentNumber;

    /**
     * @ORM\Column(type="text")
     */
    private $errorDesc;

    /**
     * @ORM\Column(type="text")
     */
    private $response;

    /**
     * @ORM\Column(type="integer")
     */
    private $transactionId;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $fees;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $fees1;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $amount;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $amount1;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $amount2;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $totalAmount;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $moFTotalAmount;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $currency;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $moFFiscalStamp;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $rounding;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $additionalFees;

    /**
     * @ORM\Column(name="displayedFees")
     */
    private $displayedFees;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSuyoolUserId(): ?int
    {
        return $this->suyoolUserId;
    }

    public function setSuyoolUserId(int $suyoolUserId): self
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

    public function getErrorDesc(): ?string
    {
        return $this->errorDesc;
    }

    public function setErrorDesc(string $errorDesc): self
    {
        $this->errorDesc = $errorDesc;
        return $this;
    }

    public function getResponse(): ?string
    {
        return $this->response;
    }

    public function setResponse(string $response): self
    {
        $this->response = $response;
        return $this;
    }

    public function getTransactionId(): ?int
    {
        return $this->transactionId;
    }

    public function setTransactionId(int $transactionId): self
    {
        $this->transactionId = $transactionId;
        return $this;
    }

    public function getFees(): ?string
    {
        return $this->fees;
    }

    public function setFees(string $fees): self
    {
        $this->fees = $fees;
        return $this;
    }

    public function getFees1(): ?string
    {
        return $this->fees1;
    }

    public function setFees1(string $fees1): self
    {
        $this->fees1 = $fees1;
        return $this;
    }

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function setAmount(string $amount): self
    {
        $this->amount = $amount;
        return $this;
    }

    public function getAmount1(): ?string
    {
        return $this->amount1;
    }

    public function setAmount1(string $amount1): self
    {
        $this->amount1 = $amount1;
        return $this;
    }

    public function getAmount2(): ?string
    {
        return $this->amount2;
    }

    public function setAmount2(string $amount2): self
    {
        $this->amount2 = $amount2;
        return $this;
    }

    public function getTotalAmount(): ?string
    {
        return $this->totalAmount;
    }

    public function setTotalAmount(string $totalAmount): self
    {
        $this->totalAmount = $totalAmount;
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

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;
        return $this;
    }

    public function getMoFFiscalStamp(): ?string
    {
        return $this->moFFiscalStamp;
    }

    public function setMoFFiscalStamp(string $moFFiscalStamp): self
    {
        $this->moFFiscalStamp = $moFFiscalStamp;
        return $this;
    }

    public function getRounding(): ?string
    {
        return $this->rounding;
    }

    public function setRounding(string $rounding): self
    {
        $this->rounding = $rounding;
        return $this;
    }

    public function getAdditionalFees(): ?string
    {
        return $this->additionalFees;
    }

    public function setAdditionalFees(string $additionalFees): self
    {
        $this->additionalFees = $additionalFees;
        return $this;
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
}
