<?php

namespace App\Entity\topup;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="merchants")
 */
class merchants
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="merchantMid")
     */
    private $merchantMid;

    /**
     * @ORM\Column(name="name")
     */
    private $name;

    /**
     * @ORM\Column(name="domain")
     */
    private $domain;

    /**
     * @ORM\Column(name="certificate")
     */
    private $certificate;

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

    public function getMerchantMid(){
        return $this->merchantMid;
    }

    public function setMerchantMid($merchantMid){
        $this->merchantMid = $merchantMid;
    }

    public function getName(){
        return $this->name;
    }

    public function setName($name){
        $this->name = $name;
    }

    public function getDomain(){
        return $this->domain;
    }

    public function setDomain($domain){
        $this->domain = $domain;
    }

    public function getCertificate(){
        return $this->certificate;
    }

    public function setCertificate($certificate){
        $this->certificate = $certificate;
    }

    public function getCreated(){
        return $this->created;
    }

}