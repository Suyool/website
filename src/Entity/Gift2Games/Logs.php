<?php

namespace App\Entity\Gift2Games;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="logs")
 */
class Logs
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
     * @ORM\Column(name="url")
     */
    private $url;

    /**
     * @ORM\Column(name="request")
     */
    private $request;

    /**
     * @ORM\Column(name="response")
     */
    private $response;

    /**
     * @ORM\Column(name="error")
     */
    private $error;

    /**
     * @ORM\Column(name="responseStatusCode")
     */
    private $responseStatusCode;

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

    public function geturl()
    {
        return $this->url;
    }

    public function seturl($url)
    {
        $this->url = $url;
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

    public function geterror()
    {
        return $this->error;
    }

    public function seterror($error)
    {
        $this->error = $error;
        return $this;
    }

    public function setresponseStatusCode($responseStatusCode)
    {
        $this->responseStatusCode = $responseStatusCode;
        return $this;
    }

    public function getresponseStatusCode()
    {
        return $this->responseStatusCode;
    }
}
