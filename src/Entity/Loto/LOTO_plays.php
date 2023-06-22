<?php

namespace App\Entity\Loto;

use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity
 * @ORM\Table(name="plays")
 */
class LOTO_plays
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * 
     * @ORM\Column(name="suyoolUserId")
     */
    private $suyoolUserId;

    /**
     * 
     * @ORM\Column(name="drawNumber",type="string")
     */
    private $drawNumber;

     /**
     * 
     * @ORM\Column(name="numdraws",type="string")
     */
    private $numdraws;

     /**
     * 
     * @ORM\Column(name="withZeed")
     */
    private $withZeed;

     /**
     * 
     * @ORM\Column(name="gridSelected")
     */
    private $gridSelected;

     /**
     * 
     * @ORM\Column(name="price")
     */
    private $price;

    /**
     * 
     * @ORM\Column(name="create_date",type="datetime")
     */
    private $create_date;

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
        $this->suyoolUserId=$suyoolUserId;
        return $this;
    }

    public function getdrawnumber()
    {
        return $this->drawNumber;
    }

    public function setdrawnumber($drawNumber)
    {
        $this->drawNumber=$drawNumber;
        return $this;
    }

    public function getnumdraws()
    {
        return $this->numdraws;
    }

    public function setnumdraws($numdraws)
    {
        $this->numdraws=$numdraws;
        return $this;
    }

    public function getwithZeed()
    {
        return $this->withZeed;
    }

    public function setWithZeed($withZeed)
    {
        $this->withZeed=$withZeed;
        return $this;
    }

    public function getgridSelected()
    {
        return $this->gridSelected;
    }

    public function setgridSelected($gridSelected)
    {
        $this->gridSelected=$gridSelected;
        return $this;
    }


    public function getprice()
    {
        return $this->price;
    }

    public function setprice($price)
    {
        $this->price=$price;
        return $this;
    }


    public function getcreatedate()
    {
        return $this->create_date;
    }

    public function setcreatedate($create_date)
    {
        $this->create_date=$create_date;
        return $this;
    }


}
