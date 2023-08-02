<?php

namespace App\Controller;

use App\Entity\Touch\Logs;
use App\Entity\Touch\Order;
use App\Entity\Touch\Postpaid;
use App\Entity\Touch\Prepaid;
use App\Entity\Touch\PostpaidRequest;
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

class TouchController extends AbstractController
{
    private $mr;
    private $hash_algo;
    private $certificate;

    public function __construct(ManagerRegistry $mr, $certificate, $hash_algo)
    {
        $this->mr = $mr->getManager('touch');
        $this->hash_algo = $hash_algo;
        $this->certificate = $certificate;
    }

    /**
     * @Route("/touch", name="app_touch")
     */
    public function index()
    {
        // phpinfo();
        $postpaid = $this->mr->getRepository(Postpaid::class)->findAll();
        $orders = $this->mr->getRepository(Order::class)->findAll();
        // dd($orders);
        $parameters['Test'] = "tst";

        return $this->render('touch/index.html.twig', [
            'parameters' => $parameters
        ]);
    }


    /**
     * PostPaid
     * Provider : BOB
     * Desc: Send Pin to user based on phoneNumber
     * @Route("/touch/bill", name="app_touch_bill",methods="POST")
     */
    public function bill(Request $request, BobServices $bobServices)
    {
        $session = 155;
        $data = json_decode($request->getContent(), true);

        if ($data != null) {
            $sendBill = $bobServices->SendTouchPinRequest($data["mobileNumber"]);

            if ($sendBill[0]) {
                $postpaidRequest = new PostpaidRequest;
                $postpaidRequest
                    ->setSuyoolUserId($session)
                    ->setGsmNumber($data["mobileNumber"])
                    ->settoken($sendBill[1])
                    ->seterror($sendBill[2])
                    ->sets2error(null)
                    ->setrequestId(null)
                    ->setPin(null)
                    ->setTransactionId(null)
                    ->setcurrency(null)
                    ->setfees(null)
                    ->setfees1(null)
                    ->setamount(null)
                    ->setamount1(null)
                    ->setamount2(null)
                    ->setreferenceNumber(null)
                    ->setinformativeOriginalWSamount(null)
                    ->settotalamount(null)
                    ->setrounding(null)
                    ->setadditionalfees(null)
                    ->setinvoiceNumber(null)
                    ->setpaymentId(null);
                $this->mr->persist($postpaidRequest);
                $this->mr->flush();
            } else {
                $postpaidRequest = new PostpaidRequest;
                $postpaidRequest
                    ->setSuyoolUserId($session)
                    ->setGsmNumber($data["mobileNumber"])
                    ->settoken($sendBill[1])
                    ->seterror($sendBill[2])
                    ->sets2error(null)
                    ->setrequestId(null)
                    ->setPin(null)
                    ->setTransactionId(null)
                    ->setcurrency(null)
                    ->setfees(null)
                    ->setfees1(null)
                    ->setamount(null)
                    ->setamount1(null)
                    ->setamount2(null)
                    ->setreferenceNumber(null)
                    ->setinformativeOriginalWSamount(null)
                    ->settotalamount(null)
                    ->setrounding(null)
                    ->setadditionalfees(null)
                    ->setinvoiceNumber(null)
                    ->setpaymentId(null);
                $this->mr->persist($postpaidRequest);
                $this->mr->flush();
            }

            $postpaidRequestId = $postpaidRequest->getId();
            // dd($postpaidRequestId);
        }

        return new JsonResponse([
            'status' => true,
            'isSuccess' => $sendBill[0],
            'postpaidRequestId' => $postpaidRequestId
        ], 200);
    }

    /**
     * PostPaid
     * Provider : BOB
     * Desc: Retrieve Channel Results 
     * @Route("/touch/bill/RetrieveResults", name="app_touch_RetrieveResults",methods="POST")
     */
    public function RetrieveResults(Request $request, BobServices $bobServices)
    {
        $data = json_decode($request->getContent(), true);
        // dd($data);
        if ($data != null) {
            $postpaidRequestId = $data["invoicesId"];
            $postpaidRequest =  $this->mr->getRepository(PostpaidRequest::class)->findOneBy(['id' => $postpaidRequestId]);
            $retrieveResults = $bobServices->RetrieveResultsTouch($data["currency"], $data["mobileNumber"], $data["Pin"], $postpaidRequest->gettoken());
            // dd($retrieveResults);

            $Pin = implode("", $data["Pin"]);
            if ($retrieveResults[0]) {
                $values = $retrieveResults[1]["Values"];

                $postpaidRequest =  $this->mr->getRepository(PostpaidRequest::class)->findOneBy(['id' => $postpaidRequestId]);
                $postpaidRequest
                    ->sets2error($retrieveResults[2])
                    ->setPin($Pin)
                    ->setTransactionId($values["transactionId"])
                    ->setcurrency($values["Currency"])
                    ->setfees($values["Fees"])
                    ->setfees1($values["Fees1"])
                    ->setamount($values["Amount"])
                    ->setamount1($values["Amount1"])
                    ->setamount2($values["Amount2"])
                    ->setreferenceNumber($values["referenceNumber"])
                    ->setinformativeOriginalWSamount($values["InformativeOriginalWSAmount"])
                    ->settotalamount($values["TotalAmount"])
                    ->setrounding($values["Rounding"])
                    ->setadditionalfees($values["AdditionalFees"])
                    ->setinvoiceNumber($values["InvoiceNumber"])
                    ->setpaymentId($values["PaymentId"]);
                $this->mr->persist($postpaidRequest);
                $this->mr->flush();
            } else {
                $values = 0;
                $postpaidRequest =  $this->mr->getRepository(PostpaidRequest::class)->findOneBy(['id' => $postpaidRequestId]);
                $postpaidRequest
                    ->sets2error($retrieveResults[2])
                    ->setrequestId($retrieveResults[1])
                    ->setPin($Pin);
                $this->mr->persist($postpaidRequest);
                $this->mr->flush();
            }
            $invoicesId = $postpaidRequest->getId();
        } else {
            $values = -1;
            $invoicesId = -1;
        }

        return new JsonResponse([
            'status' => true,
            'isSuccess' => $retrieveResults[0],
            'postpayed' => $invoicesId,
            'displayData' => $values,
        ], 200);
    }

    /**
     * PostPaid
     * Provider : BOB
     * Desc: Retrieve Channel Results 
     * @Route("/touch/bill/pay", name="app_touch_bill_pay",methods="POST")
     */
    public function billPay(Request $request, BobServices $bobServices, SuyoolServices $suyoolServices, NotificationServices $notificationServices)
    {
        $data = json_decode($request->getContent(), true);
        $session = 155;
        $app_id = 4;
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
            // $response = $suyoolServices->PushUtilities($session, $order->getId(), 30000000, $order->getcurrency(), $this->hash_algo, $this->certificate, $app_id);
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
                $billPay = $bobServices->BillPayTouch($Postpaid_With_id);

                // dd($billPay);
                if ($billPay[0]) {
                    //if payment from loto provider success insert prepaid data to db
                    $postpaid = new Postpaid;
                    $postpaid
                        ->setSuyoolUserId($Postpaid_With_id->getSuyoolUserId())
                        ->setGsmNumber($Postpaid_With_id->getGsmNumber())
                        ->settoken($Postpaid_With_id->gettoken())
                        ->setPin($Postpaid_With_id->getpin())
                        ->setTransactionId($Postpaid_With_id->getTransactionId())
                        ->setcurrency($Postpaid_With_id->getcurrency())
                        ->setfees($Postpaid_With_id->getfees())
                        ->setfees1($Postpaid_With_id->getfees1())
                        ->setamount($Postpaid_With_id->getamount())
                        ->setamount1($Postpaid_With_id->getamount1())
                        ->setamount2($Postpaid_With_id->getamount2())
                        ->setreferenceNumber($Postpaid_With_id->getreferenceNumber())
                        ->setinformativeOriginalWSamount($Postpaid_With_id->getinformativeOriginalWSamount())
                        ->settotalamount($Postpaid_With_id->gettotalamount())
                        ->setrounding($Postpaid_With_id->getrounding())
                        ->setadditionalfees($Postpaid_With_id->getadditionalfees())
                        ->setinvoiceNumber($Postpaid_With_id->getinvoiceNumber())
                        ->setpaymentId($Postpaid_With_id->getpaymentId())
                        ->seterror($billPay[2]);
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
                    $notificationServices->addNotification($session, 5, $params, $additionalData);

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
     * @Route("/touch/ReCharge", name="app_touch_ReCharge",methods="POST")
     */
    public function ReCharge(LotoServices $lotoServices, Memcached $Memcached)
    {

        // $filter = $lotoServices->VoucherFilter("MTC");
        $filter =  $Memcached->getVouchersTouch($lotoServices);
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
     * @Route("/touch/BuyPrePaid", name="app_touch_BuyPrePaid",methods="POST")
     */
    public function BuyPrePaid(Request $request, LotoServices $lotoServices, SuyoolServices $suyoolServices, NotificationServices $notificationServices)
    {
        $session = 89;
        $app_id = 5;
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
                    $notificationServices->addNotification($session, 4, $params, "");

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
                $IsSuccess = false;
                // $message = $response[1];
                // $dataPayResponse = -1;
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
