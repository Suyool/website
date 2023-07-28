<?php

namespace App\Entity\Notification;

use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity
 * @ORM\Table(name="template")
 */
class Template
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="titleEN")
     */
    private $titleEN;

    /**
     * @ORM\Column(name="titleAR")
     */
    private $titleAR;

    /**
     * 
     * @ORM\Column(name="notificationEN")
     */
    private $notificationEN;

    /**
     * 
     * @ORM\Column(name="notificationAR")
     */
    private $notificationAR;

    /**
     * @ORM\Column(name="subjectEN")
     */
    private $subjectEN;

    /**
     * @ORM\Column(name="subjectAR")
     */
    private $subjectAR;

    /**
     * 
     * @ORM\Column(name="bodyEN")
     */
    private $bodyEN;

    /**
     * 
     * @ORM\Column(name="bodyAR")
     */
    private $bodyAR;

    /**
     * 
     * @ORM\Column(name="proceedButtonEN")
     */
    private $proceedButtonEN;

    /**
     * 
     * @ORM\Column(name="proceedButtonAR")
     */
    private $proceedButtonAR;

    /**
     * 
     * @ORM\Column(name="isInbox",type="integer")
     */
    private $isInbox;

    /**
     * 
     * @ORM\Column(name="flag",type="integer")
     */
    private $flag;

    /**
     * 
     * @ORM\Column(name="identifier")
     */
    private $identifier;

    /**
     * 
     * @ORM\Column(name="notificationType",type="integer")
     */
    private $notificationType;

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

    public function gettitleEN()
    {
        return $this->titleEN;
    }

    public function settitleEN($titleEN)
    {
        $this->titleEN = $titleEN;
        return $this;
    }

    public function gettitleAR()
    {
        return $this->titleAR;
    }

    public function settitleAR($titleAR)
    {
        $this->titleAR = $titleAR;
        return $this;
    }


    public function getsubjectEN()
    {
        return $this->subjectEN;
    }

    public function setsubjectEN($subjectEN)
    {
        $this->subjectEN = $subjectEN;
        return $this;
    }

    public function getsubjectAR()
    {
        return $this->subjectAR;
    }

    public function setsubjectAR($subjectAR)
    {
        $this->subjectAR = $subjectAR;
        return $this;
    }

    public function getproceedButtonEN()
    {
        return $this->proceedButtonEN;
    }

    public function setproceedButtonEN($proceedButtonEN)
    {
        $this->proceedButtonEN = $proceedButtonEN;
        return $this;
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

    public function getnotificationType()
    {
        return $this->notificationType;
    }

    public function setnotificationType($notificationType)
    {
        $this->notificationType = $notificationType;
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

    public function getproceedButtonAR()
    {
        return $this->proceedButtonAR;
    }

    public function setproceedButtonAR($proceedButtonAR)
    {
        $this->proceedButtonAR = $proceedButtonAR;
        return $this;
    }

    public function getnotificationEN()
    {
        return $this->notificationEN;
    }

    public function setnotificationEN($notificationEN)
    {
        $this->notificationEN = $notificationEN;
        return $this;
    }

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

    public function getbodyEN()
    {
        return $this->bodyEN;
    }

    public function setbodyEN($bodyEN)
    {
        $this->bodyEN = $bodyEN;
        return $this;
    }

    public function getnotificationAR()
    {
        return $this->notificationAR;
    }

    public function setnotificationAR($notificationAR)
    {
        $this->notificationAR = $notificationAR;
        return $this;
    }

    function getbodyAR()
    {
        return $this->bodyAR;
    }

    function setbodyAR($bodyAR)
    {
        $this->bodyAR = $bodyAR;
        return $this;
    }

    function getflag()
    {
        return $this->flag;
    }

    function setflag($flag)
    {
        $this->flag = $flag;
        return $this;
    }

    function getisInbox()
    {
        return $this->isInbox;
    }

    function setisInbox($isInbox)
    {
        $this->isInbox = $isInbox;
        return $this;
    }
}
