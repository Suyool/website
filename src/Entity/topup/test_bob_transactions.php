<?php


// src/Entity/Transaction.php

namespace App\Entity\topup;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="test_bob_transactions")
 */
class test_bob_transactions
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\topup\test_session", fetch="EAGER")
     * @ORM\JoinColumn(name="session_id", referencedColumnName="id")
     */
    private $session;

    /**
     * @ORM\Column(length=200)
     */
    private $response;

    /**
     * @ORM\Column(type="string")
     */
    private $status;

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

    public function __construct()
    {
        $this->created = new \DateTime();
    }

    // Getters and Setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSession(): ?test_session
    {
        return $this->session;
    }

    public function setSession(test_session $session): self
    {
        $this->session = $session;
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

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status): self
    {
        $this->status = $status;

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
