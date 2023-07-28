<?php

namespace App\Entity\Notification;

use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity
 * @ORM\Table(name="notification")
 */
class Notification
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="userId")
     */
    private $userId;

    /**
     * @ORM\Column(name="templateId")
     */
    private $templateId;

    /**
     * @ORM\Column(name="status")
     */
    private $status;

    /**
     * @ORM\Column(name="errorMsg")
     */
    private $errorMsg;

    /**
     * 
     * @ORM\Column(name="paramsText")
     */
    private $paramsText;

    // /**
    //  * 
    //  * @ORM\Column(name="informativeOriginalWSamount")
    //  */
    // private $informativeOriginalWSamount;

    // /**
    //  * 
    //  * @ORM\Column(name="totalamount")
    //  */
    // private $totalamount;

    // /**
    //  * 
    //  * @ORM\Column(name="rounding")
    //  */
    // private $rounding;

    // /**
    //  * 
    //  * @ORM\Column(name="additionalfees")
    //  */
    // private $additionalfees;



    public function getId()
    {
        return $this->id;
    }

    public function getuserId()
    {
        return $this->userId;
    }

    public function setuserId($userId)
    {
        $this->userId = $userId;
        return $this;
    }

    public function gettemplateId()
    {
        return $this->templateId;
    }

    public function settemplateId($templateId)
    {
        $this->templateId = $templateId;
        return $this;
    }


    public function getstatus()
    {
        return $this->status;
    }

    public function setstatus($status)
    {
        $this->status = $status;
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

    public function getparamsText()
    {
        return $this->paramsText;
    }

    public function setparamsText($paramsText)
    {
        $this->paramsText = $paramsText;
        return $this;
    }

    // public function getinformativeOriginalWSamount()
    // {
    //     return $this->informativeOriginalWSamount;
    // }

    // public function setinformativeOriginalWSamount($informativeOriginalWSamount)
    // {
    //     $this->informativeOriginalWSamount = $informativeOriginalWSamount;
    //     return $this;
    // }

    // public function gettotalamount()
    // {
    //     return $this->totalamount;
    // }

    // public function settotalamount($totalamount)
    // {
    //     $this->totalamount = $totalamount;
    //     return $this;
    // }

    // public function getrounding()
    // {
    //     return $this->rounding;
    // }

    // public function setrounding($rounding)
    // {
    //     $this->rounding = $rounding;
    //     return $this;
    // }

    // public function getadditionalfees()
    // {
    //     return $this->additionalfees;
    // }

    // public function setadditionalfees($additionalfees)
    // {
    //     $this->additionalfees = $additionalfees;
    //     return $this;
    // }
}
