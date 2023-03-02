<?php

namespace App\Entity;

use App\Entity\Traits\EditorTrait;
use App\Entity\Traits\DateTrait;
use App\Entity\Traits\StatusTrait;
use Doctrine\ORM\Mapping as ORM;
use Scheb\TwoFactorBundle\Model\Email\TwoFactorInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Managers
 *
 * @ORM\Table(name="managers")
 * @ORM\Entity(repositoryClass="App\Repository\ManagersRepository")
 */
class Managers extends Entity implements UserInterface, TwoFactorInterface
{
    use StatusTrait;
    use EditorTrait;
    use DateTrait;

    public static $roles = array(1=>"ROLE_ADMIN",2=>"ROLE_EXCHANGER");
    public static $ManagerStatusArray = array(0=>"",1=> "Active",2=> "Inactive");
    public static $displayedRole = array(0=>"",1=>"Admin",2=>"Exchanger");

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    public $id;

    /**
     * @var string
     *
     * @ORM\Column(name="user", type="string", length=50, nullable=false)
     * @Assert\NotBlank
     */
    public $user;

    /**
     * @Assert\NotBlank
     * @Assert\Length(max=4096)
     */
    private $plainPassword;

    /**
     * @var string
     *
     * @ORM\Column(name="pass", type="string", length=70, nullable=false)
     */
    public $pass;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=60, nullable=false)
     */
    private $email;

    /**
     * @var integer
     *
     * @ORM\Column(name="access_level", type="integer", nullable=false)
     */
    private $accessLevel;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_visit", type="datetime", nullable=false)
     */
    private $lastVisit;

    /**
     * @var string
     *
     * @ORM\Column(name="last_ip", type="string", length=50, nullable=false)
     */
    private $lastIp;

    /**
     * @var integer
     *
     * @ORM\Column(name="auth_code", type="integer", nullable=false)
     */
    private $authCode;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=20, nullable=false)
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=50, nullable=false)
     */
    private $name;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Managers
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param string $user
     * @return Managers
     */
    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * @param mixed $plainPassword
     * @return Managers
     */
    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;
        return $this;
    }

    /**
     * @return string
     */
    public function getPass()
    {
        return $this->pass;
    }

    /**
     * @param string $pass
     * @return Managers
     */
    public function setPass($pass)
    {
        $this->pass = $pass;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return Managers
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return int
     */
    public function getAccessLevel()
    {
        return $this->accessLevel;
    }

    /**
     * @param int $accessLevel
     * @return Managers
     */
    public function setAccessLevel($accessLevel)
    {
        $this->accessLevel = $accessLevel;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getLastVisit()
    {
        return $this->lastVisit;
    }

    /**
     * @param \DateTime $lastVisit
     * @return Managers
     */
    public function setLastVisit($lastVisit)
    {
        $this->lastVisit = $lastVisit;
        return $this;
    }

    /**
     * @return string
     */
    public function getLastIp()
    {
        return $this->lastIp;
    }

    /**
     * @param string $lastIp
     * @return Managers
     */
    public function setLastIp($lastIp)
    {
        $this->lastIp = $lastIp;
        return $this;
    }

    public function isEmailAuthEnabled(): bool
    {
        return true; // This can be a persisted field to switch email code authentication on/off
    }

    public function getEmailAuthRecipient(): string
    {
        return $this->email;
    }

    public function getEmailAuthCode(): string
    {
        return $this->authCode;
    }

    public function setEmailAuthCode(string $authCode): void
    {
        $this->authCode = $authCode;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     * @return Managers
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Managers
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }




    public function __construct()
    {
        $this->isActive = true;
        // may not be needed, see section on salt below
        // $this->salt = md5(uniqid('', true));
    }


    public function getUsername()
    {
        return $this->user;
    }

    public function getSalt()
    {
        // you *may* need a real salt depending on your encoder
        // see section on salt below
        return null;
    }

    public function getPassword()
    {
        return $this->pass;
    }

    public function getRoles()
    {
        return array(self::$roles[$this->accessLevel]);
    }

    public function displayRole()
    {
        return self::$displayedRole[$this->accessLevel];
    }

    public function isAdmin(){
        return in_array('ROLE_ADMIN', $this->getRoles());
    }

    public function eraseCredentials()
    {
    }

    public function serialize() {
        return serialize($this->id);
    }

    public function unserialize($data) {
        $this->id = unserialize($data);
    }

    public function sendAuthCode($sms){
        return $sms->send($this->authCode,$this->phone) == "0";
    }

    public function displayStatus(){
        $icon = '';
        switch($this->status){
            case 1:
                $icon = 'active';
                break;
            case 2:
                $icon	= 'inactive';
                break;
        }
        return $icon;
    }
}