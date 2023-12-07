<?php

namespace App\Entity\topup;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AttemptsRepository")
 * @ORM\Table(name="attempts")
 */
class attempts
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="response")
     */
    private $response;

     /**
     * @ORM\Column(name="suyoolUserId")
     */
    private $suyoolUserId;

     /**
     * @ORM\Column(name="receiverPhone")
     */
    private $receiverPhone;

     /**
     * @ORM\Column(name="senderPhone")
     */
    private $senderPhone;

    /**
     * @ORM\Column(name="transactionId")
     */
    private $transactionId;

    /**
     * @ORM\Column(name="amount")
     */
    private $amount;

    /**
     * @ORM\Column(name="currency")
     */
    private $currency;

     /**
     * @ORM\Column(name="status")
     */
    private $status;

     /**
     * @ORM\Column(name="result")
     */
    private $result;

     /**
     * @ORM\Column(name="authenticationStatus")
     */
    private $authenticationStatus;

     /**
     * @ORM\Column(name="card")
     */
    private $card;

     /**
     * @ORM\Column(name="name")
     */
    private $name;

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

    public function getTransactionId()
    {
        return $this->transactionId;
    }

    public function setTransactionId($transactionId)
    {
        $this->transactionId = $transactionId;
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

    public function getReceiverPhone()
    {
        return $this->receiverPhone;
    }

    public function setReceiverPhone($receiverPhone)
    {
        $this->receiverPhone = $receiverPhone;
        return $this;
    }

    public function getSenderPhone()
    {
        return $this->senderPhone;
    }

    public function setSenderPhone($senderPhone)
    {
        $this->senderPhone = $senderPhone;
        return $this;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function setAmount($amount)
    {
        $this->amount = $amount;
        return $this;
    }

    public function getCurrency()
    {
        return $this->currency;
    }

    public function setCurrency($currency)
    {
        $this->currency = $currency;
        return $this;
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function setResponse($response)
    {
        $this->response = $response;
        return $this;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    public function getResult()
    {
        return $this->result;
    }

    public function setResult($result)
    {
        $this->result = $result;
        return $this;
    }

    public function getAuthStatus()
    {
        return $this->authenticationStatus;
    }

    public function setAuthStatus($authenticationStatus)
    {
        $this->authenticationStatus = $authenticationStatus;
        return $this;
    }

    public function getCard()
    {
        return $this->card;
    }

    public function setCard($card)
    {
        $this->card = $card;
        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function getCreated()
    {
        return $this->created;
    }
}
