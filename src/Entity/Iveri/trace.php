<?php

namespace App\Entity\Iveri;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="trace")
 */
class trace
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Iveri\orders",  fetch="EAGER")
     * @ORM\JoinColumn(name="orders_id", referencedColumnName="id")
     */
    private $orders;

    /**
     * @ORM\Column(name="merchantTrace")
     */
    private $merchantTrace;

    /**
     * @ORM\Column(name="created",type="datetime",nullable=true)
     */
    private $created;

    public function __construct()
    {
        $this->created = new \DateTime();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getOrders(): ?orders
    {
        return $this->orders;
    }

    public function setOrders(orders $orders): self
    {
        $this->orders = $orders;
        return $this;
    }

    public function getTrace()
    {
        return $this->merchantTrace;
    }

    public function setTrace($merchantTrace)
    {
        $this->merchantTrace = $merchantTrace;
        return $this;
    }

    public function getCreated()
    {
        return $this->created;
    }
}
