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
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class AlfaController extends AbstractController
{
    private $mr;
    private $hash_algo;
    private $certificate;
    private $notMr;
    private $params;
    public $cipher_algorithme = "AES128";
    public $key = "SY1X24elh9eG3fpOaHcWlQ9h2bHaqimdIDoyoOaFoi0rukAj3Z";
    public $iv = "fgu26y9e43wc8dj2"; //initiallization vector for decrypt
    private $session;

    public function __construct(ManagerRegistry $mr, $certificate, $hash_algo, ParameterBagInterface $params, SessionInterface $sessionInterface)
    {
        $this->mr = $mr->getManager('alfa');
        $this->hash_algo = $hash_algo;
        $this->certificate = $certificate;
        $this->notMr = $mr->getManager('notification');
        $this->params = $params;
        $this->session = $sessionInterface;
    }

    /**
     * @Route("/alfa", name="app_alfa")
     */
    public function index(NotificationServices $notificationServices)
    {
        $useragent = $_SERVER['HTTP_USER_AGENT'];
        if (isset($_POST['infoString'])) {
            $string_to_decrypt = $_POST['infoString'];
            $decrypted_string = openssl_decrypt($string_to_decrypt, $this->cipher_algorithme, $this->key, 0, $this->iv);
            // dd($decrypted_string);
            $suyoolUserInfo = explode("!#!", $decrypted_string);
            $devicetype = stripos($useragent, $suyoolUserInfo[1]);

            if ($notificationServices->checkUser($suyoolUserInfo[0], $suyoolUserInfo[2]) && $devicetype) {
                $SuyoolUserId = $suyoolUserInfo[0];
                $this->session->set('suyoolUserId', $SuyoolUserId);
                $parameters['deviceType'] = $suyoolUserInfo[1];


                return $this->render('alfa/index.html.twig', [
                    'parameters' => $parameters
                ]);
            } else {
                return $this->render('ExceptionHandling.html.twig');
            }
        } else {
            return $this->render('ExceptionHandling.html.twig');
        }
    }


    /**
     * PostPaid
     * Provider : BOB
     * Desc: Send Pin to user based on phoneNumber
     * @Route("/alfa/bill", name="app_alfa_bill",methods="POST")
     */
    public function bill(Request $request, BobServices $bobServices)
    {
        $SuyoolUserId = $this->session->get('suyoolUserId');

        $data = json_decode($request->getContent(), true);
        // dd($data);
        if ($data != null) {
            $sendBill = $bobServices->Bill($data["mobileNumber"]);
            // dd($sendBill);
            $sendBillRes = json_decode($sendBill, true);
            if (isset($sendBillRes["ResponseText"]) && $sendBillRes["ResponseText"] == "Success") {
                // dd($sendBillRes);
                $invoices = new PostpaidRequest;
                $invoices
                    ->setSuyoolUserId($SuyoolUserId)
                    ->setGsmNumber($data["mobileNumber"]);
                $this->mr->persist($invoices);
                $this->mr->flush();

                $invoicesId = $invoices->getId();
                // dd($invoicesId);
                $message = "connected";
            } else {
                echo "error";
                $invoicesId = -1;
                $message="not connected";
            }
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
        $SuyoolUserId = $this->session->get('suyoolUserId');

        $data = json_decode($request->getContent(), true);
        $displayedFees = 0;

        // dd($data);
        if ($data != null) {
            $retrieveResults = $bobServices->RetrieveResults($data["currency"], $data["mobileNumber"], $data["Pin"]);
            // dd($retrieveResults);
            if(isset($retrieveResults) && $retrieveResults != ""){
            $jsonResult = json_decode($retrieveResults, true);
            $displayData = $jsonResult["Values"];

            $Pin = implode("", $data["Pin"]);
            $RandSuyoolUserId = rand();
            $invoicesId = $data["invoicesId"];
            $displayedFees = intval($jsonResult["Values"]["Fees"])+intval($jsonResult["Values"]["Fees1"])+intval($jsonResult["Values"]["AdditionalFees"]);

            // dd($invoicesId);
            $invoices =  $this->mr->getRepository(PostpaidRequest::class)->findOneBy(['id' => $invoicesId]);
            // $Postpaid = new Postpaid;
            $invoices
                ->setfees($jsonResult["Values"]["Fees"])
                ->setfees1($jsonResult["Values"]["Fees1"])
                ->setdisplayedFees($displayedFees)
                ->setamount($jsonResult["Values"]["Amount"])
                ->setamount1($jsonResult["Values"]["Amount1"])
                ->setamount2($jsonResult["Values"]["Amount2"])
                ->setreferenceNumber($jsonResult["Values"]["ReferenceNumber"])
                ->setinformativeOriginalWSamount($jsonResult["Values"]["InformativeOriginalWSAmount"])
                ->settotalamount($jsonResult["Values"]["TotalAmount"])
                ->setcurrency($jsonResult["Values"]["Currency"])
                ->setrounding($jsonResult["Values"]["Rounding"])
                ->setadditionalfees($jsonResult["Values"]["AdditionalFees"])
                ->setSuyoolUserId($SuyoolUserId)
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
            }else{
                $displayData = -1;
            $message = "pinwrong";
            $invoicesId = -1;
            }
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
            'displayedFees' => $displayedFees,
        ], 200);
    }

    /**
     * PostPaid
     * Provider : BOB
     * Desc: Retrieve Channel Results 
     * @Route("/alfa/bill/pay", name="app_alfa_bill_pay",methods="POST")
     */
    public function billPay(Request $request, BobServices $bobServices, NotificationServices $notificationServices)
    {

        $suyoolServices = new SuyoolServices($this->params->get('ALFA_POSTPAID_MERCHANT_ID'));
        $data = json_decode($request->getContent(), true);
        $SuyoolUserId = $this->session->get('suyoolUserId');
        $Postpaid_With_id = $this->mr->getRepository(PostpaidRequest::class)->findOneBy(['id' => $data["ResponseId"]]);
        $flagCode = null;

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

            $order_id = $this->params->get('ALFA_PREPAID_MERCHANT_ID') . "-" . $order->getId();


            //Take amount from .net
            $response = $suyoolServices->PushUtilities($SuyoolUserId, $order_id, $order->getamount(), $this->params->get('CURRENCY_LBP'));

            if ($response[0]) {
                //set order status to held
                $orderupdate1 = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $SuyoolUserId, 'status' => Order::$statusOrder['PENDING']]);
                $orderupdate1
                    ->settransId($response[1])
                    ->setstatus(Order::$statusOrder['HELD']);
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
                        ->setstatus(Order::$statusOrder['PENDING'])
                        ->setfees($Postpaid_With_id->getfees())
                        ->setfees1($Postpaid_With_id->getfees1())
                        ->setdisplayedFees($Postpaid_With_id->getdisplayedFees())
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

                    $content = $notificationServices->getContent('AcceptedAlfaPayment');
                    $bulk = 0; //1 for broadcast 0 for unicast
                    $notificationServices->addNotification($SuyoolUserId, $content, $params, $bulk, $additionalData);

                    $updateUtilitiesAdditionalData = json_encode([
                        'Fees' => $Postpaid_With_id->getfees(),
                        'Fees1' => $Postpaid_With_id->getfees1(),
                        'AdditionalFees' => $Postpaid_With_id->getadditionalfees(),
                        'TransactionId' => $Postpaid_With_id->getTransactionId(),
                        'Amount' => $Postpaid_With_id->getamount(),
                        'Amount1' => $Postpaid_With_id->getamount1(),
                        'ReferenceNumber' => $Postpaid_With_id->getreferenceNumber(),
                        'Amount2' => $Postpaid_With_id->getamount2(),
                        'InformativeOriginalWSAmount' => $Postpaid_With_id->getinformativeOriginalWSamount(),
                        'TotalAmount' => $Postpaid_With_id->gettotalamount(),
                        'Currency' => $Postpaid_With_id->getcurrency(),
                        'Rounding' => $Postpaid_With_id->getrounding(),
                    ]);
                    // dd($updateUtilitiesAdditionalData);
                    //tell the .net that total amount is paid
                    $responseUpdateUtilities = $suyoolServices->UpdateUtilities($order->getamount(),  $updateUtilitiesAdditionalData, $orderupdate->gettransId());
                    if ($responseUpdateUtilities) {
                        $orderupdate5 = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $SuyoolUserId, 'status' =>Order::$statusOrder['PURCHASED']]);

                        //update te status from purshased to completed
                        $orderupdate5
                            ->setstatus(Order::$statusOrder['COMPLETED']);
                        $this->mr->persist($orderupdate5);
                        $this->mr->flush();

                        $dataPayResponse = ['amount' => $order->getamount(), 'currency' => $order->getcurrency()];
                        $message = "Success";
                    } else {
                        $orderupdate5 = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $SuyoolUserId, 'status' => Order::$statusOrder['PURCHASED']]);
                        $orderupdate5
                            ->setstatus(Order::$statusOrder['CANCELED'])
                            ->seterror($responseUpdateUtilities[1]);
                        $this->mr->persist($orderupdate5);
                        $this->mr->flush();
                        $message = "something wrong while UpdateUtilities";
                        $dataPayResponse = -1;
                    }
                } else {
                    $IsSuccess = false;
                    $dataPayResponse = -1;
                    //if not purchase return money
                    $responseUpdateUtilities = $suyoolServices->UpdateUtilities(0, "", $orderupdate1->gettransId());
                    if ($responseUpdateUtilities[0]) {
                        $orderupdate4 = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $SuyoolUserId, 'status' => Order::$statusOrder['HELD']]);
                        $orderupdate4
                            ->setstatus(Order::$statusOrder['COMPLETED']);

                        $this->mr->persist($orderupdate4);
                        $this->mr->flush();

                        $message = "Success return money!!";
                    } else {
                        $orderupdate4 = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $SuyoolUserId, 'status' => Order::$statusOrder['HELD']]);
                        $orderupdate4
                            ->setstatus(Order::$statusOrder['CANCELED'])
                            ->seterror($responseUpdateUtilities[1]);
                        $this->mr->persist($orderupdate4);
                        $this->mr->flush();
                        $message = "Can not return money!!";
                    }
                }
            } else {

                //if can not take money from .net cancel the state of the order
                $orderupdate3 = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $SuyoolUserId, 'status' => Order::$statusOrder['PENDING']]);
                $orderupdate3
                    ->setstatus(Order::$statusOrder['CANCELED'])
                    ->seterror($response[1]);
                $this->mr->persist($orderupdate3);
                $this->mr->flush();
                $IsSuccess = false;
                $message = json_decode($response[1], true);
                if (isset($response[2])) {
                    $flagCode = $response[2];
                }
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
            'data' => $dataPayResponse,
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
    public function BuyPrePaid(Request $request, LotoServices $lotoServices, NotificationServices $notificationServices)
    {
        $SuyoolUserId = $this->session->get('suyoolUserId');
        $suyoolServices = new SuyoolServices($this->params->get('ALFA_PREPAID_MERCHANT_ID'));
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

            $order_id = $this->params->get('ALFA_PREPAID_MERCHANT_ID') . "-" . $order->getId();

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
                    $IsSuccess = false;
                    $message = $PayResonse["errorinfo"]["errormsg"];
                    //if not purchase return money
                    $responseUpdateUtilities = $suyoolServices->UpdateUtilities(0, "", $orderupdate1->gettransId());
                    if ($responseUpdateUtilities[0]) {
                        $orderupdate4 = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $SuyoolUserId, 'status' => Order::$statusOrder['HELD']]);
                        $orderupdate4
                            ->setstatus(Order::$statusOrder['CANCELED'])
                            ->seterror("{reversed ".$message."}");
                        $this->mr->persist($orderupdate4);
                        $this->mr->flush();

                        // $message = "Success return money!!";
                    } else {
                        $orderupdate4 = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $SuyoolUserId, 'status' => Order::$statusOrder['HELD']]);
                        $orderupdate4
                            ->setstatus(Order::$statusOrder['CANCELED'])
                            ->seterror($responseUpdateUtilities[1]);
                        // $message = "Can not return money!!";
                    }
                }
                if ($PayResonse["errorinfo"]["errorcode"] == 0) {
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
                        ->setstatus(Order::$statusOrder['PURCHASED']);
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
                    $content = $notificationServices->getContent('AlfaCardPurchasedSuccessfully');
                    $bulk = 0; //1 for broadcast 0 for unicast
                    $notificationServices->addNotification($SuyoolUserId, $content, $params, $bulk, $additionalData);

                    //tell the .net that total amount is paid
                    $responseUpdateUtilities = $suyoolServices->UpdateUtilities($order->getamount(), "", $orderupdate->gettransId());
                    if ($responseUpdateUtilities[0]) {
                        $orderupdate5 = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $SuyoolUserId, 'status' => Order::$statusOrder['PURCHASED']]);

                        //update te status from purshased to completed
                        $orderupdate5
                            ->setstatus(Order::$statusOrder['COMPLETED']);
                        $this->mr->persist($orderupdate5);
                        $this->mr->flush();

                        $message = "Success";
                    } else {
                        $orderupdate5 = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $SuyoolUserId, 'status' => Order::$statusOrder['PURCHASED']]);
                        $orderupdate5
                            ->setstatus(Order::$statusOrder['CANCELED'])
                            ->seterror($responseUpdateUtilities[1]);
                        $message = "something wrong while UpdateUtilities";
                    }
                }
            } else {

                //if can not take money from .net cancel the state of the order
                $orderupdate3 = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $SuyoolUserId, 'status' => Order::$statusOrder['PENDING']]);
                $orderupdate3
                    ->setstatus(Order::$statusOrder['CANCELED'])
                    ->seterror($response[3]);
                $this->mr->persist($orderupdate3);
                $this->mr->flush();
                // $IsSuccess = false;
                // $message = $response[1];
                // $dataPayResponse = -1;
                $IsSuccess = false;
                
                if (isset($response[2])) {
                    $message = json_decode($response[3], true);
                    $flagCode = $response[2];
                }else{
                    $message = "You can not purchase now";
                }
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
