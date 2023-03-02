<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait StatusTrait
{
    public static $statusArray = array(1=> "Active",2=> "Inactive",3=> "Deleted");

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer", nullable=false)
     */
    private $status = 1;

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param int $status
     * @return StatusTrait
     */
    public function setStatus(int $status)
    {
        $this->status = $status;
        return $this;
    }

    public function displayStatus(){
        switch($this->status){
            case 0:
                $icon	= 'unverified.gif';
                break;
            case 1:
                $icon = 'active';
                break;
            case 2:
                $icon	= 'inactive';
                break;
            case 3:
                $icon	= 'deleted';
                break;
            case 4:
                $icon	= 'canceled';
                break;
            case 5:
                $icon	= 'scheduled';
                break;
        }
        return $icon;
    }
}