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
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class TouchController extends AbstractController
{
    private $mr;
    private $hash_algo;
    private $certificate;
    private $params;


    public function __construct(ManagerRegistry $mr, $certificate, $hash_algo,ParameterBagInterface $params)
    {
        $this->mr = $mr->getManager('touch');
        $this->hash_algo = $hash_algo;
        $this->certificate = $certificate;
        $this->params=$params;
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

            $postpaidRequest = new PostpaidRequest;
            if ($sendBill[0]) {
                $postpaidRequest
                    ->setSuyoolUserId($session)
                    ->setGsmNumber($data["mobileNumber"])
                    ->settoken($sendBill[1])
                    ->seterror($sendBill[2])
                    ->sets2error(null)
                    ->setrequestId(null)
                    ->setPin(null)
                    ->setdisplayedFees(null)
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
            } else {
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
                    ->setdisplayedFees(null)
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
            }

            $this->mr->persist($postpaidRequest);
            $this->mr->flush();
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
        $displayedFees = 0;

        // dd($data);
        if ($data != null) {
            $postpaidRequestId = $data["invoicesId"];
            $postpaidRequest =  $this->mr->getRepository(PostpaidRequest::class)->findOneBy(['id' => $postpaidRequestId]);
            $retrieveResults = $bobServices->RetrieveResultsTouch($data["currency"], $data["mobileNumber"], $data["Pin"], $postpaidRequest->gettoken());
            // dd($retrieveResults);

            $Pin = implode("", $data["Pin"]);
            if ($retrieveResults[0]) {
                $values = $retrieveResults[1]["Values"];
                $displayedFees = intval($values["Fees"])+intval($values["Fees1"])+intval($values["AdditionalFees"]);

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
                    ->setdisplayedFees($displayedFees)
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
            'displayedFees' => $displayedFees,
        ], 200);
    }

    /**
     * PostPaid
     * Provider : BOB
     * Desc: Retrieve Channel Results 
     * @Route("/touch/bill/pay", name="app_touch_bill_pay",methods="POST")
     */
    public function billPay(Request $request, BobServices $bobServices, NotificationServices $notificationServices)
    {
        $suyoolServices=new SuyoolServices($this->params->get('TOUCH_POSTPAID_MERCHANT_ID'));
        $data = json_decode($request->getContent(), true);
        $SuyoolUserId = 155;
        $Postpaid_With_id = $this->mr->getRepository(PostpaidRequest::class)->findOneBy(['id' => $data["ResponseId"]]);
        $flagCode = null;
        // $billPay = $bobServices->BillPayTouch($Postpaid_With_id);
        // dd($billPay);
        if ($data != null) {
            //Initial order with status pending
            $order = new Order;
            $order
                ->setsuyoolUserId($SuyoolUserId)
                ->settransId(null)
                ->setpostpaidId(null)
                ->setprepaidId(null)
                ->setstatus(Order::$statusOrder['PENDING'])
                ->setamount($Postpaid_With_id->gettotalamount())
                ->setcurrency("LBP");
            $this->mr->persist($order);
            $this->mr->flush();

            $order_id=$this->params->get('TOUCH_POSTPAID_MERCHANT_ID')."-".$order->getId();

            //Take amount from .net
            // $response = $suyoolServices->PushUtilities($session, $order->getId(), 30000000, $order->getcurrency(), $this->hash_algo, $this->certificate, $app_id);
            $response = $suyoolServices->PushUtilities($SuyoolUserId, $order_id, $order->getamount(), $this->params->get('CURRENCY_LBP'));

            // dd($response);

            if ($response[0]) {
                //set order status to held
                $orderupdate1 = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $SuyoolUserId, 'status' =>Order::$statusOrder['PENDING']]);
                $orderupdate1
                    ->settransId($response[1])
                    ->setstatus(Order::$statusOrder['HELD']);
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
                        ->settransactionDescription($billPay[1]["TransactionDescription"])
                        ->settransactionReference($billPay[1]["TransactionReference"])
                        ->setPin($Postpaid_With_id->getpin())
                        ->setTransactionId($Postpaid_With_id->getTransactionId())
                        ->setcurrency($Postpaid_With_id->getcurrency())
                        ->setfees($Postpaid_With_id->getfees())
                        ->setfees1($Postpaid_With_id->getfees1())
                        ->setdisplayedFees($Postpaid_With_id->getdisplayedFees())
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
                    $orderupdate = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $SuyoolUserId, 'status' => Order::$statusOrder['HELD']]);
                    $orderupdate
                        ->setpostpaidId($postpaid)
                        ->setstatus(Order::$statusOrder['PURCHASED']);
                    $this->mr->persist($orderupdate);
                    $this->mr->flush();

                    //intial notification
                    $params = json_encode([
                        'amount' => $order->getamount(),
                        'currency' => $order->getcurrency(),
                        'mobilenumber' => $Postpaid_With_id->getGsmNumber(),
                    ]);
                    $additionalData = "";
                    $content=$notificationServices->getContent('AcceptedTouchPayment');
                    $bulk=0;//1 for broadcast 0 for unicast
                    $notificationServices->addNotification($SuyoolUserId, $content, $params,$bulk, $additionalData);

                    $updateUtilitiesAdditionalData = json_encode([
                        'Amount' => $Postpaid_With_id->getamount(),
                        'TransactionId' => $Postpaid_With_id->getTransactionId(),
                        'Amount1' => $Postpaid_With_id->getamount1(),
                        'referenceNumber' => $Postpaid_With_id->getreferenceNumber(),
                        'Amount2' => $Postpaid_With_id->getamount2(),
                        'InformativeOriginalWSAmount' => $Postpaid_With_id->getinformativeOriginalWSamount(),
                        'InvoiceNumber' => $Postpaid_With_id->getinvoiceNumber(),
                        'Fees' => $Postpaid_With_id->getfees(),
                        'Fees1' => $Postpaid_With_id->getfees1(),
                        'TotalAmount' => $Postpaid_With_id->gettotalamount(),
                        'Currency' => $Postpaid_With_id->getcurrency(),
                        'Rounding' => $Postpaid_With_id->getrounding(),
                        'PaymentId' => $Postpaid_With_id->getpaymentId(),
                        'AdditionalFees' => $Postpaid_With_id->getadditionalfees(),  
                    ]);

                    //tell the .net that total amount is paid
                    $responseUpdateUtilities = $suyoolServices->UpdateUtilities($order->getamount(), $updateUtilitiesAdditionalData , $orderupdate->gettransId());
                    if ($responseUpdateUtilities) {
                        $orderupdate5 = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $SuyoolUserId, 'status' => Order::$statusOrder['PURCHASED']]);

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
                    $responseUpdateUtilities = $suyoolServices->UpdateUtilities(0,"", $orderupdate1->gettransId());
                    if ($responseUpdateUtilities) {
                        $orderupdate4 = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $SuyoolUserId, 'status' => Order::$statusOrder['HELD']]);
                        $orderupdate4
                            ->setstatus(Order::$statusOrder['COMPLETED']);
                        $this->mr->persist($orderupdate4);
                        $this->mr->flush();

                        $message = "Success return money!!";
                    } else {
                        $message = "Can not return money!!";
                    }
                }
            } else {

                //if can not take money from .net cancel the state of the order
                $orderupdate3 = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $SuyoolUserId, 'status' => Order::$statusOrder['PENDING']]);
                $orderupdate3
                    ->setstatus(Order::$statusOrder['CANCELED']);
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
    public function BuyPrePaid(Request $request, LotoServices $lotoServices, NotificationServices $notificationServices)
    {
        $SuyoolUserId = 89;
        $suyoolServices=new SuyoolServices($this->params->get('TOUCH_PREPAID_MERCHANT_ID'));
        $data = json_decode($request->getContent(), true);
        $flagCode = null;
        // dd($data["desc"]);

        if ($data != null) {
            //Initial order with status pending
            $order = new Order;
            $order
                ->setsuyoolUserId($SuyoolUserId)
                ->settransId(null)
                ->setpostpaidId(null)
                ->setprepaidId(null)
                ->setstatus(Order::$statusOrder['PENDING'])
                ->setamount($data["amountLBP"])
                ->setcurrency("LBP");
            $this->mr->persist($order);
            $this->mr->flush();

            $order_id=$this->params->get('TOUCH_PREPAID_MERCHANT_ID')."-".$order->getId();

            //Take amount from .net
            $response = $suyoolServices->PushUtilities($SuyoolUserId, $order_id, $order->getamount(), $order->getcurrency());
            // $response = $suyoolServices->PushUtilities($session, $order->getId(), 1000, 'USD', $this->hash_algo, $this->certificate, $app_id);

            // dd($response);
            if ($response[0]) {
                //set order status to held
                $orderupdate1 = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $SuyoolUserId, 'status' => Order::$statusOrder['PENDING']]);
                $orderupdate1
                    ->settransId($response[1])
                    ->setstatus(Order::$statusOrder['HELD']);
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
                        ->setSuyoolUserId($SuyoolUserId);

                    $this->mr->persist($prepaid);
                    $this->mr->flush();

                    $IsSuccess = true;

                    $prepaidId = $prepaid->getId();
                    $prepaid = $this->mr->getRepository(Prepaid::class)->findOneBy(['id' => $prepaidId]);

                    //update order by passing prepaidId to order and set status to purshased
                    $orderupdate = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $SuyoolUserId, 'status' => Order::$statusOrder['HELD']]);
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
                    $content=$notificationServices->getContent('AlfaCardPurchasedSuccessfully');
                    $bulk=0;//1 for broadcast 0 for unicast
                    $notificationServices->addNotification($SuyoolUserId, $content, $params,$bulk);
                    //tell the .net that total amount is paid
                    $responseUpdateUtilities = $suyoolServices->UpdateUtilities($order->getamount(), "", $orderupdate->gettransId());
                    if ($responseUpdateUtilities) {
                        $orderupdate5 = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $SuyoolUserId, 'status' => Order::$statusOrder['PURCHASED']]);

                        //update te status from purshased to completed
                        $orderupdate5
                            ->setstatus(Order::$statusOrder['COMPLETED']);
                        $this->mr->persist($orderupdate5);
                        $this->mr->flush();

                        $message = "Success";
                    } else {
                        $message = "something wrong while UpdateUtilities";
                    }
                } else {
                    $IsSuccess = false;

                    //if not purchase return money
                    $responseUpdateUtilities = $suyoolServices->UpdateUtilities(0, "", $orderupdate1->gettransId());
                    // dd($responseUpdateUtilities);
                    if ($responseUpdateUtilities) {
                        $orderupdate4 = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $SuyoolUserId, 'status' => Order::$statusOrder['HELD']]);
                        $orderupdate4
                            ->setstatus(Order::$statusOrder['COMPLETED']);
                        $this->mr->persist($orderupdate4);
                        $this->mr->flush();

                        $message = "Success return money!!";
                    } else {
                        $message = "Can not return money!!";
                    }
                }
            } else {

                //if can not take money from .net cancel the state of the order
                $orderupdate3 = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $SuyoolUserId, 'status' => Order::$statusOrder['PENDING']]);
                $orderupdate3
                    ->setstatus(Order::$statusOrder['CANCELED']);
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
