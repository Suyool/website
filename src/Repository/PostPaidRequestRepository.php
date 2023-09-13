<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

class PostPaidRequestRepository extends EntityRepository
{
    public function insertRetrieveResults($postpaidrequest, $sets2error = null, $pin = null, $errorCode, $response, $transId = null, $currency = null, $fees = null, $fees1 = null, $amount = null, $amount1 = null, $amount2 = null, $refnum = null, $displayedfees = null, $infooriginalamount = null, $totalamount = null, $rounding = null, $additionalfees = null, $invoicenum = null, $paymentId = null)
    {
        $postpaidrequest
            ->setCodeerror($errorCode)
            ->setresponse($response)
            ->sets2error($sets2error)
            ->setPin($pin)
            ->setTransactionId($transId)
            ->setcurrency($currency)
            ->setfees($fees)
            ->setfees1($fees1)
            ->setamount($amount)
            ->setamount1($amount1)
            ->setamount2($amount2)
            ->setreferenceNumber($refnum)
            ->setdisplayedFees($displayedfees)
            ->setinformativeOriginalWSamount($infooriginalamount)
            ->settotalamount($totalamount)
            ->setrounding($rounding)
            ->setadditionalfees($additionalfees)
            ->setinvoiceNumber($invoicenum)
            ->setpaymentId($paymentId);

        return $postpaidrequest;
    }

    public function insertbill($postpaidrequest, $SuyoolUserId, $mobnumber, $token = null, $error = null)
    {
        $postpaidrequest
            ->setSuyoolUserId($SuyoolUserId)
            ->setGsmNumber($mobnumber)
            ->settoken($token)
            ->seterror($error);

        return $postpaidrequest;
    }

    public function insertBillPay($postpaid, $SuyoolUserId = null, $setGsmNumber = null, $settoken = null, $settransactionDescription = null, $settransactionReference = null, $setPin = null, $setTransactionId = null, $setcurrency = null, $setfees = null, $setfees1 = null, $setamount = null, $setamount1 = null, $setamount2 = null, $setreferenceNumber = null, $setinformativeOriginalWSamount = null, $settotalamount = null, $setrounding = null, $setadditionalfees = null, $setinvoiceNumber = null, $setpaymentId = null, $seterror = null)
    {
        $postpaid
            ->setSuyoolUserId($SuyoolUserId)
            ->setGsmNumber($setGsmNumber)
            ->settoken($settoken)
            ->settransactionDescription($settransactionDescription)
            ->settransactionReference($settransactionReference)
            ->setPin($setPin)
            ->setTransactionId($setTransactionId)
            ->setcurrency($setcurrency)
            ->setfees($setfees)
            ->setfees1($setfees1)
            ->setamount($setamount)
            ->setamount1($setamount1)
            ->setamount2($setamount2)
            ->setreferenceNumber($setreferenceNumber)
            ->setinformativeOriginalWSamount($setinformativeOriginalWSamount)
            ->settotalamount($settotalamount)
            ->setrounding($setrounding)
            ->setadditionalfees($setadditionalfees)
            ->setinvoiceNumber($setinvoiceNumber)
            ->setpaymentId($setpaymentId)
            ->seterror($seterror);

        return $postpaid;
    }
}
