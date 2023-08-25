<?php

namespace App\Entity\Shopify;


use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="merchant_credentials")
 */
class MerchantCredentials
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="shop", type="string", length=250, nullable=false)
     * @Assert\NotBlank
     */
    private $shop;

    /**
     * @var string
     *
     * @ORM\Column(name="accessToken", type="string", length=250, nullable=false)
     * @Assert\Length(max=4096)
     */
    private $accessToken;

    /**
     * @var int
     *
     * @ORM\Column(name="test_checked", type="integer", length=4, nullable=false)
     * @Assert\Length(max=4096)
     */
    private $testChecked;

    /**
     * @var int
     *
     * @ORM\Column(name="test_merchant_id", type="integer", length=11, nullable=true)
     */
    private $testMerchantId;

    /**
     * @var string
     *
     * @ORM\Column(name="test_certificate_key", type="string", length=250, nullable=true)
     */
    private $testCertificateKey;

    /**
     * @var boolean
     *
     * @ORM\Column(name="live_checked", type="integer", length=4, nullable=false)
     */
    private $liveChecked;

    /**
     * @var int
     *
     * @ORM\Column(name="live_merchant_id", type="integer", length=11, nullable=false)
     */
    private $liveMerchantId;

    /**
     * @var string
     *
     * @ORM\Column(name="live_certificate_key", type="string", length=250, nullable=false)
     */
    private $liveCertificateKey;

    private $created;

    /**
     * @ORM\Column(type="datetime", name="updated")
     */
    private $updated;

    /**
     * @ORM\PrePersist
     */
    public function updatedTimestamps()
    {
        $this->created = new \DateTime('now');
        $this->updated = new \DateTime('now');
    }

    /**
     * Set the value of id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * Get the value of id
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set the value of shop
     */
    public function setShop(string $shop): void
    {
        $this->shop = $shop;
    }

    /**
     * Get the value of shop
     */
    public function getShop(): ?string
    {
        return $this->shop;
    }

    /**
     * Set the value of accessToken
     */
    public function setAccessToken(string $accessToken): void
    {
        $this->accessToken = $accessToken;
    }

    /**
     * Get the value of accessToken
     */
    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    /**
     * Set the value of testChecked
     */
    public function setTestChecked(int $testChecked): void
    {
        $this->testChecked = $testChecked;
    }

    /**
     * Get the value of testChecked
     */
    public function getTestChecked(): ?int
    {
        return $this->testChecked;
    }

    /**
     * Set the value of testMerchantId
     */
    public function setTestMerchantId(?int $testMerchantId): void
    {
        $this->testMerchantId = $testMerchantId;
    }

    /**
     * Get the value of testMerchantId
     */
    public function getTestMerchantId(): ?int
    {
        return $this->testMerchantId;
    }

    /**
     * Set the value of testCertificateKey
     */
    public function setTestCertificateKey(?string $testCertificateKey): void
    {
        $this->testCertificateKey = $testCertificateKey;
    }

    /**
     * Get the value of testCertificateKey
     */
    public function getTestCertificateKey(): ?string
    {
        return $this->testCertificateKey;
    }

    /**
     * Set the value of liveChecked
     */
    public function setLiveChecked(bool $liveChecked): void
    {
        $this->liveChecked = $liveChecked;
    }

    /**
     * Get the value of liveChecked
     */
    public function getLiveChecked(): ?bool
    {
        return $this->liveChecked;
    }

    /**
     * Set the value of liveMerchantId
     */
    public function setLiveMerchantId(int $liveMerchantId): void
    {
        $this->liveMerchantId = $liveMerchantId;
    }

    /**
     * Get the value of liveMerchantId
     */
    public function getLiveMerchantId(): ?int
    {
        return $this->liveMerchantId;
    }

    /**
     * Set the value of liveCertificateKey
     */
    public function setLiveCertificateKey(string $liveCertificateKey): void
    {
        $this->liveCertificateKey = $liveCertificateKey;
    }

    /**
     * Get the value of liveCertificateKey
     */
    public function getLiveCertificateKey(): ?string
    {
        return $this->liveCertificateKey;
    }

    public function getCreated(): ?\DateTimeInterface
    {
        return $this->created;
    }

    public function setCreated(\DateTimeInterface $created): self
    {
        $this->created = $created;

        return $this;
    }

    public function getUpdated(): ?\DateTimeInterface
    {
        return $this->updated;
    }

    public function setUpdated(\DateTimeInterface $updated): self
    {
        $this->updated = $updated;

        return $this;
    }

}