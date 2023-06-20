<?php
namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\DateTrait;

/**
 * @ORM\Entity
 * @ORM\Table(name="emailsubscriber")
 */
class emailsubscriber
{
    use DateTrait;

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
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime", nullable=false)
     */
    private $created;
    public function getId(){
        return $this->id;
    }
    public function setId($id){
        $this->id=$id;
        return $this;
    }
    public function getEmail(){
        return $this->id;
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