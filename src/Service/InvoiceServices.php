<?php

namespace App\Service;

use App\Entity\Invoices\invoices;
use App\Entity\Invoices\merchants;
use App\Entity\Invoices\test_invoices;
use Doctrine\Persistence\ManagerRegistry;

class InvoiceServices
{
    private $mr;

    public function __construct(ManagerRegistry $mr)
    {
        $this->mr=$mr->getManager('invoices');
    }

    public function PostInvoices($merchant,$merchantOrderId,$amount,$currency,$merchantOrderDesc,$transId,$paymentMethod,$ref,$callBackUrl=null)
    {
        $invoices =  new invoices();

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
    public function findExistingInvoice($merchant,$mechantOrderId) {
        $existingInvoice = $this->mr->getRepository(invoices::class)->findOneBy([
            'merchants' => $merchant,
            'merchantOrderId' => $mechantOrderId
        ]);

        return $existingInvoice;
    }
    public function findInvoiceByTranId($tranId) {
        $invoice = $this->mr->getRepository(invoices::class)->findOneBy(['transId' => $tranId]);

        return $invoice;
    }
    public function findMerchantByMerchId($merchantId) {
        $merchant = $this->mr->getRepository(merchants::class)->findOneBy(['merchantMid' => $merchantId]);

        return $merchant;
    }
    public function UpdateInvoiceDetails($tranId,$paymentMethod,$merchant,$mechantOrderId) {
        $invoice = $this->findExistingInvoice($merchant,$mechantOrderId);

        $invoice->setTransId($tranId);
        $invoice->setPaymentMethod($paymentMethod);
        $this->mr->persist($invoice);
        $this->mr->flush();
    }

    public function findInvoiceByRefNumber($refnumber) {

        $invoice = $this->mr->getRepository(invoices::class)->findOneBy(['reference' => $refnumber]);

        return $invoice;
    }

    public function updateOrderStatus($refnumber, $status) {
        $invoice = $this->findInvoiceByRefNumber($refnumber);

        $invoice->setStatus($status);
        $this->mr->persist($invoice);
        $this->mr->flush();
    }
}