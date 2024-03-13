<?php

namespace App\Entity\Notification;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\NotificationRepository")
 * @ORM\Table(name="users")
 */
class Users
{
    /**
     * @ORM\Id
     * @ORM\Column(name="suyoolUserId",type="integer",unique=true)
     */
    private $suyoolUserId;

    /**
     * @ORM\Column(name="fname",type="string")
     */
    private $fname;

    /**
     * @ORM\Column(name="lname",type="string")
     */
    private $lname;

    /**
     * @ORM\Column(name="lang",type="integer")
     */
    private $lang;

    /**
     * @ORM\Column(name="companyName",type="string")
     */
    private $companyName;

    /**
     * @ORM\Column(name="type",type="integer")
     */
    private $type;

    /**
     * @ORM\Column(name="mobileNo",type="string")
     */
    private $mobileNo;

    /**
     * @ORM\Column(name="isHavingCard")
     */
    private $isHavingCard = 0;

    public function getsuyoolUserId()
    {
        return $this->suyoolUserId;
    }

    public function setsuyoolUserId($suyoolUserId)
    {
        $this->suyoolUserId = $suyoolUserId;
        return $this;
    }

    public function getfname()
    {
        return $this->fname;
    }

    public function setfname($fname)
    {
        $this->fname = $fname;
        return $this;
    }

    public function getlname()
    {
        return $this->lname;
    }

    public function setlname($lname)
    {
        $this->lname = $lname;
        return $this;
    }

    public function getlang()
    {
        return $this->lang;
    }

    public function setlang($lang)
    {
        $this->lang = $lang;
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

    public function getCompanyName()
    {
        return $this->companyName;
    }

    public function setCompanyName($companyName)
    {
        $this->companyName = $companyName;
        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    public function getIsHavingCard()
    {
        return $this->isHavingCard;
    }

    public function setIsHavingCard($isHavingCard)
    {
        $this->isHavingCard = $isHavingCard;
        return $this;
    }
}
