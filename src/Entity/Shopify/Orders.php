<?php

namespace App\Entity\Shopify;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="suyool_shopify.orders")
 * @ORM\HasLifecycleCallbacks
 */
class Orders
{

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, name="order_id")
     */
    private $orderId;

    /**
     * @ORM\Column(type="string", length=200, name="shop_name")
     */
    private $shopName;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=0, name="amount")
     */
    private $amount;

    /**
     * @ORM\Column(type="string", length=50, name="currency")
     */
    private $currency;

    /**
     * @ORM\Column(type="integer", nullable=true, name="status")
     */
    private $status;

    /**
     * @ORM\Column(type="string", length=250, name="callback_url")
     */
    private $callbackUrl;

    /**
     * @ORM\Column(type="string", length=250, name="error_url")
     */
    private $errorUrl;

    /**
     * @ORM\Column(type="string", length=50, name="env")
     */
    private $env;

    /**
     * @ORM\Column(type="integer", name="merchant_id")
     */
    private $merchantId;

    /**
     * @ORM\Column(type="integer", nullable=true, name="flag")
     */
    private $flag;

    /**
     * @ORM\Column(type="datetime", name="created")
     */
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
    // Getter and setter methods for each property

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

    public function getShopName(): ?string
    {
        return $this->shopName;
    }

    public function setShopName(string $shopName): self
    {
        $this->shopName = $shopName;

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

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(?int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCallbackUrl(): ?string
    {
        return $this->callbackUrl;
    }

    public function setCallbackUrl(string $callbackUrl): self
    {
        $this->callbackUrl = $callbackUrl;

        return $this;
    }

    public function getErrorUrl(): ?string
    {
        return $this->errorUrl;
    }

    public function setErrorUrl(string $errorUrl): self
    {
        $this->errorUrl = $errorUrl;

        return $this;
    }

    public function getEnv(): ?string
    {
        return $this->env;
    }

    public function setEnv(string $env): self
    {
        $this->env = $env;

        return $this;
    }

    public function getMerchantId(): ?int
    {
        return $this->merchantId;
    }

    public function setMerchantId(int $merchantId): self
    {
        $this->merchantId = $merchantId;

        return $this;
    }

    public function getFlag(): ?int
    {
        return $this->flag;
    }

    public function setFlag(?int $flag): self
    {
        $this->flag = $flag;

        return $this;
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
    public function setStatusName() {
        if(isset($this->status)){
            $status = '';
            if($this->status == 1) {
                $status = "Completed";
            }elseif($this->status == 2) {
                $status = "Rejected";
            }else
                $status = "Pending";

        return $status;
        }
        else
            return Null;
    }

    public function setFlagName() {
        if(isset($this->flag)){
            $flag = '';
            if($this->flag == 1) {
                $flag = "Deleted";
            }else
                $flag = "Available";

            return $flag;
        }
        else
            return Null;
    }
}
