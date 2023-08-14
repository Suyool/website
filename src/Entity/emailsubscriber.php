<?php
namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="emailsubscriber")
 */
class emailsubscriber
{
    //use DateTrait;

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
     * @ORM\Column(name="email", type="string", length=60, nullable=false)
     */
    private $email;

    public function getId(){
        return $this->id;
    }
    public function setId($id){
        $this->id=$id;
        return $this;
    }
    public function getEmail(){
        return $this->email;
    }
    public function setEmail($email){
        $this->email=$email;
        return $this;
    }
//    public function getCreated(){
//        return $this->created;
//    }
//    public function setCreated($created){
//        $this->created=$created;
//        return $this;
//    }
}