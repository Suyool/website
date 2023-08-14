<?php

namespace App\Entity\Notification;

use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity
 * @ORM\Table(name="suyool_notification.notification")
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
     * @ORM\Column(name="bulk")
     */
    private $bulk;

    /**
     * @ORM\Column(name="userId")
     */
    private $userId;

     /**
     * @ORM\ManyToOne(targetEntity=content::class, inversedBy="id")
     */
    private $content;

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
     * @ORM\Column(name="params")
     */
    private $params;

    /**
     * @ORM\Column(name="additionalData")
     */
    private $additionalData;


    /**
     * @ORM\Column(name="titleOut")
     */
    private $titleOut;

    /**
     * @ORM\Column(name="bodyOut")
     */
    private $bodyOut;

    /**
     * @ORM\Column(name="titleIn")
     */
    private $titleIn;

    /**
     * @ORM\Column(name="bodyIn")
     */
    private $bodyIn;

    /**
     * @ORM\Column(name="proceedButton")
     */
    private $proceedButton;

    /**
     * @ORM\Column(name="send_Date")
     */
    private $sendDate;



    public function getId()
    {
        return $this->id;
    }

    public function getbulk()
    {
        return $this->bulk;
    }

    public function setbulk($bulk)
    {
        $this->bulk = $bulk;
        return $this;
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

    public function getadditionalData()
    {
        return $this->additionalData;
    }

    public function setadditionalData($additionalData)
    {
        $this->additionalData = $additionalData;
        return $this;
    }

    public function getcontentId(): ?content
    {
        return $this->content;
    }

    public function setcontentId(?content $content_id): self
    {
        $this->content = $content_id;

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

    public function getparams()
    {
        return $this->params;
    }

    public function setparams($params)
    {
        $this->params = $params;
        return $this;
    }

    public function gettitleOut()
    {
        return $this->titleOut;
    }

    public function settitleOut($titleOut)
    {
        $this->titleOut = $titleOut;
        return $this;
    }

    public function getbodyOut()
    {
        return $this->bodyOut;
    }

    public function setbodyOut($bodyOut)
    {
        $this->bodyOut = $bodyOut;
        return $this;
    }

    public function gettitleIn()
    {
        return $this->titleIn;
    }

    public function settitleIn($titleIn)
    {
        $this->titleIn = $titleIn;
        return $this;
    }

    public function getbodyIn()
    {
        return $this->bodyIn;
    }

    public function setbodyIn($bodyIn)
    {
        $this->bodyIn = $bodyIn;
        return $this;
    }

    public function getproceedButton()
    {
        return $this->proceedButton;
    }

    public function setproceedButton($proceedButton)
    {
        $this->proceedButton = $proceedButton;
        return $this;
    }

    public function getsendDate()
    {
        return $this->sendDate;
    }

    public function setsendDate($sendDate)
    {
        $this->sendDate = $sendDate;
        return $this;
    }
}
