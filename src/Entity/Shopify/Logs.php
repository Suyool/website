<?php

namespace App\Entity\Shopify;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="logs")
 * @ORM\HasLifecycleCallbacks
 */
class Logs
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer",name="order_id")
     */
    private $orderId;

    /**
     * @ORM\Column(type="string", length=250,name="request")
     */
    private $request;

    /**
     * @ORM\Column(type="string", length=250,name="response")
     */
    private $response;

    /**
     * @ORM\Column(type="string", length=20, nullable=true, name="env")
     */
    private $env;

    /**
     * @ORM\Column(type="datetime",name="created")
     */
    private $created;

    /**
     * @ORM\PrePersist
     */
    public function updatedTimestamps()
    {
        $this->created = new \DateTime('now');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrderId(): ?int
    {
        return $this->orderId;
    }

    public function setOrderId(string $orderId): self
    {
        $this->orderId = $orderId;

        return $this;
    }

    public function getRequest(): ?string
    {
        return $this->request;
    }

    public function setRequest(string $request): self
    {
        $this->request = $request;

        return $this;
    }

    public function getResponse(): ?string
    {
        return $this->response;
    }

    public function setResponse(string $response): self
    {
        $this->response = $response;

        return $this;
    }

    public function getEnv(): ?string
    {
        return $this->env;
    }

    public function setEnv(?string $env): self
    {
        $this->env = $env;

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

    public function getCreateDateFormat()
    {
        if (isset($this->created)) {
            return $this->created->format('h:i Y-m-d');
        } else
            return Null;
    }
}
