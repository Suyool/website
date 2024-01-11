<?php

namespace App\Service;

use App\Entity\Invoices\invoices;
use App\Entity\Invoices\test_invoices;
use Doctrine\Persistence\ManagerRegistry;

class InvoiceServices
{
    private $mr;

    public function __construct(ManagerRegistry $mr)
    {
        $this->mr=$mr->getManager('invoices');
    }

    public function PostInvoices($merchant,$merchantOrderId,$amount,$currency,$merchantOrderDesc,$transId,$paymentMethod,$ref,$callBackUrl=null,$simulation = null)
    {
        $invoices = ($simulation == 'true') ? new test_invoices() : new invoices();

        $invoices->setMerchantsId($merchant);
        $invoices->setMerchantOrderId($merchantOrderId);
        $invoices->setAmount($amount);
        $invoices->setCurrency($currency);
        $invoices->setMerchantOrderDesc($merchantOrderDesc);
        $invoices->setTransId($transId);
        $invoices->setStatus(invoices::$statusOrder['PENDING']);
        $invoices->setPaymentMethod($paymentMethod);
        $invoices->setReference($ref);
        $invoices->setCallBackURL($callBackUrl);

        $this->mr->persist($invoices);
        $this->mr->flush();

        return $invoices->getId();
    }
}