<?php


// src/Entity/Transaction.php

namespace App\Entity\Iveri;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="transactions")
 */
class transactions
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="trace", fetch="EAGER")
     * @ORM\JoinColumn(name="trace_id", referencedColumnName="id")
     */
    private $trace;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $merchantReference;

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
     *  @ORM\Column(name="authorisationCode",type="string")
     */
    private $authorisationCode;

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

    public function getTrace(): ?trace
    {
        return $this->trace;
    }

    public function setTrace(trace $trace): self
    {
        $this->trace = $trace;
        return $this;
    }

    public function getAuthCode()
    {
        return $this->authorisationCode;
    }

    public function setAuthCode($authorisationCode)
    {
        $this->authorisationCode = $authorisationCode;

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

    public function getMerchantReference(): ?string
    {
        return $this->merchantReference;
    }

    public function setMerchantReference(string $merchantReference): self
    {
        $this->merchantReference = $merchantReference;

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
