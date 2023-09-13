<?php

namespace App\Entity\Loto;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\lotonumbersRepository")
 * @ORM\Table(name="prices")
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
     * @ORM\Column(type="integer")
     */
    private $numbers;

    /**
     * @ORM\Column(name="price",type="integer")
     */
    private $price;

    /**
     * @ORM\Column(name="zeed",type="integer")
     */
    private $zeed;

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
        $this->numbers = $numbers;
        return $this;
    }

    public function getprice()
    {
        return $this->price;
    }

    public function setzeed($zeed)
    {
        $this->zeed = $zeed;
        return $this;
    }

    public function getzeed()
    {
        return $this->zeed;
    }

    public function setprice($price)
    {
        $this->price = $price;
        return $this;
    }
}
