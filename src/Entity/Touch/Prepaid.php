<?php

namespace App\Entity\Touch;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="prepaid")
 */
class Prepaid
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="suyoolUserId",type="integer")
     */
    private $suyoolUserId;

    /**
     * @ORM\Column(name="voucherSerial")
     */
    private $voucherSerial;

    /**
     * @ORM\Column(name="voucherCode")
     */
    private $voucherCode;

    /**
     * @ORM\Column(name="voucherExpiry")
     */
    private $voucherExpiry;

    /**
     * @ORM\Column(name="description")
     */
    private $description;

    /**
     * @ORM\Column(name="displayMessage")
     */
    private $displayMessage;

    /**
     * @ORM\Column(name="token")
     */
    private $token;

    /**
     * @ORM\Column(name="balance")
     */
    private $balance;

    /**
     * @ORM\Column(name="errorMsg")
     */
    private $errorMsg;

    /**
     * @ORM\Column(name="insertId")
     */
    private $insertId;

    public function getId()
    {
        return $this->id;
    }

    public function getdisplayMessage()
    {
        return $this->displayMessage;
    }

    public function setdisplayMessage($displayMessage)
    {
        $this->displayMessage = $displayMessage;
        return $this;
    }

    public function gettoken()
    {
        return $this->token;
    }

    public function settoken($token)
    {
        $this->token = $token;
        return $this;
    }

    public function getbalance()
    {
        return $this->balance;
    }

    public function setbalance($balance)
    {
        $this->balance = $balance;
        return $this;
    }

    public function geterrorMsg()
    {
        return $this->errorMsg;
    }

    public function seterrorMsg($errorMsg)
    {
        $this->errorMsg = $errorMsg;
        return $this;
    }

    public function getinsertId()
    {
        return $this->insertId;
    }

    public function setinsertId($insertId)
    {
        $this->insertId = $insertId;
        return $this;
    }

    public function getvoucherCode()
    {
        return $this->voucherCode;
    }

    public function setvoucherCode($voucherCode)
    {
        $this->voucherCode = $voucherCode;
        return $this;
    }

    public function getSuyoolUserId()
    {
        return $this->suyoolUserId;
    }

    public function setSuyoolUserId($suyoolUserId)
    {
        $this->suyoolUserId = $suyoolUserId;
        return $this;
    }

    public function getvoucherExpiry()
    {
        return $this->voucherExpiry;
    }

    public function setvoucherExpiry($voucherExpiry)
    {
        $this->voucherExpiry = $voucherExpiry;
        return $this;
    }

    function getvoucherSerial()
    {
        return $this->voucherSerial;
    }

    function setvoucherSerial($voucherSerial)
    {
        $this->voucherSerial = $voucherSerial;
        return $this;
    }

    function getdescription()
    {
        return $this->description;
    }

    function setdescription($description)
    {
        $this->description = $description;
        return $this;
    }
}
