<?php

namespace App\Entity\Simly;

use Doctrine\ORM\Mapping as ORM;

/**
// * @ORM\Entity(repositoryClass="App\Repository\SimlyOrdersRepository")
 * @ORM\Table(name="esims")
 */

class Esim
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $esimId;

    /**
     * @ORM\Column(type="integer")
     */
    private $suyoolUserId;

    /**
     * @ORM\Column(type="string")
     */
    private $status;

    /**
     * @ORM\Column(type="string")
     */
    private $smdp;

    /**
     * @ORM\Column(type="string")
     */
    private $matchingId;

    /**
     * @ORM\Column(type="string")
     */
    private $qrCodeImageUrl;

    /**
     * @ORM\Column(type="string")
     */
    private $qrCodeString;

    /**
     * @ORM\Column(type="string")
     */
    private $topups;

    /**
     * @ORM\Column(type="string")
     */
    private $transaction;

    /**
     * @ORM\Column(type="string")
     */
    private $plan;

    /**
     * @ORM\Column(type="string")
     */
    private $country;


    /**
     * @ORM\Column(type="string")
     */
    private $allowedPlans;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEsimId(): ?string
    {
        return $this->esimId;
    }

    public function setEsimId(string $esimId): self
    {
        $this->esimId = $esimId;
        return $this;
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

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getSmdp(): ?string
    {
        return $this->smdp;
    }

    public function setSmdp(string $smdp): self
    {
        $this->smdp = $smdp;
        return $this;
    }

    public function getMatchingId(): ?string
    {
        return $this->matchingId;
    }


    public function setMatchingId(string $matchingId): self
    {
        $this->matchingId = $matchingId;
        return $this;
    }

    public function getQrCodeImageUrl(): ?string
    {
        return $this->qrCodeImageUrl;
    }

    public function setQrCodeImageUrl(string $qrCodeImageUrl): self
    {
        $this->qrCodeImageUrl = $qrCodeImageUrl;
        return $this;
    }

    public function getQrCodeString(): ?string
    {
        return $this->qrCodeString;
    }

    public function setQrCodeString(string $qrCodeString): self
    {
        $this->qrCodeString = $qrCodeString;
        return $this;
    }

    public function getTopups(): ?string
    {
        return $this->topups;
    }

    public function setTopups(string $topups): self
    {
        $this->topups = $topups;
        return $this;
    }

    public function getTransaction(): ?string
    {
        return $this->transaction;
    }

    public function setTransaction(string $transaction): self
    {
        $this->transaction = $transaction;
        return $this;
    }

    public function getPlan(): ?string
    {
        return $this->plan;
    }

    public function setPlan(string $plan): self
    {
        $this->plan = $plan;
        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;
        return $this;
    }

    public function getAllowedPlans(): ?string
    {
        return $this->allowedPlans;
    }

    public function setAllowedPlans(string $allowedPlans): self
    {
        $this->allowedPlans = $allowedPlans;
        return $this;
    }

}