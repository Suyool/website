<?php

namespace App\Controller;

use App\Entity\Alfa\Logs;
use App\Entity\Alfa\Order;
use App\Entity\Alfa\Postpaid;
use App\Entity\Alfa\Prepaid;
use App\Entity\Alfa\PostpaidRequest;
use App\Service\LotoServices;
use App\Service\BobServices;
use App\Service\Memcached;
use App\Service\NotificationServices;
use App\Service\SuyoolServices;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class AlfaController extends AbstractController
{
    private $mr;
    private $hash_algo;
    private $certificate;

    public function __construct(ManagerRegistry $mr, $certificate, $hash_algo)
    {
        $this->mr = $mr->getManager('alfa');
        $this->hash_algo = $hash_algo;
        $this->certificate = $certificate;
    }

    /**
     * @Route("/alfa", name="app_alfa")
     */
    public function index()
    {
        // phpinfo();
        $postpaid = $this->mr->getRepository(Postpaid::class)->findAll();
        $orders = $this->mr->getRepository(Order::class)->findAll();
        // dd($orders);
        $parameters['Test'] = "tst";

        return $this->render('alfa/index.html.twig', [
            'parameters' => $parameters
        ]);
    }


    /**
     * PostPaid
     * Provider : BOB
     * Desc: Send Pin to user based on phoneNumber
     * @Route("/alfa/bill", name="app_alfa_bill",methods="POST")
     */
    public function bill(Request $request, BobServices $bobServices)
    {
        $data = json_decode($request->getContent(), true);
        if ($data != null) {
            $sendBill = $bobServices->Bill($data["mobileNumber"]);
            $sendBillRes = json_decode($sendBill, true);
            if ($sendBillRes["ResponseText"] == "Success") {
                // dd($sendBillRes);
                $invoices = new PostpaidRequest;
                $invoices
                    ->setfees(null)
                    ->setfees1(null)
                    ->setamount(null)
                    ->setamount1(null)
                    ->setamount2(null)
                    ->setreferenceNumber(null)
                    ->setinformativeOriginalWSamount(null)
                    ->settotalamount(null)
                    ->setcurrency(null)
                    ->setrounding(null)
                    ->setadditionalfees(null)
                    ->setPin(null)
                    ->setTransactionId(null)
                    ->setSuyoolUserId(rand())
                    ->setGsmNumber($data["mobileNumber"]);
                $this->mr->persist($invoices);
                $this->mr->flush();

                $invoicesId = $invoices->getId();
                // dd($invoicesId);
            } else {
                echo "error";
                $invoicesId = -1;
            }
            $message = "connected";
        } else {
            $message = "not connected";
            $invoicesId = -1;
        }

        return new JsonResponse([
            'status' => true,
            'message' => $message,
            'invoicesId' => $invoicesId
        ], 200);
    }

    /**
     * PostPaid
     * Provider : BOB
     * Desc: Retrieve Channel Results 
     * @Route("/alfa/bill/RetrieveResults", name="app_alfa_RetrieveResults",methods="POST")
     */
    public function RetrieveResults(Request $request, BobServices $bobServices)
    {
        $data = json_decode($request->getContent(), true);
        // dd($data);
        if ($data != null) {
            $retrieveResults = $bobServices->RetrieveResults($data["currency"], $data["mobileNumber"], $data["Pin"]);
            $jsonResult = json_decode($retrieveResults, true);
            $displayData = $jsonResult["Values"];

            $Pin = implode("", $data["Pin"]);
            $RandSuyoolUserId = rand();
            $invoicesId = $data["invoicesId"];
            // dd($invoicesId);

            $invoices =  $this->mr->getRepository(PostpaidRequest::class)->findOneBy(['id' => $invoicesId]);
            // $Postpaid = new Postpaid;
            $invoices
                ->setfees($jsonResult["Values"]["Fees"])
                ->setfees1($jsonResult["Values"]["Fees1"])
                ->setamount($jsonResult["Values"]["Amount"])
                ->setamount1($jsonResult["Values"]["Amount1"])
                ->setamount2($jsonResult["Values"]["Amount2"])
                ->setreferenceNumber($jsonResult["Values"]["ReferenceNumber"])
                ->setinformativeOriginalWSamount($jsonResult["Values"]["InformativeOriginalWSAmount"])
                ->settotalamount($jsonResult["Values"]["TotalAmount"])
                ->setcurrency($jsonResult["Values"]["Currency"])
                ->setrounding($jsonResult["Values"]["Rounding"])
                ->setadditionalfees($jsonResult["Values"]["AdditionalFees"])
                ->setSuyoolUserId($RandSuyoolUserId)
                ->setPin($Pin)
                ->setGsmNumber($data["mobileNumber"])
                ->setTransactionId($jsonResult["Values"]["TransactionId"]);

            $this->mr->persist($invoices);
            $this->mr->flush();

            // dd($invoices->getId());

            $invoicesId = $invoices->getId();
            // $postpaid = $this->mr->getRepository(Postpaid::class)->findOneBy(['id' => $postpayedId]);


            // $order = new Order;
            // $order
            //     ->setsuyoolUserId($RandSuyoolUserId)
            //     ->settransId(null)
            //     ->setpostpaidId($postpaid)
            //     ->setprepaidId(null)
            //     ->setstatus("Pending")
            //     ->setamount($jsonResult["Values"]["TotalAmount"])
            //     ->setcurrency($jsonResult["Values"]["Currency"]);
            // $this->mr->persist($order);
            // $this->mr->flush();

            // dd($order);
            $message = "connected";
        } else {
            $displayData = -1;
            $message = "No data retrived!!";
            $invoicesId = -1;
        }

        return new JsonResponse([
            'status' => true,
            'message' => $message,
            'postpayed' => $invoicesId,
            'displayData' => $displayData,
        ], 200);
    }

    /**
     * PostPaid
     * Provider : BOB
     * Desc: Retrieve Channel Results 
     * @Route("/alfa/bill/pay", name="app_alfa_bill_pay",methods="POST")
     */
    public function billPay(Request $request, BobServices $bobServices, SuyoolServices $suyoolServices, NotificationServices $notificationServices)
    {
        $data = json_decode($request->getContent(), true);
        $session = 89;
        $app_id = 2;
        $Postpaid_With_id = $this->mr->getRepository(PostpaidRequest::class)->findOneBy(['id' => $data["ResponseId"]]);
        $flagCode = null;

        if ($data != null) {
            //Initial order with status pending
            $order = new Order;
            $order
                ->setsuyoolUserId($session)
                ->settransId(null)
                ->setpostpaidId(null)
                ->setprepaidId(null)
                ->setstatus("pending")
                ->setamount($Postpaid_With_id->gettotalamount())
                ->setcurrency("LBP");
            $this->mr->persist($order);
            $this->mr->flush();

            //Take amount from .net
            $response = $suyoolServices->PushUtilities($session, $order->getId(), $order->getamount(), $order->getcurrency(), $this->hash_algo, $this->certificate, $app_id);

            if ($response[0]) {
                //set order status to held
                $orderupdate1 = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $session, 'status' => 'pending']);
                $orderupdate1
                    ->settransId($response[1])
                    ->setstatus("held");
                $this->mr->persist($orderupdate1);
                $this->mr->flush();


                //paid postpaid from bob Provider
                $billPay = $bobServices->BillPay($Postpaid_With_id);
                // dd($billPay);
                if ($billPay != "") {
                    $billPayArray = json_decode($billPay, true);

                    //if payment from loto provider success insert prepaid data to db
                    $postpaid = new Postpaid;
                    $postpaid
                        ->settransactionDescription($billPayArray["TransactionDescription"])
                        ->setstatus("pending")
                        ->setfees($Postpaid_With_id->getfees())
                        ->setfees1($Postpaid_With_id->getfees1())
                        ->setamount($Postpaid_With_id->getamount())
                        ->setamount1($Postpaid_With_id->getamount1())
                        ->setamount2($Postpaid_With_id->getamount2())
                        ->setreferenceNumber($Postpaid_With_id->getreferenceNumber())
                        ->setinformativeOriginalWSamount($Postpaid_With_id->getinformativeOriginalWSamount())
                        ->settotalamount($Postpaid_With_id->gettotalamount())
                        ->setcurrency($Postpaid_With_id->getcurrency())
                        ->setrounding($Postpaid_With_id->getrounding())
                        ->setadditionalfees($Postpaid_With_id->getadditionalfees())
                        ->setPin($Postpaid_With_id->getPin())
                        ->setTransactionId($Postpaid_With_id->getTransactionId())
                        ->setSuyoolUserId($Postpaid_With_id->getSuyoolUserId())
                        ->setGsmNumber($Postpaid_With_id->getGsmNumber());
                    $this->mr->persist($postpaid);
                    $this->mr->flush();

                    $IsSuccess = true;

                    $postpaidId = $postpaid->getId();
                    $postpaid = $this->mr->getRepository(Postpaid::class)->findOneBy(['id' => $postpaidId]);

                    //update order by passing prepaidId to order and set status to purshased
                    $orderupdate = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $session, 'status' => 'held']);
                    $orderupdate
                        ->setpostpaidId($postpaid)
                        ->setstatus("purchased");
                    $this->mr->persist($orderupdate);
                    $this->mr->flush();

                    //intial notification
                    $params = json_encode([
                        'amount' => $order->getamount(),
                        'currency' => $order->getcurrency(),
                        'mobilenumber' => $Postpaid_With_id->getGsmNumber(),
                    ]);
                    $additionalData = "";
                    $notificationServices->addNotification($session, 3, $params, $additionalData);

                    //tell the .net that total amount is paid
                    $responseUpdateUtilities = $suyoolServices->UpdateUtilities($order->getamount(), $this->hash_algo, $this->certificate, "", $orderupdate->gettransId());
                    if ($responseUpdateUtilities) {
                        $orderupdate5 = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $session, 'status' => 'purchased']);

                        //update te status from purshased to completed
                        $orderupdate5
                            ->setstatus("completed");
                        $this->mr->persist($orderupdate5);
                        $this->mr->flush();

                        $dataPayResponse = ['amount' => $order->getamount(), 'currency' => $order->getcurrency()];
                        $message = "Success";
                    } else {
                        $message = "something wrong while UpdateUtilities";
                        $dataPayResponse = -1;
                    }
                } else {
                    $IsSuccess = false;
                    $dataPayResponse = -1;
                    //if not purchase return money
                    $responseUpdateUtilities = $suyoolServices->UpdateUtilities(10, $this->hash_algo, $this->certificate, "", $orderupdate1->gettransId());
                    if ($responseUpdateUtilities) {
                        $orderupdate4 = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $session, 'status' => 'held']);
                        $orderupdate4
                            ->setstatus("completed");
                        $this->mr->persist($orderupdate4);
                        $this->mr->flush();

                        $message = "Success return money!!";
                    } else {
                        $message = "Can not return money!!";
                    }
                }
            } else {

                //if can not take money from .net cancel the state of the order
                $orderupdate3 = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $session, 'status' => 'pending']);
                $orderupdate3
                    ->setstatus("canceled");
                $this->mr->persist($orderupdate3);
                $this->mr->flush();
                $IsSuccess = false;
                $message = json_decode($response[1], true);
                $flagCode = $response[2];
                // $message = $response[1];
                $dataPayResponse = -1;
            }
        } else {
            $IsSuccess = false;
            $dataPayResponse = -1;
            $message = "Can not retrive data !!";
        }

        return new JsonResponse([
            'status' => true,
            'message' => $message,
            'IsSuccess' => $IsSuccess,
            'flagCode' => $flagCode,
            'data' => $dataPayResponse
        ], 200);
    }



    /**
     * PrePaid
     * Provider : LOTO
     * Desc: Fetch ReCharge vouchers
     * @Route("/alfa/ReCharge", name="app_alfa_ReCharge",methods="POST")
     */
    public function ReCharge(LotoServices $lotoServices, Memcached $Memcached)
    {

        // $filter = $lotoServices->VoucherFilter("ALFA");
        $filter =  $Memcached->getVouchers($lotoServices);
        // dd($filter);

        return new JsonResponse([
            'status' => true,
            'message' => $filter
        ], 200);
    }

    /**
     * PrePaid
     * Provider : LOTO
     * Desc: Buy PrePaid vouchers
     * @Route("/alfa/BuyPrePaid", name="app_alfa_BuyPrePaid",methods="POST")
     */
    public function BuyPrePaid(Request $request, LotoServices $lotoServices, SuyoolServices $suyoolServices, NotificationServices $notificationServices)
    {
        $session = 89;
        $app_id = 3;
        $data = json_decode($request->getContent(), true);
        $flagCode = null;
        // dd($data["desc"]);

        if ($data != null) {
            //Initial order with status pending
            $order = new Order;
            $order
                ->setsuyoolUserId($session)
                ->settransId(null)
                ->setpostpaidId(null)
                ->setprepaidId(null)
                ->setstatus("pending")
                ->setamount($data["amountLBP"])
                ->setcurrency("LBP");
            $this->mr->persist($order);
            $this->mr->flush();

            //Take amount from .net
            $response = $suyoolServices->PushUtilities($session, $order->getId(), $order->getamount(), $order->getcurrency(), $this->hash_algo, $this->certificate, $app_id);
            // $response = $suyoolServices->PushUtilities($session, $order->getId(), 1000, 'USD', $this->hash_algo, $this->certificate, $app_id);

            // dd($response);
            if ($response[0]) {
                //set order status to held
                $orderupdate1 = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $session, 'status' => 'pending']);
                $orderupdate1
                    ->settransId($response[1])
                    ->setstatus("held");
                $this->mr->persist($orderupdate1);
                $this->mr->flush();


                //buy voucher from loto Provider
                $BuyPrePaid = $lotoServices->BuyPrePaid($data["Token"], $data["category"], $data["type"]);
                // dd($BuyPrePaid);
                $PayResonse = $BuyPrePaid[0]["d"];
                $dataPayResponse = $PayResonse;
                if ($PayResonse["errorinfo"]["errorcode"] != 0) {
                    $logs = new Logs;
                    $logs
                        ->setidentifier("Prepaid Request")
                        ->seturl("https://backbone.lebaneseloto.com/Service.asmx/PurchaseVoucher")
                        ->setrequest($BuyPrePaid[1])
                        ->setresponse(json_encode($PayResonse))
                        ->seterror($PayResonse["errorinfo"]["errormsg"]);
                    $this->mr->persist($logs);
                    $this->mr->flush();
                }
                if ($PayResonse["errorinfo"]["errormsg"] == "SUCCESS") {
                    //if payment from loto provider success insert prepaid data to db
                    $prepaid = new Prepaid;
                    $prepaid
                        ->setvoucherSerial($PayResonse["voucherSerial"])
                        ->setvoucherCode($PayResonse["voucherCode"])
                        ->setvoucherExpiry($PayResonse["voucherExpiry"])
                        ->setdescription($PayResonse["desc"])
                        ->setdisplayMessage($PayResonse["displayMessage"])
                        ->settoken($PayResonse["token"])
                        ->setbalance($PayResonse["balance"])
                        ->seterrorMsg($PayResonse["errorinfo"]["errormsg"])
                        ->setinsertId($PayResonse["insertId"])
                        ->setSuyoolUserId($session);

                    $this->mr->persist($prepaid);
                    $this->mr->flush();

                    $IsSuccess = true;

                    $prepaidId = $prepaid->getId();
                    $prepaid = $this->mr->getRepository(Prepaid::class)->findOneBy(['id' => $prepaidId]);

                    //update order by passing prepaidId to order and set status to purshased
                    $orderupdate = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $session, 'status' => 'held']);
                    $orderupdate
                        ->setprepaidId($prepaid)
                        ->setstatus("purchased");
                    $this->mr->persist($orderupdate);
                    $this->mr->flush();

                    //intial notification
                    $params = json_encode([
                        'amount' => $order->getamount(),
                        'currency' => $order->getcurrency(),
                        'plan' => $data["desc"],
                        'code' => $PayResonse["voucherSerial"],
                    ]);
                    $additionalData = "*14*" . $prepaid->getvoucherSerial() . "#";
                    $notificationServices->addNotification($session, 4, $params, $additionalData);

                    //tell the .net that total amount is paid
                    $responseUpdateUtilities = $suyoolServices->UpdateUtilities($order->getamount(), $this->hash_algo, $this->certificate, "", $orderupdate->gettransId());
                    if ($responseUpdateUtilities) {
                        $orderupdate5 = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $session, 'status' => 'purchased']);

                        //update te status from purshased to completed
                        $orderupdate5
                            ->setstatus("completed");
                        $this->mr->persist($orderupdate5);
                        $this->mr->flush();

                        $message = "Success";
                    } else {
                        $message = "something wrong while UpdateUtilities";
                    }
                } else {
                    $IsSuccess = false;

                    //if not purchase return money
                    $responseUpdateUtilities = $suyoolServices->UpdateUtilities(10, $this->hash_algo, $this->certificate, "", $orderupdate1->gettransId());
                    if ($responseUpdateUtilities) {
                        $orderupdate4 = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $session, 'status' => 'held']);
                        $orderupdate4
                            ->setstatus("completed");
                        $this->mr->persist($orderupdate4);
                        $this->mr->flush();

                        $message = "Success return money!!";
                    } else {
                        $message = "Can not return money!!";
                    }
                }
            } else {

                //if can not take money from .net cancel the state of the order
                $orderupdate3 = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $session, 'status' => 'pending']);
                $orderupdate3
                    ->setstatus("canceled");
                $this->mr->persist($orderupdate3);
                $this->mr->flush();
                // $IsSuccess = false;
                // $message = $response[1];
                // $dataPayResponse = -1;
                $IsSuccess = false;
                $message = json_decode($response[1], true);
                $flagCode = $response[2];
                $dataPayResponse = -1;
            }
        } else {
            $IsSuccess = false;
            $dataPayResponse = -1;
            $message = "Can not retrive data !!";
        }

        return new JsonResponse([
            'status' => true,
            'message' => $message,
            'IsSuccess' => $IsSuccess,
            'flagCode' => $flagCode,
            'data' => $dataPayResponse
        ], 200);
    }

}
