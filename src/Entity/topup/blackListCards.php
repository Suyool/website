<?php

namespace App\Entity\topup;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AttemptsRepository")
 * @ORM\Table(name="blacklistcards")
 */
class blackListCards
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

     /**
     * @ORM\Column(name="card")
     */
    private $card;

    /**
     * @ORM\Column(name="created",type="datetime",nullable=true)
     */
    private $created;

    public function __construct()
    {
        $this->created = new \DateTime();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getCard()
    {
        return $this->card;
    }

    public function setCard($card)
    {
        $this->card = $card;
        return $this;
    }

    public function getCreated()
    {
        return $this->created;
    }
}
