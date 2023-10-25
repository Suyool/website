<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SupportRepository")
 * @ORM\Table(name="support")
 */
class Support
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="name")
     */
    private $name;

    /**
     * @ORM\Column(name="mail")
     */
    private $mail;

    /**
     * @ORM\Column(name="phone")
     */
    private $phoneNumber;

    /**
     * @ORM\Column(name="message")
     */
    private $message;

    public function getId()
    {
        return $this->id;
    }

    public function getname()
    {
        return $this->name;
    }

    public function setname($name)
    {
        $this->name = $name;
        return $this;
    }

    public function getmail()
    {
        return $this->mail;
    }

    public function setmail($mail)
    {
        $this->mail = $mail;
        return $this;
    }

    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;
        return $this;
    }

    public function setmessage($message)
    {
        $this->message = $message;
        return $this;
    }

    public function getmessage()
    {
        return $this->message;
    }
}
