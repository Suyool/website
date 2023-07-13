<?php

namespace App\Entity\Shopify;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\DateTrait;

/**
 * @ORM\Entity
 * @ORM\Table(name="shopify_installation")
 */
class ShopifyInstallation
{
    use DateTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="domain", type="string", nullable=false)
     */
    private $domain;

    /**
     * @ORM\Column(name="app_key", type="string", nullable=false)
     */
    private $appKey;

    /**
     * @ORM\Column(name="app_pass", type="string", nullable=false)
     */
    private $appPass;

    /**
     * @ORM\Column(name="app_secret", type="string", nullable=false)
     */
    private $appSecret;

    /**
     * @ORM\Column(name="shop_currency", type="string", nullable=false)
     */
    private $shopCurrency;

    /**
     * @ORM\Column(name="merchant_id", type="string", nullable=false)
     */
    private $merchantId;

    /**
     * @ORM\Column(name="certificate_key", type="string", nullable=false)
     */
    private $certificateKey;

    /**
     * @ORM\Column(name="integration_type", type="string", nullable=false)
     */
    private $integrationType;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDomain(): ?string
    {
        return $this->domain;
    }

    public function setDomain(string $domain): self
    {
        $this->domain = $domain;

        return $this;
    }

    public function getAppKey(): ?string
    {
        return $this->appKey;
    }

    public function setAppKey(string $appKey): self
    {
        $this->appKey = $appKey;

        return $this;
    }

    public function getAppPass(): ?string
    {
        return $this->appPass;
    }

    public function setAppPass(string $appPass): self
    {
        $this->appPass = $appPass;

        return $this;
    }

    public function getAppSecret(): ?string
    {
        return $this->appSecret;
    }

    public function setAppSecret(string $appSecret): self
    {
        $this->appSecret = $appSecret;

        return $this;
    }

    public function getShopCurrency(): ?string
    {
        return $this->shopCurrency;
    }

    public function setShopCurrency(string $shopCurrency): self
    {
        $this->shopCurrency = $shopCurrency;

        return $this;
    }

    public function getMerchantId(): ?string
    {
        return $this->merchantId;
    }

    public function setMerchantId(string $merchantId): self
    {
        $this->merchantId = $merchantId;

        return $this;
    }

    public function getCertificateKey(): ?string
    {
        return $this->certificateKey;
    }

    public function setCertificateKey(string $certificateKey): self
    {
        $this->certificateKey = $certificateKey;

        return $this;
    }

    public function getIntegrationType(): ?string
    {
        return $this->integrationType;
    }

    public function setIntegrationType(string $integrationType): self
    {
        $this->integrationType = $integrationType;

        return $this;
    }
}
