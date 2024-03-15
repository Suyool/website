<?php

namespace App\Entity\Simly;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SimlyOrders2Repository")
 * @ORM\Table(name="orders")
 */
class Order
{
    public static $statusOrder = array("PENDING" => "pending", "HELD" => "held", "PURCHASED" => "purchased", "COMPLETED" => "completed", "CANCELED" => "canceled");

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     * @ORM\ManyToOne(targetEntity="App\Entity\Simly\Esim",fetch="EAGER")
     * @ORM\JoinColumn(name="esims_id", referencedColumnName="id")
     */
    private $esims_id;

    /**
     * @ORM\Column(type="string")
     */
    private $status;

    /**
     * @ORM\Column(type="decimal", scale=2))
     */
    private $amount;

    /**
     * @ORM\Column(type="decimal", scale=2))
     */
    private $fees;

    /**
     * @ORM\Column(type="string")
     */
    private $currency;

    /**
     * @ORM\Column(type="integer")
     */
    private $transId;

    /**
     * @ORM\Column(type="string")
     */
    private $type;


    /**
     * @ORM\Column(name="errorInfo")
     */
    private $error;

    /**
     * @ORM\Column(name="suyoolUserId")
     */
    private $suyoolUserId;

    /**
     * @ORM\Column(name="isOffre")
     */
    private $isOffre = 0;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEsimsId(): ?int
    {
        return $this->esims_id;
    }

    public function setEsimsId(int $esims_id): self
    {
        $this->esims_id = $esims_id;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;
        return $this;
    }

    public function getFees(): ?float
    {
        return $this->fees;
    }

    public function setFees(float $fees): self
    {
        $this->fees = $fees;
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

    public function getTransId(): ?int
    {
        return $this->transId;
    }

    public function setTransId(int $transId): self
    {
        $this->transId = $transId;
        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getError(): ?string
    {
        return $this->error;
    }

    public function setError(string $error): self
    {
        $this->error = $error;
        return $this;
    }

    public function getSuyoolUserId(): ?string
    {
        return $this->suyoolUserId;
    }

    public function setSuyoolUserId(string $suyoolUserId): self
    {
        $this->suyoolUserId = $suyoolUserId;
        return $this;
    }

    public function getIsOffre()
    {
        return $this->isOffre;
    }

    public function setIsOffre($isOffre)
    {
        $this->isOffre = $isOffre;
        return $this;
    }

}
