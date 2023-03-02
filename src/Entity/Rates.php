<?php

namespace App\Entity;

use App\Entity\Traits\EditorTrait;
use App\Entity\Traits\DateTrait;
use App\Entity\Traits\StatusTrait;
use App\Utils\Helper;
use Doctrine\ORM\Mapping as ORM;

/**
 * Categories
 *
 * @ORM\Table(name="rates")
 * @ORM\Entity(repositoryClass="App\Repository\RatesRepository")
 */
class Rates extends Entity
{
    use EditorTrait;
    use DateTrait;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="sell_rate", type="integer", nullable=false)
     */
    private $sellRate;

    /**
     * @var string
     *
     * @ORM\Column(name="buy_rate", type="integer", nullable=false)
     */
    private $buyRate;

    /**
     * @var integer
     *
     * @ORM\Column(name="direction", type="integer", nullable=false)
     */
    private $direction = 1;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Rates
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getSellRate()
    {
        return $this->sellRate;
    }

    /**
     * @param int $sellRate
     * @return Rates
     */
    public function setSellRate($sellRate)
    {
        $this->sellRate = $sellRate;
        return $this;
    }

    /**
     * @return string
     */
    public function getBuyRate()
    {
        return $this->buyRate;
    }

    /**
     * @param string $buyRate
     * @return Rates
     */
    public function setBuyRate($buyRate)
    {
        $this->buyRate = $buyRate;
        return $this;
    }

    /**
     * @return int
     */
    public function getDirection()
    {
        return $this->direction;
    }

    /**
     * @param int $direction
     * @return Rates
     */
    public function setDirection($direction)
    {
        $this->direction = $direction;
        return $this;
    }

    public function displayTime(){
        return $this->createDate->format("h:i");
    }

    public function displayDate(){
        return $this->createDate->format("Y-m-d");
    }

    public function displayDirection(){
        switch ($this->direction){
            case 1:
                return "glyphicon-triangle-top";
                break;
            case 2:
                return "glyphicon-triangle-bottom";
                break;
        }
    }
}