<?php

namespace App\Controller;

use App\Entity\Ogero\Landline;
use App\Entity\Ogero\LandlineRequest;
use App\Entity\Ogero\Logs;
use App\Entity\Ogero\Order;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\BobServices;
use App\Service\DecryptService;
use App\Service\NotificationServices;
use App\Service\SuyoolServices;
use Exception;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class OgeroController extends AbstractController
{
    private $mr;
    private $params;
    public $cipher_algorithme = "AES128";
    public $key = "SY1X24elh9eG3fpOaHcWlQ9h2bHaqimdIDoyoOaFoi0rukAj3Z";
    public $iv = "fgu26y9e43wc8dj2"; //initiallization vector for decrypt
    private $session;

    public function __construct(ManagerRegistry $mr, ParameterBagInterface $params, SessionInterface $sessionInterface)
    {
        $this->mr = $mr->getManager('ogero');
        $this->params = $params;
        $this->session = $sessionInterface;
    }

    /**
     * @Route("/ogero", name="app_ogero")
     */
    public function index(NotificationServices $notificationServices): Response
    {
        $useragent = $_SERVER['HTTP_USER_AGENT'];
        // $_POST['infoString']="3mzsXlDm5DFUnNVXA5Pu8T1d5nNACEsiiUEAo7TteE/x3BGT3Oy3yCcjUHjAVYk3";

        if (isset($_POST['infoString'])) {
            $decrypted_string = SuyoolServices::decrypt($_POST['infoString']);
            $suyoolUserInfo = explode("!#!", $decrypted_string);
            if (!isset($suyoolUserInfo[1])) {
                return $this->render('ExceptionHandling.html.twig');
            }
            $devicetype = stripos($useragent, $suyoolUserInfo[1]);
            $parameters['deviceType'] = $suyoolUserInfo[1];
            if ($notificationServices->checkUser($suyoolUserInfo[0], $suyoolUserInfo[2]) && $devicetype) {
                $this->session->set('suyoolUserId', $suyoolUserInfo[0]);
                return $this->render('ogero/index.html.twig', ['parameters' => $parameters]);
            } else {
                return $this->render('ExceptionHandling.html.twig');
            }
        } else {
            return $this->render('ExceptionHandling.html.twig');
        }
    }

    /**
     * Landline
     * Provider : BOB
     * Desc: Get TrasactionId to user based on phoneNumber
     * @Route("/ogero/landline", name="app_ogero_landline",methods="POST")
     */
    public function bill(Request $request, BobServices $bobServices)
    {
        $suyoolUserId = $this->session->get('suyoolUserId');
        $data = json_decode($request->getContent(), true);
        $displayedFees = 0;

        if ($data != null) {
            $RetrieveChannel = $bobServices->RetrieveChannelResults($data["mobileNumber"]);
            // dd($RetrieveChannel);
            if ($RetrieveChannel[0] == true) {
                $resp = $RetrieveChannel[1]["Values"];
                $displayedFees = intval($resp["Fees"]) + intval($resp["Fees1"]) + intval($resp["AdditionalFees"]) + intval($resp["OgeroFees"]);

                $LandlineReq = new LandlineRequest;
                $LandlineReq
                    ->setsuyoolUserId($suyoolUserId)
                    ->setgsmNumber($data["mobileNumber"])
                    ->setresponse($RetrieveChannel[3])
                    ->seterrordesc($RetrieveChannel[2])
                    ->settransactionId($resp["TransactionId"])
                    ->setogeroBills(json_encode($resp["OgeroBills"]))
                    ->setogeroPenalty($resp["OgeroPenalty"])
                    ->setogeroInitiationDate($resp["OgeroInitiationDate"])
                    ->setogeroClientName($resp["OgeroClientName"])
                    ->setogeroAddress($resp["OgeroAddress"])
                    ->setcurrency($resp["Currency"])
                    ->setamount($resp["Amount"])
                    ->setamount1($resp["Amount1"])
                    ->setamount2($resp["Amount2"])
                    ->settotalAmount($resp["TotalAmount"])
                    ->setogeroTotalAmount($resp["OgeroTotalAmount"])
                    ->setogeroFees($resp["OgeroFees"])
                    ->setadditionalFees($resp["AdditionalFees"])
                    ->setfees($resp["Fees"])
                    ->setfees1($resp["Fees1"])
                    ->setdisplayedFees($displayedFees)
                    ->setrounding($resp["Rounding"]);
                $this->mr->persist($LandlineReq);
                $this->mr->flush();

                $LandlineReqId = $LandlineReq->getId();
                $message = $resp;
                $mobileNb = $data["mobileNumber"];
            } else {
                $LandlineReq = new LandlineRequest;
                $LandlineReq
                    ->setsuyoolUserId($suyoolUserId)
                    ->setgsmNumber($data["mobileNumber"])
                    ->setresponse($RetrieveChannel[3])
                    ->seterrordesc($RetrieveChannel[2]);
                $this->mr->persist($LandlineReq);
                $this->mr->flush();
                $error = explode("-", $RetrieveChannel[2]);
                $errorcode = $error[1];
                // if($errorcode == 113){
                //     dd("ok");
                // }
                $message = $errorcode;
                $LandlineReqId = -1;
                $mobileNb = $data["mobileNumber"];
            }
        } else {
            $message = "not connected";
            $LandlineReqId = -1;
            $mobileNb = -1;
        }

        return new JsonResponse([
            'status' => true,
            'message' => $message,
            'mobileNb' => $mobileNb,
            'LandlineReqId' => $LandlineReqId,
            'displayedFees' => $displayedFees,
        ], 200);
    }

    /**
     * Landline
     * Provider : BOB
     * Desc: Pay Landline Request 
     * @Route("/ogero/landline/pay", name="app_ogero_landline_pay",methods="POST")
     */
    public function billPay(Request $request, BobServices $bobServices, NotificationServices $notificationServices)
    {
        $suyoolServices = new SuyoolServices($this->params->get('OGERO_MERCHANT_ID'));
        $data = json_decode($request->getContent(), true);
        $suyoolUserId = $this->session->get('suyoolUserId');
        $Landline_With_id = $this->mr->getRepository(LandlineRequest::class)->findOneBy(['id' => $data["LandlineId"]]);
        $flagCode = null;

        if ($data != null) {
            //Initial order with status pending
            $order = new Order;
            $order
                ->setsuyoolUserId($suyoolUserId)
                ->settransId(null)
                ->setlandlineId(null)
                ->setstatus(Order::$statusOrder['PENDING'])
                ->setamount($Landline_With_id->getamount() + $Landline_With_id->getfees())
                ->setfees($Landline_With_id->getfees())
                ->setcurrency("LBP");
            $this->mr->persist($order);
            $this->mr->flush();

            $orderTst = $this->params->get('OGERO_MERCHANT_ID') . "-" . $order->getId();
            //Take amount from .net
            $response = $suyoolServices->PushUtilities($suyoolUserId, $orderTst, $order->getamount(), $this->params->get('CURRENCY_LBP'), $Landline_With_id->getfees());

            if ($response[0]) {
                //set order status to held
                $orderupdate1 = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $suyoolUserId, 'status' => Order::$statusOrder['PENDING']]);
                $orderupdate1
                    ->settransId($response[1])
                    ->setstatus(Order::$statusOrder['HELD']);
                $this->mr->persist($orderupdate1);
                $this->mr->flush();

                //paid landline from bob Provider
                $BillPayOgero = $bobServices->BillPayOgero($Landline_With_id);
                if ($BillPayOgero[0]) {
                    //if payment from Bob provider success insert landline data to db
                    $landline = new Landline;
                    $landline
                        ->setsuyoolUserId($suyoolUserId)
                        ->setgsmNumber($Landline_With_id->getgsmNumber())
                        ->settransactionId($Landline_With_id->gettransactionId())
                        ->settransactionDescription($BillPayOgero[1]["TransactionDescription"])
                        ->setreferenceNumber($BillPayOgero[1]["ReferenceNumber"])
                        ->setogeroBills($Landline_With_id->getogeroBills())
                        ->setogeroPenalty($Landline_With_id->getogeroPenalty())
                        ->setogeroInitiationDate($Landline_With_id->getogeroInitiationDate())
                        ->setogeroClientName($Landline_With_id->getogeroClientName())
                        ->setogeroAddress($Landline_With_id->getogeroAddress())
                        ->setdisplayedFees($Landline_With_id->getdisplayedFees())
                        ->setcurrency($Landline_With_id->getcurrency())
                        ->setamount($Landline_With_id->getamount())
                        ->setamount1($Landline_With_id->getamount1())
                        ->setamount2($Landline_With_id->getamount2())
                        ->settotalAmount($Landline_With_id->gettotalAmount())
                        ->setogeroTotalAmount($Landline_With_id->getogeroTotalAmount())
                        ->setogeroFees($Landline_With_id->getogeroFees())
                        ->setadditionalFees($Landline_With_id->getadditionalFees())
                        ->setfees($Landline_With_id->getfees())
                        ->setfees1($Landline_With_id->getfees1())
                        ->setrounding($Landline_With_id->getrounding());
                    $this->mr->persist($landline);
                    $this->mr->flush();

                    $IsSuccess = true;

                    $landlineId = $landline->getId();
                    $landline = $this->mr->getRepository(Landline::class)->findOneBy(['id' => $landlineId]);

                    //update order by passing prepaidId to order and set status to purshased
                    $orderupdate = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $suyoolUserId, 'status' => Order::$statusOrder['HELD']]);
                    $orderupdate
                        ->setlandlineId($landline)
                        ->setstatus(Order::$statusOrder['PURCHASED']);
                    $this->mr->persist($orderupdate);
                    $this->mr->flush();

                    //intial notification
                    $params = json_encode([
                        'amount' => $order->getamount(),
                        'currency' => "L.L",
                        'mobilenumber' => $Landline_With_id->getGsmNumber(),
                    ]);
                    $additionalData = "";

                    $content = $notificationServices->getContent('AcceptedOgeroPayment');
                    $bulk = 0; //1 for broadcast 0 for unicast
                    $notificationServices->addNotification($suyoolUserId, $content, $params, $bulk, $additionalData);

                    $updateUtilitiesAdditionalData = json_encode([
                        'OgeroPenalty' => $Landline_With_id->getogeroPenalty(),
                        'Amount' => $Landline_With_id->getamount(),
                        'OgeroFees' => $Landline_With_id->getogeroFees(),
                        'OgeroInitiationDate' => $Landline_With_id->getogeroInitiationDate(),
                        'Amount1' => $Landline_With_id->getamount1(),
                        'Amount2' => $Landline_With_id->getamount2(),
                        'OgeroAddress' => $Landline_With_id->getogeroAddress(),
                        'TransactionId' => $Landline_With_id->gettransactionId(),
                        'Fees' => $Landline_With_id->getfees(),
                        'OgeroBills' => $Landline_With_id->getogeroBills(),
                        'Fees1' => $Landline_With_id->getfees1(),
                        'OgeroClientName' => $Landline_With_id->getogeroClientName(),
                        'TotalAmount' => $Landline_With_id->gettotalAmount(),
                        'Currency' => $Landline_With_id->getcurrency(),
                        'Rounding' => $Landline_With_id->getrounding(),
                        'OgeroTotalAmount' => $Landline_With_id->getogeroTotalAmount(),
                        'AdditionalFees' => $Landline_With_id->getadditionalFees(),
                    ]);

                    //tell the .net that total amount is paid
                    $responseUpdateUtilities = $suyoolServices->UpdateUtilities($order->getamount(), $updateUtilitiesAdditionalData, $orderupdate->gettransId());
                    if ($responseUpdateUtilities[0]) {
                        $orderupdate5 = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $suyoolUserId, 'status' => Order::$statusOrder['PURCHASED']]);

                        //update te status from purshased to completed
                        $orderupdate5
                            ->setstatus(Order::$statusOrder['COMPLETED'])
                            ->seterror($responseUpdateUtilities[1]);
                        $this->mr->persist($orderupdate5);
                        $this->mr->flush();

                        $dataPayResponse = ['amount' => $order->getamount(), 'currency' => $order->getcurrency(), 'fees' => 0];
                        $message = "Success";
                    } else {
                        $orderupdate5 = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $suyoolUserId, 'status' => Order::$statusOrder['PURCHASED']]);
                        $orderupdate5
                            ->setstatus(Order::$statusOrder['CANCELED'])
                            ->seterror($responseUpdateUtilities[1]);
                        $message = "something wrong while UpdateUtilities";
                        $dataPayResponse = -1;
                    }
                } else {
                    $logs = new Logs;
                    $logs->setidentifier("ogero error");
                    $logs->seterror($BillPayOgero[2]);

                    $this->mr->persist($logs);
                    $this->mr->flush();

                    $IsSuccess = false;
                    $dataPayResponse = -1;
                    //if not purchase return money
                    $responseUpdateUtilities = $suyoolServices->UpdateUtilities(0.0, "", $orderupdate1->gettransId());
                    if ($responseUpdateUtilities[0]) {
                        $orderupdate4 = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $suyoolUserId, 'status' => Order::$statusOrder['HELD']]);
                        $orderupdate4
                            ->setstatus(Order::$statusOrder['CANCELED'])
                            ->seterror("reversed");
                        $this->mr->persist($orderupdate4);
                        $this->mr->flush();

                        $message = "Success return money!!";
                    } else {
                        $logs = new Logs;
                        $logs->setidentifier("Update Utility error");

                        if (isset($responseUpdateUtilities[1])) {
                            $logs->seterror($responseUpdateUtilities[1]);
                        } else {
                            $logs->seterror(null);
                        }

                        $this->mr->persist($logs);
                        $this->mr->flush();
                        $message = "Can not return money!!";
                    }
                }
            } else {
                //if can not take money from .net cancel the state of the order
                $orderupdate3 = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $suyoolUserId, 'status' => Order::$statusOrder['PENDING']]);
                $orderupdate3
                    ->setstatus(Order::$statusOrder['CANCELED'])
                    ->setamount($order->getamount())
                    ->setcurrency($this->params->get('CURRENCY_LBP'))
                    ->seterror($response[3]);
                $this->mr->persist($orderupdate3);
                $this->mr->flush();
                $IsSuccess = false;
                $message = json_decode($response[1], true);
                if (isset($response[2])) {
                    $flagCode = $response[2];
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

    /**
     * @Route("api/ogero", name="ap1_ogero_bill_pay",methods="GET")
     */
    public function checkWebkey(Request $request, BobServices $bobServices, NotificationServices $notificationServices)
    {
        $data = json_decode($request->getContent(), true);
        $webkey = apache_request_headers();
        $webkey = $webkey['Authorization'];
        $webkeyDecrypted = SuyoolServices::decryptWebKey($webkey);

        if ($notificationServices->checkUser($webkeyDecrypted['merchantId'], $webkeyDecrypted['lang']) &&  $webkeyDecrypted['devicesType'] == "CORPORATE") {
            return new JsonResponse([
                'status' => true
            ]);
        } else {
            return new JsonResponse([
                'status' => false
            ]);
        }
    }

    /**
     * @Route("/api/ogero/landline", name="ap2_ogero_bill", methods="POST")
     */
    public function billApi(Request $request, BobServices $bobServices, NotificationServices $notificationServices)
    {
        try {
            $data = json_decode($request->getContent(), true);
            $webkey = apache_request_headers();
            $webkey = $webkey['Authorization'];
            $webkeyDecrypted = SuyoolServices::decryptWebKey($webkey);

            if ($notificationServices->checkUser($webkeyDecrypted['merchantId'], $webkeyDecrypted['lang']) &&  $webkeyDecrypted['devicesType'] == "CORPORATE") {
                $suyoolUserId = $webkeyDecrypted['merchantId'];
                $displayedFees = 0;

                if ($data != null) {
                    $RetrieveChannel = $bobServices->RetrieveChannelResults($data["mobileNumber"]);
                    // dd($RetrieveChannel);
                    if ($RetrieveChannel[0] == true) {
                        $resp = $RetrieveChannel[1]["Values"];
                        $displayedFees = intval($resp["Fees"]) + intval($resp["Fees1"]) + intval($resp["AdditionalFees"]) + intval($resp["OgeroFees"]);

                        $LandlineReq = new LandlineRequest;
                        $LandlineReq
                            ->setsuyoolUserId($suyoolUserId)
                            ->setgsmNumber($data["mobileNumber"])
                            ->setresponse($RetrieveChannel[3])
                            ->seterrordesc($RetrieveChannel[2])
                            ->settransactionId($resp["TransactionId"])
                            ->setogeroBills(json_encode($resp["OgeroBills"]))
                            ->setogeroPenalty($resp["OgeroPenalty"])
                            ->setogeroInitiationDate($resp["OgeroInitiationDate"])
                            ->setogeroClientName($resp["OgeroClientName"])
                            ->setogeroAddress($resp["OgeroAddress"])
                            ->setcurrency($resp["Currency"])
                            ->setamount($resp["Amount"])
                            ->setamount1($resp["Amount1"])
                            ->setamount2($resp["Amount2"])
                            ->settotalAmount($resp["TotalAmount"])
                            ->setogeroTotalAmount($resp["OgeroTotalAmount"])
                            ->setogeroFees($resp["OgeroFees"])
                            ->setadditionalFees($resp["AdditionalFees"])
                            ->setfees($resp["Fees"])
                            ->setfees1($resp["Fees1"])
                            ->setdisplayedFees($displayedFees)
                            ->setrounding($resp["Rounding"]);
                        $this->mr->persist($LandlineReq);
                        $this->mr->flush();

                        $LandlineReqId = $LandlineReq->getId();
                        $messageBack = "Success";
                        $message = $resp;
                        $mobileNb = $data["mobileNumber"];
                    } else {
                        $LandlineReq = new LandlineRequest;
                        $LandlineReq
                            ->setsuyoolUserId($suyoolUserId)
                            ->setgsmNumber($data["mobileNumber"])
                            ->setresponse($RetrieveChannel[3])
                            ->seterrordesc($RetrieveChannel[2]);
                        $this->mr->persist($LandlineReq);
                        $this->mr->flush();
                        $error = explode("-", $RetrieveChannel[2]);
                        $errorcode = $error[1];
                        // if($errorcode == 113){
                        //     dd("ok");
                        // }
                        $messageBack = $RetrieveChannel[2];
                        $LandlineReqId = -1;
                        $mobileNb = $data["mobileNumber"];
                    }
                } else {
                    $message = "not connected";
                    $LandlineReqId = -1;
                    $mobileNb = -1;
                }

                return new JsonResponse([
                    'status' => true,
                    'message' => @$messageBack,
                    'data' => [
                        'mobileNb' => $mobileNb,
                        'displayBill' => $message,
                        'LandlineReqId' => $LandlineReqId,
                        'displayedFees' => $displayedFees,
                    ]
                ], 200);
            } else {
                return new JsonResponse([
                    'status' => false,
                    'message' => 'Unauthorize'
                ], 401);
            }
        } catch (Exception $e) {
            return new JsonResponse([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @Route("/api/ogero/landline/pay", name="ap3_ogero_bill", methods="POST")
     */
    public function billPayApi(Request $request, BobServices $bobServices, NotificationServices $notificationServices)
    {
        $data = json_decode($request->getContent(), true);
        $webkey = apache_request_headers();
        $webkey = $webkey['Authorization'];
        $webkeyDecrypted = SuyoolServices::decryptWebKey($webkey);

        if ($notificationServices->checkUser($webkeyDecrypted['merchantId'], $webkeyDecrypted['lang']) &&  $webkeyDecrypted['devicesType'] == "CORPORATE") {
            $suyoolServices = new SuyoolServices($this->params->get('OGERO_MERCHANT_ID'));
            $suyoolUserId = $webkeyDecrypted['merchantId'];
            $Landline_With_id = $this->mr->getRepository(LandlineRequest::class)->findOneBy(['id' => $data["ResponseId"]]);
            $flagCode = null;

            if ($data != null) {
                //Initial order with status pending
                $order = new Order;
                $order
                    ->setsuyoolUserId($suyoolUserId)
                    ->settransId(null)
                    ->setlandlineId(null)
                    ->setstatus(Order::$statusOrder['PENDING'])
                    ->setamount($Landline_With_id->getamount() + $Landline_With_id->getfees())
                    ->setfees($Landline_With_id->getfees())
                    ->setcurrency("LBP");
                $this->mr->persist($order);
                $this->mr->flush();

                $orderTst = $this->params->get('OGERO_MERCHANT_ID') . "-" . $order->getId();
                //Take amount from .net
                $response = $suyoolServices->PushUtilities($suyoolUserId, $orderTst, $order->getamount(), $this->params->get('CURRENCY_LBP'), $Landline_With_id->getfees());

                if ($response[0]) {
                    //set order status to held
                    $orderupdate1 = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $suyoolUserId, 'status' => Order::$statusOrder['PENDING']]);
                    $orderupdate1
                        ->settransId($response[1])
                        ->setstatus(Order::$statusOrder['HELD']);
                    $this->mr->persist($orderupdate1);
                    $this->mr->flush();

                    //paid landline from bob Provider
                    $BillPayOgero = $bobServices->BillPayOgero($Landline_With_id);
                    if ($BillPayOgero[0]) {
                        //if payment from Bob provider success insert landline data to db
                        $landline = new Landline;
                        $landline
                            ->setsuyoolUserId($suyoolUserId)
                            ->setgsmNumber($Landline_With_id->getgsmNumber())
                            ->settransactionId($Landline_With_id->gettransactionId())
                            ->settransactionDescription($BillPayOgero[1]["TransactionDescription"])
                            ->setreferenceNumber($BillPayOgero[1]["ReferenceNumber"])
                            ->setogeroBills($Landline_With_id->getogeroBills())
                            ->setogeroPenalty($Landline_With_id->getogeroPenalty())
                            ->setogeroInitiationDate($Landline_With_id->getogeroInitiationDate())
                            ->setogeroClientName($Landline_With_id->getogeroClientName())
                            ->setogeroAddress($Landline_With_id->getogeroAddress())
                            ->setdisplayedFees($Landline_With_id->getdisplayedFees())
                            ->setcurrency($Landline_With_id->getcurrency())
                            ->setamount($Landline_With_id->getamount())
                            ->setamount1($Landline_With_id->getamount1())
                            ->setamount2($Landline_With_id->getamount2())
                            ->settotalAmount($Landline_With_id->gettotalAmount())
                            ->setogeroTotalAmount($Landline_With_id->getogeroTotalAmount())
                            ->setogeroFees($Landline_With_id->getogeroFees())
                            ->setadditionalFees($Landline_With_id->getadditionalFees())
                            ->setfees($Landline_With_id->getfees())
                            ->setfees1($Landline_With_id->getfees1())
                            ->setrounding($Landline_With_id->getrounding());
                        $this->mr->persist($landline);
                        $this->mr->flush();

                        $IsSuccess = true;

                        $landlineId = $landline->getId();
                        $landline = $this->mr->getRepository(Landline::class)->findOneBy(['id' => $landlineId]);

                        //update order by passing prepaidId to order and set status to purshased
                        $orderupdate = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $suyoolUserId, 'status' => Order::$statusOrder['HELD']]);
                        $orderupdate
                            ->setlandlineId($landline)
                            ->setstatus(Order::$statusOrder['PURCHASED']);
                        $this->mr->persist($orderupdate);
                        $this->mr->flush();

                        //intial notification
                        $params = json_encode([
                            'amount' => $order->getamount(),
                            'currency' => "L.L",
                            'mobilenumber' => $Landline_With_id->getGsmNumber(),
                        ]);
                        $additionalData = "";

                        $content = $notificationServices->getContent('AcceptedOgeroPayment');
                        $bulk = 0; //1 for broadcast 0 for unicast
                        $notificationServices->addNotification($suyoolUserId, $content, $params, $bulk, $additionalData);

                        $updateUtilitiesAdditionalData = json_encode([
                            'OgeroPenalty' => $Landline_With_id->getogeroPenalty(),
                            'Amount' => $Landline_With_id->getamount(),
                            'OgeroFees' => $Landline_With_id->getogeroFees(),
                            'OgeroInitiationDate' => $Landline_With_id->getogeroInitiationDate(),
                            'Amount1' => $Landline_With_id->getamount1(),
                            'Amount2' => $Landline_With_id->getamount2(),
                            'OgeroAddress' => $Landline_With_id->getogeroAddress(),
                            'TransactionId' => $Landline_With_id->gettransactionId(),
                            'Fees' => $Landline_With_id->getfees(),
                            'OgeroBills' => $Landline_With_id->getogeroBills(),
                            'Fees1' => $Landline_With_id->getfees1(),
                            'OgeroClientName' => $Landline_With_id->getogeroClientName(),
                            'TotalAmount' => $Landline_With_id->gettotalAmount(),
                            'Currency' => $Landline_With_id->getcurrency(),
                            'Rounding' => $Landline_With_id->getrounding(),
                            'OgeroTotalAmount' => $Landline_With_id->getogeroTotalAmount(),
                            'AdditionalFees' => $Landline_With_id->getadditionalFees(),
                        ]);

                        //tell the .net that total amount is paid
                        $responseUpdateUtilities = $suyoolServices->UpdateUtilities($order->getamount(), $updateUtilitiesAdditionalData, $orderupdate->gettransId());
                        if ($responseUpdateUtilities[0]) {
                            $orderupdate5 = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $suyoolUserId, 'status' => Order::$statusOrder['PURCHASED']]);

                            //update te status from purshased to completed
                            $orderupdate5
                                ->setstatus(Order::$statusOrder['COMPLETED'])
                                ->seterror($responseUpdateUtilities[1]);
                            $this->mr->persist($orderupdate5);
                            $this->mr->flush();

                            $popup = [
                                "Title" => "Ogero Bill Paid Successfully",
                                "globalCode" => 0,
                                "flagCode" => 0,
                                "Message" => "You have successfully paid your Ogero bill of L.L " . number_format($order->getamount()) . ".",
                                "isPopup" => true
                            ];
                            $message = "Success";
                            $messageBack = "Success";
                        } else {
                            $orderupdate5 = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $suyoolUserId, 'status' => Order::$statusOrder['PURCHASED']]);
                            $orderupdate5
                                ->setstatus(Order::$statusOrder['CANCELED'])
                                ->seterror($responseUpdateUtilities[1]);
                            $message = "something wrong while UpdateUtilities";
                            $dataPayResponse = -1;
                        }
                    } else {
                        $logs = new Logs;
                        $logs->setidentifier("ogero error");
                        $logs->seterror($BillPayOgero[2]);

                        $this->mr->persist($logs);
                        $this->mr->flush();

                        $IsSuccess = false;
                        $dataPayResponse = -1;
                        //if not purchase return money
                        $responseUpdateUtilities = $suyoolServices->UpdateUtilities(0.0, "", $orderupdate1->gettransId());
                        if ($responseUpdateUtilities[0]) {
                            $orderupdate4 = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $suyoolUserId, 'status' => Order::$statusOrder['HELD']]);
                            $orderupdate4
                                ->setstatus(Order::$statusOrder['CANCELED'])
                                ->seterror("reversed");
                            $this->mr->persist($orderupdate4);
                            $this->mr->flush();

                            $messageBack = "Success return money!!";
                            $message = "Success return money!!";
                        } else {
                            $logs = new Logs;
                            $logs->setidentifier("Update Utility error");

                            if (isset($responseUpdateUtilities[1])) {
                                $logs->seterror($responseUpdateUtilities[1]);
                            } else {
                                $logs->seterror(null);
                            }

                            $this->mr->persist($logs);
                            $this->mr->flush();
                            $messageBack = "Can not return money!!";
                            $message = "Can not return money!!";
                        }
                    }
                } else {
                    //if can not take money from .net cancel the state of the order
                    $orderupdate3 = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $suyoolUserId, 'status' => Order::$statusOrder['PENDING']]);
                    $orderupdate3
                        ->setstatus(Order::$statusOrder['CANCELED'])
                        ->setamount($order->getamount())
                        ->setcurrency($this->params->get('CURRENCY_LBP'))
                        ->seterror($response[3]);
                    $this->mr->persist($orderupdate3);
                    $this->mr->flush();
                    $IsSuccess = false;
                    $message = json_decode($response[1], true);
                    $popup = [
                        "Title" => @$message['Title'],
                        "globalCode" => 0,
                        "flagCode" => @$message['ButtonOne']['Flag'],
                        "Message" => @$message['SubTitle'],
                        "isPopup" => true
                    ];
                    if (isset($response[2])) {
                        $flagCode = $response[2];
                    }
                    $dataPayResponse = -1;
                    $messageBack = $response[3];
                }
            } else {
                $IsSuccess = false;
                $dataPayResponse = -1;
                $messageBack = "Can not retrive data !!";
                $message = "Can not retrive data !!";
            }

            $parameters = [
                'message' => $message,
                'IsSuccess' => $IsSuccess,
                'flagCode' => $flagCode,
                'Popup' => @$popup,
            ];
            return new JsonResponse([
                'status' => true,
                'message' => @$messageBack,
                'data' => $parameters
            ], 200);
        } else {
            return new JsonResponse([
                'status' => false,
                'message' => "Unauthorize"
            ], 401);
        }
    }
}
