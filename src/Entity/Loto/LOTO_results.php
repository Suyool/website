<?php

namespace App\Entity\Loto;

use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity
 * @ORM\Table(name="results")
 */
class LOTO_results
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * 
     * @ORM\Column(name="drawdate",type="datetime")
     */
    private $drawdate;

    /**
     * 
     * @ORM\Column(type="string")
     */
    private $winner1;

     /**
     * 
     * @ORM\Column(type="string")
     */
    private $winner2;

     /**
     * 
     * @ORM\Column(type="string")
     */
    private $winner3;

     /**
     * 
     * @ORM\Column(type="string")
     */
    private $winner4;

     /**
     * 
     * @ORM\Column(type="string")
     */
    private $winner5;

    /**
     * 
     * @ORM\Column(type="string")
     */
    private $numbers;

     /**
     * 
     * @ORM\Column(type="string")
     */
    private $zeednumber1;

     /**
     * 
     * @ORM\Column(type="string")
     */
    private $zeednumber2;

     /**
     * 
     * @ORM\Column(type="string")
     */
    private $zeednumber3;

     /**
     * 
     * @ORM\Column(type="string")
     */
    private $zeednumber4;

     /**
     * 
     * @ORM\Column(type="string")
     */
    private $winner1zeed;

     /**
     * 
     * @ORM\Column(type="string")
     */
    private $winner2zeed;

     /**
     * 
     * @ORM\Column(type="string")
     */
    private $winner3zeed;

    /**
     * 
     * @ORM\Column(type="string")
     */
    private $winner4zeed;

    /**
     * 
     * @ORM\Column(type="string")
     */
    private $drawId;


    public function getId()
    {
        return $this->id;
    }


    public function getdrawdate()
    {
        return $this->drawdate;
    }

    public function getdrawid()
    {
        return $this->drawId;
    }

    public function setdrawid($drawId)
    {
        $this->drawId=$drawId;
        return $this;
    }

    public function setdrawdate(\DateTimeInterface $drawdate)
    {
        $this->drawdate=$drawdate;
        return $this;
    }

    public function getwinner1()
    {
        return $this->winner1;
    }

    public function setwinner1($winner1)
    {
        $this->winner1=$winner1;
        return $this;
    }

    public function getwinner2()
    {
        return $this->winner2;
    }

    public function setwinner2($winner2)
    {
        $this->winner2=$winner2;
        return $this;
    }

    public function getwinner3()
    {
        return $this->winner3;
    }

    public function setwinner3($winner3)
    {
        $this->winner3=$winner3;
        return $this;
    }

    public function getwinner4()
    {
        return $this->winner4;
    }

    public function setwinner4($winner4)
    {
        $this->winner4=$winner4;
        return $this;
    }

    public function getwinner5()
    {
        return $this->winner5;
    }

    public function setwinner5($winner5)
    {
        $this->winner5=$winner5;
        return $this;
    }

    public function setnumbers($numbers)
    {
        $this->numbers=$numbers;
        return $this;
    }

    public function getnumbers()
    {
        return $this->numbers;
    }

    public function getzeednumber1()
    {
        return $this->zeednumber1;
    }

    public function setzeednumber1($zeednumber1)
    {
        $this->zeednumber1=$zeednumber1;
        return $this;
    }

    public function getzeednumber2()
    {
        return $this->zeednumber2;
    }

    public function setzeednumber2($zeednumber2)
    {
        $this->zeednumber2=$zeednumber2;
        return $this;
    }

    public function getzeednumber3()
    {
        return $this->zeednumber3;
    }

    public function setzeednumber3($zeednumber3)
    {
        $this->zeednumber3=$zeednumber3;
        return $this;
    }

    public function getzeednumber4()
    {
        return $this->zeednumber4;
    }

    public function setzeednumber4($zeednumber4)
    {
        $this->zeednumber4=$zeednumber4;
        return $this;
    }

    public function getwinner1zeed()
    {
        return $this->winner1zeed;
    }

    public function setwinner1zeed($winner1zeed)
    {
        $this->winner1zeed=$winner1zeed;
        return $this;
    }

    public function getwinner2zeed()
    {
        return $this->winner2zeed;
    }

    public function setwinner2zeed($winner2zeed)
    {
        $this->winner2zeed=$winner2zeed;
        return $this;
    }

    public function getwinner3zeed()
    {
        return $this->winner3zeed;
    }

    public function setwinner3zeed($winner3zeed)
    {
        $this->winner3zeed=$winner3zeed;
        return $this;
    }

    public function getwinner4zeed()
    {
        return $this->winner4zeed;
    }

    public function setwinner4zeed($winner4zeed)
    {
        $this->winner4zeed=$winner4zeed;
        return $this;
    }

}
