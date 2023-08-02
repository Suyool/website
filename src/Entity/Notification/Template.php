<?php

namespace App\Entity\Notification;

use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity
 * @ORM\Table(name="template")
 */
class Template
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * 
     * @ORM\Column(name="identifier")
     */
    private $identifier;

    /**
     * 
     * @ORM\Column(name="versionIndex")
     */
    private $index;

    public function getId()
    {
        return $this->id;
    }

    
    public function getidentifier()
    {
        return $this->identifier;
    }

    public function setidentifier($identifier)
    {
        $this->identifier = $identifier;
        return $this;
    }

    function getIndex()
    {
        
        return $this->index;
    }

    function setindex($index)
    {
        $this->index = $index;
        return $this;
    }
}
