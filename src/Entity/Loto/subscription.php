<?php

namespace App\Entity\Loto;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SubscriptionRepository")
 * @ORM\Table(name="subscription")
 */
class loto
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=order::class, inversedBy="id")
     */
    private $order;

    /**
     * @ORM\Column(name="ticketId")
     */
    private $ticketId = 0;

    /**
     * @ORM\Column(name="drawNumber",type="string")
     */
    private $drawNumber;

    /**
     * @ORM\Column(name="numdraws",type="string")
     */
    private $numdraws;

    /**
     * @ORM\Column(name="withZeed",type="integer")
     */
    private $withZeed;

    /**
     * @ORM\Column(name="gridSelected")
     */
    private $gridSelected;

    /**
     * @ORM\Column(name="zeednumbers")
     */
    private $zeednumbers;

    /**
     * @ORM\Column(name="price")
     */
    private $price;

    /**
     * @ORM\Column(name="currency")
     */
    private $currency;

    /**
     * @ORM\Column(name="bouquet")
     */
    private $bouquet;

    /**
     * @ORM\Column(name="errorInfo")
     */
    private $errorInfo;

    /**
     * @ORM\Column(name="winLoto")
     */
    private $winloto;

    /**
     * @ORM\Column(name="winZeed")
     */
    private $winzeed;

     /**
     * @ORM\Column(name="isWon")
     */
    private $isWon;

    /**
     * @ORM\Column(type="string")
     */
    private $winningStatus;

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

    public function getOrderId(): ?order
    {
        return $this->order;
    }

    public function setOrderId(?order $order_id): self
    {
        $this->order = $order_id;
        return $this;
    }

    public function getticketId()
    {
        return $this->ticketId;
    }

    public function setticketId($ticketId)
    {
        $this->ticketId = $ticketId;
        return $this;
    }

    public function getdrawnumber()
    {
        return $this->drawNumber;
    }

    public function setdrawnumber($drawNumber)
    {
        $this->drawNumber = $drawNumber;
        return $this;
    }

    public function getnumdraws()
    {
        return $this->numdraws;
    }

    public function setnumdraws($numdraws)
    {
        $this->numdraws = $numdraws;
        return $this;
    }

    public function getwithZeed()
    {
        if ($this->withZeed == true) {
            return 1;
        } else {
            return 0;
        }
    }

    public function setWithZeed($withZeed)
    {
        $this->withZeed = $withZeed;
        return $this;
    }

    public function getgridSelected()
    {
        return $this->gridSelected;
    }

    public function setgridSelected($gridSelected)
    {
        $this->gridSelected = $gridSelected;
        return $this;
    }

    public function getzeednumber()
    {
        return $this->zeednumbers;
    }

    public function setzeednumber($zeednumbers)
    {
        $this->zeednumbers = $zeednumbers;
        return $this;
    }


    public function getprice()
    {
        return $this->price;
    }

    public function setprice($price)
    {
        $this->price = $price;
        return $this;
    }

    public function getcurrency()
    {
        return $this->currency;
    }

    public function setcurrency($currency)
    {
        $this->currency = $currency;
        return $this;
    }

    public function getbouquet()
    {
        if ($this->bouquet) {
            return true;
        } else {
            return false;
        }
    }

    public function setbouquet($bouquet)
    {
        $this->bouquet = $bouquet;
        return $this;
    }

    public function geterror()
    {
        return $this->errorInfo;
    }

    public function seterror($errorInfo)
    {
        $this->errorInfo = $errorInfo;
        return $this;
    }

    public function getwinloto()
    {
        return $this->winloto;
    }

    public function setwinloto($winloto)
    {
        $this->winloto = $winloto;
        return $this;
    }

    public function getwinzeed()
    {
        return $this->winzeed;
    }

    public function setwinzeed($winzeed)
    {
        $this->winzeed = $winzeed;
        return $this;
    }

    public function setwinningStatus($winningStatus)
    {
        $this->winningStatus = $winningStatus;
        return $this;
    }

    public function getwinningStatus()
    {
        return $this->winningStatus;
    }

    public function setisWon($isWon)
    {
        $this->isWon = $isWon;
        return $this;
    }

    public function getisWon()
    {
        return $this->isWon;
    }

    public function getCreated()
    {
        return $this->created;
    }
}
