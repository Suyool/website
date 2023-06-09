<?php

namespace App\Entity\Loto;

use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity
 * @ORM\Table(name="loto_numbers")
 */
class LOTO_numbers
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
    private $numbers;

    /**
     * 
     * @ORM\Column(name="price",type="integer")
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

    public function getnumbers()
    {
        return $this->numbers;
    }

    public function setnumbers($numbers)
    {
        $this->numbers=$numbers;
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
