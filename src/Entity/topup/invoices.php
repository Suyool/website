<?php

namespace App\Entity\topup;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="invoices")
 */
class invoices
{

    public static $statusOrder = array("COMPLETED" => "completed", "PENDING" => "pending", "CANCELED" => "canceled","HELD"=>"held",'PAID'=>"paid");
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=merchants::class, inversedBy="id")
     */
    private $merchants;

    /**
     * @ORM\Column(name="reference")
     */
    private $reference;

    /**
     * @ORM\Column(name="merchantOrderId")
     */
    private $merchantOrderId;

    /**
     * @ORM\Column(type="amount")
     */
    private $amount;

    /**
     * @ORM\Column(type="currency")
     */
    private $currency;

    /**
     * @ORM\Column(type="merchantOrderDesc")
     */
    private $merchantOrderDesc;

    /**
     * @ORM\Column(type="transId")
     */
    private $transId;

    /**
     * @ORM\Column(type="status")
     */
    private $status;

    /**
     * @ORM\Column(type="paymentMethod")
     */
    private $paymentMethod;

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

    public function getMerchantsId(): ?merchants
    {
        return $this->merchants;
    }

    public function setMerchantsId(?merchants $merchants): self
    {
        $this->merchants = $merchants;
        return $this;
    }

    public function getReference(){
        return $this->reference;
    }
    
    public function setReference($reference){
        $this->reference = $reference;
    }

    public function getMerchantOrderId(){
        return $this->merchantOrderId;
    }
    
    public function setMerchantOrderId($merchantOrderId){
        $this->merchantOrderId = $merchantOrderId;
    }

    public function getAmount(){
        return $this->amount;
    }
    
    public function setAmount($amount){
        $this->amount = $amount;
    }

    public function getCurrency(){
        return $this->currency;
    }
    
    public function setCurrency($currency){
        $this->currency = $currency;
    }

    public function getMerchantOrderDesc(){
        return $this->merchantOrderDesc;
    }
    
    public function setMerchantOrderDesc($merchantOrderDesc){
        $this->merchantOrderDesc = $merchantOrderDesc;
    }

    public function getTransId(){
        return $this->transId;
    }
    
    public function setTransId($transId){
        $this->transId = $transId;
    }

    public function getStatus(){
        return $this->status;
    }
    
    public function setStatus($status){
        $this->status = $status;
    }

    public function getPaymentMethod(){
        return $this->paymentMethod;
    }
    
    public function setPaymentMethod($paymentMethod){
        $this->paymentMethod = $paymentMethod;
    }

    public function getCreated(){
        return $this->created;
    }

}
