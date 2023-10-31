<?php
// src/Entity/TerranetTransaction.php

namespace App\Entity\TerraNet;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="transactions")
 */
class Transaction
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $TransactionID;

    /**
     * @ORM\Column(type="integer")
     */
    private $ProductId;

    /**
     * @ORM\Column(type="string")
     */
    private $Username;

    /**
     * @ORM\Column(type="float")
     */
    private $PaidAmount;

    /**
     * @ORM\Column(type="boolean")
     */
    private $Cancelled;

    /**
     * @ORM\Column(type="datetime")
     */
    private $Date;

    public function getId()
    {
        return $this->id;
    }

    public function getTransactionID()
    {
        return $this->TransactionID;
    }

    public function setTransactionID($TransactionID)
    {
        $this->TransactionID = $TransactionID;
    }

    public function getProductId()
    {
        return $this->ProductId;
    }

    public function setProductId($ProductId)
    {
        $this->ProductId = $ProductId;
    }

    public function getUsername()
    {
        return $this->Username;
    }

    public function setUsername($Username)
    {
        $this->Username = $Username;
    }

    public function getPaidAmount()
    {
        return $this->PaidAmount;
    }

    public function setPaidAmount($PaidAmount)
    {
        $this->PaidAmount = $PaidAmount;
    }

    public function getCancelled()
    {
        return $this->Cancelled;
    }

    public function setCancelled($Cancelled)
    {
        $this->Cancelled = $Cancelled;
    }

    public function getDate()
    {
        return $this->Date;
    }

    public function setDate($Date)
    {
        $this->Date = $Date;
    }
}
