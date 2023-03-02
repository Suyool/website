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
 * @ORM\Table(name="thresholds")
 * @ORM\Entity(repositoryClass="App\Repository\ThresholdsRepository")
 */
class Thresholds extends Entity
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
     * @ORM\Column(name="threshold", type="integer", nullable=false)
     */
    private $threshold;

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
     * @return Thresholds
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getThreshold()
    {
        return $this->threshold;
    }

    /**
     * @param int $threshold
     * @return Thresholds
     */
    public function setThreshold($threshold)
    {
        $this->threshold = $threshold;
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
     * @return Thresholds
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