<?php

namespace Simly\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="visitors")
 */

class Visitors
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
    private $suyoolUserId;

    /**
     * @ORM\Column(type="string")
     */
    private $webkey;

      /**
     * @ORM\Column()
     */
    private $isPopup = 0;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSuyoolUserId(): ?int
    {
        return $this->suyoolUserId;
    }

    public function setSuyoolUserId(int $suyoolUserId): self
    {
        $this->suyoolUserId = $suyoolUserId;
        return $this;
    }

    
    public function getWebKey()
    {
        return $this->webkey;
    }

    public function setWebKey($webkey): self
    {
        $this->webkey = $webkey;
        return $this;
    }

    
    public function getPopup()
    {
        return $this->isPopup;
    }

    public function setPopup($isPopup): self
    {
        $this->isPopup = $isPopup;
        return $this;
    }

}