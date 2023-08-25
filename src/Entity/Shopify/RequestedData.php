<?php

namespace App\Entity\Shopify;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="requested_data")
 * @ORM\HasLifecycleCallbacks
 */
class RequestedData
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
     * @ORM\Column(name="request_id", type="integer", nullable=true)
     */
    private $requestId;

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
     * @ORM\Column(name="data", type="text", length=250, nullable=false)
     * @Assert\Length(max=4096)
     */
    private $data;
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
     * Set the value of requestId
     */
    public function setRequestId(int $requestId): void
    {
        $this->id = $requestId;
    }

    /**
     * Get the value of requestId
     */
    public function getRequestId(): ?int
    {
        return $this->requestId;
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
    public function setData(string $data): void
    {
        $this->data = $data;
    }

    /**
     * Get the value of accessToken
     */
    public function getData(): ?string
    {
        return $this->data;
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
    public function getCreateDateFormat()
    {
        if(isset($this->created)){
            return $this->created->format('h:i Y-m-d');
        }
        else
            return Null;
    }
    public function getUpdatedDateFormat()
    {
        if(isset($this->updated)){
            return $this->updated->format('h:i Y-m-d');
        }
        else
            return Null;
    }
}
