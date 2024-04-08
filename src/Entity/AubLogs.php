<?php
// src/Entity/AubLogs.php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="aublogs")
 */
class AubLogs
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="identifier")
     */
    private $identifier;

    /**
     * @ORM\Column(name="request")
     */
    private $request;

    /**
     * @ORM\Column(name="response")
     */
    private $response;


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
    public function getrequest()
    {
        return $this->request;
    }

    public function setrequest($request)
    {
        $this->request = $request;
        return $this;
    }

    public function setresponse($response)
    {
        $this->response = $response;
        return $this;
    }

    public function getresponse()
    {
        return $this->response;
    }

}
