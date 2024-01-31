<?php

namespace App\Entity\Loto;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SubscriptionRepository")
 * @ORM\Table(name="subscription")
 */
class subscription
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="suyoolUserId")
     */
    private $suyoolUserId ;

    /**
     * @ORM\Column(name="mobileNo")
     */
    private $mobileNo ;

    /**
     * @ORM\Column(name="numdraws")
     */
    private $numdraws;

    /**
     * @ORM\Column(name="isZeed",type="integer")
     */
    private $isZeed;

    /**
     * @ORM\Column(name="gridSelected")
     */
    private $gridSelected;

    /**
     * @ORM\Column(name="isBouquet")
     */
    private $isBouquet;

    /**
     * @ORM\Column(name="canceledDate",type="datetime",nullable=true)
     */
    private $canceledDate;

    /**
     * @ORM\Column(name="canceled")
     */
    private $canceled = 0;

    /**
     * @ORM\Column(name="autoPlay")
     */
    private $autoPlay = 0;

    /**
     * @ORM\Column(name="remaining")
     */
    private $remaining;

     /**
     * @ORM\Column(name="numGrids")
     */
    private $numGrids;

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

    public function getsuyoolUserId()
    {
        return $this->suyoolUserId;
    }

    public function setsuyoolUserId($suyoolUserId)
    {
        $this->suyoolUserId = $suyoolUserId;
        return $this;
    }

    public function getMobileNo()
    {
        return $this->mobileNo;
    }

    public function setMobileNo($mobileNo)
    {
        $this->mobileNo = $mobileNo;
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

    public function getRemaining()
    {
        return $this->remaining;
    }

    public function setRemaining($remaining)
    {
        $this->remaining = $remaining;
        return $this;
    }

    public function getNumGrids()
    {
        return $this->numGrids;
    }

    public function setNumGrids($numGrids)
    {
        $this->numGrids = $numGrids;
        return $this;
    }

    public function getIsZeed()
    {
        if ($this->isZeed == true) {
            return 1;
        } else {
            return 0;
        }
    }

    public function setIsZeed($isZeed)
    {
        $this->isZeed = $isZeed;
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

    public function getCanceled()
    {
        return $this->canceled;
    }

    public function setCanceled($canceled)
    {
        $this->canceled = $canceled;
        return $this;
    }

    public function getAutoPlay()
    {
        return $this->autoPlay;
    }

    public function setAutoPlay($autoPlay)
    {
        $this->autoPlay = $autoPlay;
        return $this;
    }

    public function getCanceledDate()
    {
        return $this->canceledDate;
    }

    public function setCanceledDate(DateTime $canceledDate)
    {
        $this->canceledDate = $canceledDate;
        return $this;
    }

    public function getIsbouquet()
    {
        if ($this->isBouquet) {
            return true;
        } else {
            return false;
        }
    }

    public function setIsbouquet($isBouquet)
    {
        $this->isBouquet = $isBouquet;
        return $this;
    }

    public function getCreated()
    {
        return $this->created;
    }
}
