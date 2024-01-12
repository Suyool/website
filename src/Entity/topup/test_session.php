<?php

namespace App\Entity\topup;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="test_bob_session")
 */
class test_session
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\topup\test_orders",  fetch="EAGER")
     * @ORM\JoinColumn(name="orders_id", referencedColumnName="id")
     */
    private $orders;

    /**
     * @ORM\Column(name="session")
     */
    private $session;

    /**
     * @ORM\Column(name="indicator")
     */
    private $indicator;

    /**
     * @ORM\Column(name="response")
     */
    private $response;

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

    public function getOrders(): ?test_orders
    {
        return $this->orders;
    }

    public function setOrders(test_orders $orders): self
    {
        $this->orders = $orders;
        return $this;
    }

    public function getSession()
    {
        return $this->session;
    }

    public function setSession($session)
    {
        $this->session = $session;
        return $this;
    }

    public function getIndicator()
    {
        return $this->indicator;
    }

    public function setIndicator($indicator)
    {
        $this->indicator = $indicator;
        return $this;
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function setResponse($response)
    {
        $this->response = $response;
        return $this;
    }

    public function getCreated()
    {
        return $this->created;
    }
}
