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
     * @ORM\Column(name="suyoolUserId",type="string")
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
}
