<?php

namespace App\Entity\Windsl;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 */
class Users
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="username")
     */
    private $username;

    /**
     * @ORM\Column(name="password")
     */
    private $password;

     /**
     * @ORM\Column(name="winDslUserId")
     */
    private $winDslUserId;

     /**
     * @ORM\Column(name="lastLogin")
     */
    private DateTime $lastLogin;

    public function getId()
    {
        return $this->id;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    public function getWinDslUserId()
    {
        return $this->winDslUserId;
    }

    public function setWinDslUserId($winDslUserId)
    {
        $this->winDslUserId = $winDslUserId;
        return $this;
    }

    public function setLastLogin(){
        $this->lastLogin = new DateTime('Asia/Beirut');
        return $this;
    }
}
