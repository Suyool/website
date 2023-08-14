<?php

namespace App\Entity\Loto;

use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity
 * @ORM\Table(name="suyool_loto.draws")
 */
class LOTO_draw
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * 
     * @ORM\Column(type="integer")
     */
    private $drawId;

    /**
     * 
     * @ORM\Column(name="drawdate",type="datetime")
     */
    private $drawdate;

    /**
     * 
     * @ORM\Column(type="string")
     */
    private $prize;

    /**
     * 
     * @ORM\Column(type="string")
     */
    private $zeedprize;


    public function getId()
    {
        return $this->id;
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

    public function getdrawdate()
    {
        return $this->drawdate;
    }

    public function setdrawdate($drawdate)
    {
        $this->drawdate=$drawdate;
        return $this;
    }

    public function getlotoprize()
    {
        return $this->prize;
    }

    public function setlotoprize($prize)
    {
        $this->prize=$prize;
        return $this;
    }

    public function getzeedprize()
    {
        return $this->zeedprize;
    }

    public function setzeedprize($zeedprize)
    {
        $this->zeedprize=$zeedprize;
        return $this;
    }


}
