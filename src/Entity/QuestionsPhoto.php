<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="questions_photo")
 */
class QuestionsPhoto
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    public $image;

    /**
     * @ORM\Column(type="datetime", name="created",nullable=true)
     */
    private $created;

    public function __construct()
    {
        $this->created = new \DateTime();

    }
    public function getId(): ?int
    {
        return $this->id;
    }
    /**
     * @return string|null
     */
    public function getImage(): ?string
    {
        return $this->image;
    }

    /**
     * @param string|null $image
     * @return $this
     */
    public function setImage(?string $image):self
    {
        $this->image = $image;

        return $this;
    }

}
