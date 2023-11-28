<?php

namespace App\Service;

use App\Entity\topup\invoices;
use App\Entity\topup\merchants;
use Doctrine\Persistence\ManagerRegistry;

class InvoiceServices
{
    private $mr;

    public function __construct(ManagerRegistry $mr)
    {
        $this->mr=$mr->getManager('topup');
    }

    public function PostInvoices($merchant,$merchantOrderId,$amount,$currency,$merchantOrderDesc,$transId,$paymentMethod)
    {
        $invoices=new invoices();
        $merchant=$this->mr->getRepository(merchants::class)->findOneBy(['name'=>$merchant]);
        $invoices->setMerchantsId($merchant);
        $invoices->setMerchantOrderId($merchantOrderId);
        $invoices->setAmount($amount);
        $invoices->setCurrency($currency);
        $invoices->setMerchantOrderDesc($merchantOrderDesc);
        $invoices->setTransId($transId);
        $invoices->setStatus(invoices::$statusOrder['PENDING']);
        $invoices->setPaymentMethod($paymentMethod);

        $this->mr->persist($invoices);
        $this->mr->flush();

        return $invoices->getId();
    }
}
