<?php

namespace App\Entity\Loto;

use App\Entity\Traits\DateTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\notificationRepository")
 * @ORM\Table(name="notification")
 */
class notification
{
    use DateTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * 
     * @ORM\Column(name="identifier")
     */
    private $identifier;

    /**
     * @ORM\ManyToOne(targetEntity=order::class, inversedBy="id")
     */
    private $order;

    /**
     * 
     * @ORM\Column(name="transId")
     */
    private $transId;

    /**
     * @ORM\ManyToOne(targetEntity=LOTO_draw::class, inversedBy="id")
     */
    private $draw;

    /**
     * 
     * @ORM\Column(name="ticketId")
     */
    private $ticketId;

    /**
     * 
     * @ORM\Column(name="withZeed")
     */
    private $withZeed;

    /**
     * 
     * @ORM\Column(name="isbouquet")
     */
    private $isbouquet;

    /**
     * 
     * @ORM\Column(name="text")
     */
    private $text;
    
    /**
     * 
     * @ORM\Column(name="title")
     */
    private $title;

    /**
     * 
     * @ORM\Column(name="notify")
     */
    private $notify;

    /**
     * 
     * @ORM\Column(name="subject")
     */
    private $subject;

    /**
     * 
     * @ORM\Column(name="grids")
     */
    private $grids;

    /**
     * 
     * @ORM\Column(name="amount")
     */
    private $amount;

    /**
     * 
     * @ORM\Column(name="currency")
     */
    private $currency;

    /**
     * 
     * @ORM\Column(name="resultDate")
     */
    private $resultDate;

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

    public function getIdentifier()
    {
        return $this->identifier;
    }

    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
        return $this;
    }

    public function gettransId()
    {
        return $this->ticketId;
    }

    public function settransId($transId)
    {
        $this->transId = $transId;
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

    public function getzeed()
    {
        return $this->withZeed;
    }

    public function setzeed($withZeed)
    {
        $this->withZeed = $withZeed;

        return $this;
    }

    public function getDrawId(): ?LOTO_draw
    {
        return $this->order;
    }

    public function setDrawId(?LOTO_draw $draw_id): self
    {
        $this->draw = $draw_id;

        return $this;
    }

    public function getbouquet()
    {
        return $this->isbouquet;
    }

    public function setbouquet($isbouquet)
    {
        $this->isbouquet = $isbouquet;
        return $this;
    }

    public function getText()
    {
        return $this->text;
    }

    public function setText($text)
    {
        $this->text = $text;
        return $this;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    public function getamount()
    {
        return $this->amount;
    }

    public function setamount($amount)
    {
        $this->amount = $amount;
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

    public function getGrids()
    {
        return $this->grids;
    }

    public function setGrids($grids)
    {
        $this->grids = $grids;
        return $this;
    }

    /**
     * Get the value of notify
     */ 
    public function getNotify()
    {
        return $this->notify;
    }

    /**
     * Set the value of notify
     *
     * @return  self
     */ 
    public function setNotify($notify)
    {
        $this->notify = $notify;

        return $this;
    }

    /**
     * Get the value of subject
     */ 
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set the value of subject
     *
     * @return  self
     */ 
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Get the value of resultDate
     */ 
    public function getResultDate()
    {
        return $this->resultDate;
    }

    /**
     * Set the value of resultDate
     *
     * @return  self
     */ 
    public function setResultDate($resultDate)
    {
        $this->resultDate = $resultDate;

        return $this;
    }
}
