<?php

namespace App\Entity\Invoices;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="test_invoices")
 */
class test_invoices
{

    public static $statusOrder = array("COMPLETED" => "completed", "PENDING" => "pending", "CANCELED" => "canceled","HELD"=>"held");
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=merchants::class)
     * @ORM\JoinColumn(name="merchants_id", referencedColumnName="id")
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
     * @ORM\Column(name="amount")
     */
    private $amount;

    /**
     * @ORM\Column(name="currency")
     */
    private $currency;

    /**
     * @ORM\Column(name="merchantOrderDesc")
     */
    private $merchantOrderDesc;

    /**
     * @ORM\Column(name="transId")
     */
    private $transId;

    /**
     * @ORM\Column(name="callBackURL")
     */
    private $callBackURL;

    /**
     * @ORM\Column(name="status")
     */
    private $status;

    /**
     * @ORM\Column(name="paymentMethod")
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

    public function getCallBackURL(){
        return $this->callBackURL;
    }

    public function setCallBackURL($callBackURL){
        $this->callBackURL = $callBackURL;
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