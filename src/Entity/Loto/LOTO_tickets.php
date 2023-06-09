<?php

namespace App\Entity\Loto;

use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity
 * @ORM\Table(name="price_tickets")
 */
class LOTO_tickets
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * 
     * @ORM\Column(type="string")
     */
    private $loto_ticket;

    /**
     * 
     * @ORM\Column(name="create_date",type="datetime")
     */
    private $create_date;

    /**
     * 
     * @ORM\Column(type="string")
     */
    private $zeed_ticket;

    public function getId()
    {
        return $this->id;
    }

    public function getloto_ticket()
    {
        return $this->loto_ticket;
    }

    public function setloto_ticket($loto_ticket)
    {
        $this->loto_ticket=$loto_ticket;
        return $this;
    }

    public function getzeed_ticket()
    {
        return $this->zeed_ticket;
    }

    public function setzeed_ticket($zeed_ticket)
    {
        $this->zeed_ticket=$zeed_ticket;
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
