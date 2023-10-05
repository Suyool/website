<?php


// src/Entity/Transaction.php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="transactions")
 * @ORM\HasLifecycleCallbacks
 */
class Transaction
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", name="users_id", length=50)
     */
    private $users;

    /**
     * @ORM\Column(type="string", name="orderId", length=50)
     */
    private $orderId;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=0)
     */
    private $amount;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $currency;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $description;

    /**
     * @ORM\Column(length=200)
     */
    private $response;

    /**
     * @ORM\Column(type="integer")
     */
    private $respCode;

    /**
     * @ORM\Column(type="integer")
     */
    private $flagCode;

    /**
     * @ORM\Column(type="string")
     */
    private $error;

    /**
     * @ORM\Column(type="datetime",nullable=true)
     */
    private $created;

    /**
     * @ORM\PrePersist
     */
    public function updatedTimestamps()
    {
        $this->created = new \DateTime('now');
    }

    // Getters and Setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrderId(): ?string
    {
        return $this->orderId;
    }

    public function setOrderId(string $orderId): self
    {
        $this->orderId = $orderId;

        return $this;
    }

    public function getUsersId(): ?string
    {
        return $this->users;
    }

    public function setUsersId(string $users): self
    {
        $this->users = $users;

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

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function setResponse($response): self
    {
        $this->response = $response;

        return $this;
    }

    public function getRespCode()
    {
        return $this->respCode;
    }

    public function setRespCode($respCode): self
    {
        $this->respCode = $respCode;

        return $this;
    }

    public function getflagCode()
    {
        return $this->flagCode;
    }

    public function setflagCode($flagCode): self
    {
        $this->flagCode = $flagCode;

        return $this;
    }

    public function getError()
    {
        return $this->error;
    }

    public function setError($error): self
    {
        $this->error = $error;

        return $this;
    }

    public function getCreated(): ?\DateTimeInterface
    {
        return $this->created;
    }
}
