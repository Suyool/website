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
    private $drawId;

    /**
     * 
     * @ORM\Column(name="created",type="datetime")
     */
    private $created;

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

    public function setdrawdate(string $drawdate)
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


    public function getcreatedate()
    {
        return $this->created;
    }

    public function setcreatedate($created)
    {
        $this->created=$created;
        return $this;
    }


}
