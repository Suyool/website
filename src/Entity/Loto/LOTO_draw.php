<?php

namespace App\Entity\Loto;

use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity
 * @ORM\Table(name="loto_draw")
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
    private $draw_id;

    /**
     * 
     * @ORM\Column(name="drawdate",type="datetime")
     */
    private $drawdate;

    /**
     * 
     * @ORM\Column(type="string")
     */
    private $winprizeloto;

    /**
     * 
     * @ORM\Column(type="string")
     */
    private $winprizezeed;

    /**
     * 
     * @ORM\Column(name="create_date",type="datetime")
     */
    private $create_date;

    public function getId()
    {
        return $this->id;
    }

    public function getdrawid()
    {
        return $this->draw_id;
    }

    public function setdrawid($draw_id)
    {
        $this->draw_id=$draw_id;
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
        return $this->winprizeloto;
    }

    public function setlotoprize($winprizeloto)
    {
        $this->winprizeloto=$winprizeloto;
        return $this;
    }

    public function getzeedprize()
    {
        return $this->winprizezeed;
    }

    public function setzeedprize($winprizezeed)
    {
        $this->winprizezeed=$winprizezeed;
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
