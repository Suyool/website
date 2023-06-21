<?php

namespace App\Entity\Shopify;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\DateTrait;

/**
 * @ORM\Table(name="shopify_orders")
 * @ORM\Entity(repositoryClass="App\Repository\OrdersRepository")
 */
class ShopifyOrders
{
    use DateTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="order_id", type="string", nullable=false)
     */
    private $orderId;

    /**
     * @var string
     *
     * @ORM\Column(name="metainfo", type="string", nullable=false)
     */
    private $metaInfo;

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="integer", nullable=false)
     */
    private $status;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getOrderId(): ?string
    {
        return $this->orderId;
    }

    public function setOrderId(string $orderId): void
    {
        $this->orderId = $orderId;
    }

    public function getMetaInfo(): ?string
    {
        return $this->metaInfo;
    }

    public function setMetaInfo(string $metaInfo): void
    {
        $this->metaInfo = $metaInfo;
    }
    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): void
    {
        $this->status = $status;
    }
}
