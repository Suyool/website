<?php

namespace App\Controller;

use App\Entity\Alfa\Logs;
use App\Entity\Alfa\Order;
use App\Entity\Alfa\Postpaid;
use App\Entity\Alfa\Prepaid;
use App\Entity\Alfa\PostpaidRequest;
use App\Entity\Notification\Users;
use App\Service\LotoServices;
use App\Service\BobServices;
use App\Service\Memcached;
use App\Service\NotificationServices;
use App\Service\SuyoolServices;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Psr\Log\LoggerInterface;
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
    private $params;
    public $cipher_algorithme = "AES128";
    public $key = "SY1X24elh9eG3fpOaHcWlQ9h2bHaqimdIDoyoOaFoi0rukAj3Z";
    public $iv = "fgu26y9e43wc8dj2"; //initiallization vector for decrypt
    private $session;
    private $lotoServices;
    private $Memcached;
    private $not;
    private $loggerInterface;

    public function __construct(ManagerRegistry $mr, $certificate, $hash_algo, ParameterBagInterface $params, SessionInterface $sessionInterface, LotoServices $lotoServices, Memcached $memcached, LoggerInterface $loggerInterface)
    {
        $this->mr = $mr->getManager('alfa');
        $this->not = $mr->getManager('notification');
        $this->params = $params;
        $this->session = $sessionInterface;
        $this->lotoServices = $lotoServices;
        $this->Memcached = $memcached;
        $this->loggerInterface = $loggerInterface;
    }

    /**
     * @Route("/alfa", name="app_alfa")
     */
    public function index(NotificationServices $notificationServices)
    {
        $useragent = $_SERVER['HTTP_USER_AGENT'];
        // $_POST['infoString']="3mzsXlDm5DFUnNVXA5Pu8T1d5nNACEsiiUEAo7TteE/x3BGT3Oy3yCcjUHjAVYk3";

        if (isset($_POST['infoString'])) {
            $decrypted_string = SuyoolServices::decrypt($_POST['infoString']); //['device'=>"aad", asdfsd]
            $suyoolUserInfo = explode("!#!", $decrypted_string);
            $devicetype = stripos($useragent, $suyoolUserInfo[1]);

            if ($notificationServices->checkUser($suyoolUserInfo[0], $suyoolUserInfo[2]) && $devicetype) {
                $SuyoolUserId = $suyoolUserInfo[0];
                $this->session->set('suyoolUserId', $SuyoolUserId);
                // $this->session->set('suyoolUserId', 155);

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
        if ($data != null) {
            $sendBill = $bobServices->Bill($data["mobileNumber"]);
            $sendBillRes = json_decode($sendBill, true);
            if (isset($sendBillRes["ResponseText"])) {
                if (isset($sendBillRes["ResponseText"]) && $sendBillRes["ResponseText"] == "Success") {
                    $invoices = new PostpaidRequest;
                    $invoices
                        ->setSuyoolUserId($SuyoolUserId)
                        ->setGsmNumber($data["mobileNumber"]);
                    $this->mr->persist($invoices);
                    $this->mr->flush();

                    $invoicesId = $invoices->getId();
                    $message = "connected";
                } else {
                    $postpaidrequest = new PostpaidRequest;
                    $postpaidrequest
                        ->setSuyoolUserId($SuyoolUserId)
                        ->setGsmNumber($data["mobileNumber"])
                        ->seterror(@$sendBillRes["ResponseText"]);

                    $this->mr->persist($postpaidrequest);
                    $this->mr->flush();
                    // echo "error";
                    $invoicesId = -1;
                    $message = $sendBillRes["ResponseText"];
                }
            } else {
                // $sendBill = "Internal Error";
                $postpaidrequest = new PostpaidRequest;
                $postpaidrequest
                    ->setSuyoolUserId($SuyoolUserId)
                    ->setGsmNumber($data["mobileNumber"])
                    ->seterror(@$sendBill);
                $this->mr->persist($postpaidrequest);
                $this->mr->flush();
                $message = @$sendBill;
                $invoicesId = -1;
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

        if ($data != null) {
            $retrieveResults = $bobServices->RetrieveResults($data["currency"], $data["mobileNumber"], $data["Pin"]);
            if (isset($retrieveResults) && $retrieveResults[0]) {
                $jsonResult = json_decode($retrieveResults[1], true);
                $displayData = $jsonResult["Values"];

                $Pin = implode("", $data["Pin"]);
                $invoicesId = $data["invoicesId"];
                $displayedFees = intval($jsonResult["Values"]["Fees"]) + intval($jsonResult["Values"]["Fees1"]) + intval($jsonResult["Values"]["AdditionalFees"]);

                $invoices =  $this->mr->getRepository(PostpaidRequest::class)->findOneBy(['id' => $invoicesId]);
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
                    ->setTransactionId($jsonResult["Values"]["TransactionId"])
                    ->seterrordesc($retrieveResults[2])
                    ->seterrorcode($retrieveResults[3])
                    ->setresponse($retrieveResults[4]);

                $this->mr->persist($invoices);
                $this->mr->flush();

                $invoicesId = $invoices->getId();
                $message = "connected";
            } else {
                $invoicesId = $data["invoicesId"];
                $invoices =  $this->mr->getRepository(PostpaidRequest::class)->findOneBy(['id' => $invoicesId]);
                $invoices
                    ->seterrordesc($retrieveResults[1])
                    ->seterrorcode($retrieveResults[2])
                    ->setresponse($retrieveResults[3]);

                $this->mr->persist($invoices);
                $this->mr->flush();

                $displayData = -1;
                $message = $retrieveResults[2];
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
                ->setamount($Postpaid_With_id->getamount() + $Postpaid_With_id->getdisplayedFees())
                ->setfees($Postpaid_With_id->getdisplayedFees())
                ->setcurrency("LBP");
            $this->mr->persist($order);
            $this->mr->flush();

            $order_id = $this->params->get('ALFA_POSTPAID_MERCHANT_ID') . "-" . $order->getId();

            //Take amount from .net
            $response = $suyoolServices->PushUtilities($SuyoolUserId, $order_id, $order->getamount(), $this->params->get('CURRENCY_LBP'), $order->getfees());

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
                if ($billPay[0] != "") {
                    $billPayArray = json_decode($billPay[0], true);
                    //if payment from loto provider success insert prepaid data to db
                    $postpaid = new Postpaid;
                    $postpaid
                        ->settransactionDescription($billPayArray["TransactionDescription"])
                        ->setstatus(Order::$statusOrder['COMPLETED'])
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
                        'currency' => "L.L",
                        'mobilenumber' => $Postpaid_With_id->getGsmNumber(),
                    ]);
                    $additionalData = "";

                    $content = $notificationServices->getContent('AcceptedAlfaPayment');
                    $bulk = 0; //1 for broadcast 0 for unicast
                    $notificationServices->addNotification($SuyoolUserId, $content, $params, $bulk, $additionalData);

                    $updateUtilitiesAdditionalData = json_encode([
                        'Fees' => $Postpaid_With_id->getfees(),
                        'TransactionId' => $Postpaid_With_id->getTransactionId(),
                        'Amount' => $Postpaid_With_id->getamount(),
                        'TotalAmount' => $Postpaid_With_id->gettotalamount(),
                        'Currency' => $Postpaid_With_id->getcurrency(),
                    ]);

                    //tell the .net that total amount is paid
                    $responseUpdateUtilities = $suyoolServices->UpdateUtilities($order->getamount(),  $updateUtilitiesAdditionalData, $orderupdate->gettransId());
                    if ($responseUpdateUtilities) {
                        $orderupdate5 = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $SuyoolUserId, 'status' => Order::$statusOrder['PURCHASED']]);
                        //update te status from purshased to completed
                        $orderupdate5
                            ->setstatus(Order::$statusOrder['COMPLETED'])
                            ->seterror("SUCCESS");
                        $this->mr->persist($orderupdate5);
                        $this->mr->flush();

                        $dataPayResponse = ['amount' => $order->getamount(), 'currency' => $order->getcurrency(), 'fees' => 0];
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
                            ->setstatus(Order::$statusOrder['CANCELED'])
                            ->seterror("reversed error from alfa:" . $billPay[2]);;

                        $this->mr->persist($orderupdate4);
                        $this->mr->flush();

                        $message = "Success return money!!";
                    } else {
                        $orderupdate4 = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $SuyoolUserId, 'status' => Order::$statusOrder['HELD']]);
                        $orderupdate4
                            ->setstatus(Order::$statusOrder['CANCELED'])
                            ->seterror($responseUpdateUtilities[1] . " " . $billPay[2]);
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
        if ($_ENV['APP_ENV'] == "prod") {
            $filter =  $Memcached->getVouchers($lotoServices);
        } else {
            // $filter =  $Memcached->getVouchers($lotoServices);
        }
        // dd($filter);

        return new JsonResponse([
            'status' => true,
            'message' => $filter
        ], 200);
    }

    public function getVoucherPriceByTypeAlfa($type)
    {
        $filterAlfa = $this->Memcached->getVouchers($this->lotoServices);
        // dd($filterAlfa);
        $priceToPush = 0;
        foreach ($filterAlfa as $filterAlfa) {
            if ($filterAlfa['vouchertype'] == $type) {
                $priceToPush = $filterAlfa['priceLBP'];
            }
        }

        return $priceToPush;
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

        if ($data != null) {
            $price = $this->getVoucherPriceByTypeAlfa($data["type"]);
            $cardsPerDay = $this->mr->getRepository(Order::class)->purchaseCardsPerDay($SuyoolUserId,$data["type"]);
            // dd($cardsPerDay);
            if (!is_null($cardsPerDay) && $cardsPerDay['numberofcompletedordersprepaid'] >= $this->params->get('CARDS_PER_DAY_PREPAID')) {
                return new JsonResponse([
                    'status' => true,
                    'IsSuccess' => false,
                    'flagCode' => 210,
                    'Title' => 'Daily Limit Exceeded',
                    'message' => "In our effort to accommodate all our Suyoolers fairly, we are temporarily limiting the purchase to 2 recharge cards per type per day.<br>We plan to remove this limitation as soon as soon as possible."
                ], 200);
            }
            //Initial order with status pending
            $order = new Order;
            $order
                ->setsuyoolUserId($SuyoolUserId)
                ->settransId(null)
                ->setpostpaidId(null)
                ->setprepaidId(null)
                ->setstatus(Order::$statusOrder['PENDING'])
                ->setamount($price)
                ->setfees(0)
                ->setVoucherTypeId($data["type"])
                ->setcurrency("LBP");
            $this->mr->persist($order);
            $this->mr->flush();

            $order_id = $this->params->get('ALFA_PREPAID_MERCHANT_ID') . "-" . $order->getId();

            //Take amount from .net
            $response = $suyoolServices->PushUtilities($SuyoolUserId, $order_id, $order->getamount(), $order->getcurrency(), 0);
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
                if ($BuyPrePaid[0] == false) {
                    $message = $BuyPrePaid[1];
                    $responseUpdateUtilities = $suyoolServices->UpdateUtilities(0, "", $orderupdate1->gettransId());
                    if ($responseUpdateUtilities[0]) {
                        $orderupdate4 = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $SuyoolUserId, 'status' => Order::$statusOrder['HELD']]);
                        $orderupdate4
                            ->setstatus(Order::$statusOrder['CANCELED'])
                            ->seterror("{reversed " . $message . "}");
                        $this->mr->persist($orderupdate4);
                        $this->mr->flush();
                    } else {
                        $orderupdate4 = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $SuyoolUserId, 'status' => Order::$statusOrder['HELD']]);
                        $orderupdate4
                            ->setstatus(Order::$statusOrder['CANCELED'])
                            ->seterror($responseUpdateUtilities[1]);
                    }
                    $IsSuccess = false;
                    $dataPayResponse = [];
                } else {
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
                        $message = $PayResonse["errorinfo"]["errorcode"];

                        //if not purchase return money
                        $responseUpdateUtilities = $suyoolServices->UpdateUtilities(0, "", $orderupdate1->gettransId());
                        if ($responseUpdateUtilities[0]) {
                            $orderupdate4 = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $SuyoolUserId, 'status' => Order::$statusOrder['HELD']]);
                            $orderupdate4
                                ->setstatus(Order::$statusOrder['CANCELED'])
                                ->seterror("{reversed " . $message . "}");
                            $this->mr->persist($orderupdate4);
                            $this->mr->flush();
                        } else {
                            $orderupdate4 = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $SuyoolUserId, 'status' => Order::$statusOrder['HELD']]);
                            $orderupdate4
                                ->setstatus(Order::$statusOrder['CANCELED'])
                                ->seterror($responseUpdateUtilities[1]);
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

                        $dateString = $PayResonse["voucherExpiry"];
                        $dateTime = new DateTime($dateString);

                        $formattedDate = $dateTime->format('d/m/Y');

                        //intial notification
                        $params = json_encode([
                            'amount' => $order->getamount(),
                            'currency' => "L.L",
                            'plan' => $data["desc"],
                            'code' => $PayResonse["voucherCode"],
                            'serial' => $PayResonse["voucherSerial"],
                            'expiry' => $formattedDate
                        ]);
                        $additionalData = "*14*" . $PayResonse["voucherCode"] . "#";
                        $content = $notificationServices->getContent('AlfaCardPurchasedSuccessfully');
                        $bulk = 0; //1 for broadcast 0 for unicast
                        $notificationServices->addNotification($SuyoolUserId, $content, $params, $bulk, $additionalData);

                        //tell the .net that total amount is paid
                        $responseUpdateUtilities = $suyoolServices->UpdateUtilities($order->getamount(), "", $orderupdate->gettransId());
                        if ($responseUpdateUtilities[0]) {
                            $orderupdate5 = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $SuyoolUserId, 'status' => Order::$statusOrder['PURCHASED']]);
                            //update te status from purshased to completed
                            $orderupdate5
                                ->setstatus(Order::$statusOrder['COMPLETED'])
                                ->seterror($responseUpdateUtilities[1]);
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
                }
            } else {
                //if can not take money from .net cancel the state of the order
                $orderupdate3 = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $SuyoolUserId, 'status' => Order::$statusOrder['PENDING']]);
                $orderupdate3
                    ->setstatus(Order::$statusOrder['CANCELED'])
                    ->seterror($response[3]);
                $this->mr->persist($orderupdate3);
                $this->mr->flush();
                $IsSuccess = false;

                if (isset($response[2])) {
                    $message = json_decode($response[1], true);
                    $flagCode = $response[2];
                } else {
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

    /**
     * @Route("api/alfa", name="ap1_alfa_bill_pay",methods="GET")
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
     * @Route("/api/alfa/bill", name="ap2_alfa_bill", methods="POST")
     */
    public function RestAlfaBillApi(Request $request, BobServices $bobServices, NotificationServices $notificationServices)
    {
        try {
            $data = json_decode($request->getContent(), true);
            $webkey = apache_request_headers();
            $webkey = $webkey['Authorization'];
            $webkeyDecrypted = SuyoolServices::decryptWebKey($webkey);

            if ($notificationServices->checkUser($webkeyDecrypted['merchantId'], $webkeyDecrypted['lang']) &&  $webkeyDecrypted['devicesType'] == "CORPORATE") {
                $SuyoolUserId = $webkeyDecrypted['merchantId'];
                if ($data != null) {
                    $sendBill = $bobServices->Bill($data["mobileNumber"]);
                    $sendBillRes = json_decode($sendBill, true);
                    if (isset($sendBillRes["ResponseText"])) {
                        if (isset($sendBillRes["ResponseText"]) && $sendBillRes["ResponseText"] == "Success") {
                            $invoices = new PostpaidRequest;
                            $invoices
                                ->setSuyoolUserId($SuyoolUserId)
                                ->setGsmNumber($data["mobileNumber"]);
                            $this->mr->persist($invoices);
                            $this->mr->flush();

                            $invoicesId = $invoices->getId();
                            $message = "connected";
                        } else {
                            $postpaidrequest = new PostpaidRequest;
                            $postpaidrequest
                                ->setSuyoolUserId($SuyoolUserId)
                                ->setGsmNumber($data["mobileNumber"])
                                ->seterror(@$sendBillRes["ResponseText"]);

                            $this->mr->persist($postpaidrequest);
                            $this->mr->flush();
                            $message = $sendBillRes["ResponseText"];
                            switch ($message) {
                                case "Maximum allowed number of PIN requests is reached":
                                    $title = "PIN Tries Exceeded";
                                    $body = "You have exceeded the allowed PIN requests.<br> Kindly try again later";
                                    break;
                                case "Not Enough Balance Amount to be paid":
                                    $title = "No Pending Bill";
                                    $body = "There is no pending bill on the mobile number {$data["mobileNumber"]}<br/>Kindly try again later";
                                    break;
                                case "Internal Error":
                                    $title = "Unable To Pay Your Bill";
                                    $body = "We were unable to process your transaction for the bill payment associated with {$data["mobileNumber"]} .<br>Kindly check your internet connection & try again. ";
                                    break;
                                default:
                                    $title = "Number Not Found";
                                    $body = "The number you entered was not found in the system.<br>Kindly try another number.";
                                    break;
                            }
                            $popup = [
                                "Title" => @$title,
                                "globalCode" => 0,
                                "flagCode" => 800,
                                "Message" => @$body,
                                "isPopup" => true
                            ];
                            // echo "error";
                            $invoicesId = -1;
                        }
                    } else {
                        // $sendBill = "Internal Error";
                        $postpaidrequest = new PostpaidRequest;
                        $postpaidrequest
                            ->setSuyoolUserId($SuyoolUserId)
                            ->setGsmNumber($data["mobileNumber"])
                            ->seterror(@$sendBill);
                        $this->mr->persist($postpaidrequest);
                        $this->mr->flush();
                        $message = @$sendBill;
                        $message = "Not Enough Balance Amount to be paid";
                        switch ($message) {
                            case "Maximum allowed number of PIN requests is reached":
                                $title = "PIN Tries Exceeded";
                                $body = "You have exceeded the allowed PIN requests.<br> Kindly try again later";
                                break;
                            case "Not Enough Balance Amount to be paid":
                                $title = "No Pending Bill";
                                $body = "There is no pending bill on the mobile number {$data["mobileNumber"]}<br/>Kindly try again later";
                                break;
                            case "Internal Error":
                                $title = "Unable To Pay Your Bill";
                                $body = "We were unable to process your transaction for the bill payment associated with {$data["mobileNumber"]} .<br>Kindly check your internet connection & try again. ";
                                break;
                            default:
                                $title = "Number Not Found";
                                $body = "The number you entered was not found in the system.<br>Kindly try another number.";
                                break;
                        }
                        $popup = [
                            "Title" => @$title,
                            "globalCode" => 0,
                            "flagCode" => 800,
                            "Message" => @$body,
                            "isPopup" => true
                        ];
                        $invoicesId = -1;
                    }
                } else {
                    $message = "not connected";
                    $invoicesId = -1;
                }

                return new JsonResponse([
                    'status' => true,
                    'message' => $message,
                    'data' => [
                        'invoiceId' => $invoicesId,
                        'Popup' => @$popup
                    ]
                ], 200);
            } else {
                return new JsonResponse([
                    'status' => false
                ]);
            }
        } catch (Exception $e) {
            $this->loggerInterface->error($e->getMessage());
            return new JsonResponse([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * PostPaid
     * Provider : BOB
     * Desc: Retrieve Channel Results 
     * @Route("/api/alfa/bill/RetrieveResults", name="ap3_alfa_RetrieveResults",methods="POST")
     */
    public function RestAlfaBillApiResults(Request $request, BobServices $bobServices, NotificationServices $notificationServices)
    {
        $data = json_decode($request->getContent(), true);
        $webkey = apache_request_headers();
        $webkey = $webkey['Authorization'];
        $webkeyDecrypted = SuyoolServices::decryptWebKey($webkey);

        if ($notificationServices->checkUser($webkeyDecrypted['merchantId'], $webkeyDecrypted['lang']) &&  $webkeyDecrypted['devicesType'] == "CORPORATE") {
            $displayedFees = 0;
            $SuyoolUserId = $webkeyDecrypted['merchantId'];
            if ($data != null) {
                $retrieveResults = $bobServices->RetrieveResults($data["currency"], $data["mobileNumber"], $data["Pin"]);
                if (isset($retrieveResults) && $retrieveResults[0]) {
                    $jsonResult = json_decode($retrieveResults[1], true);
                    $displayData = $jsonResult["Values"];

                    $Pin = implode("", $data["Pin"]);
                    $invoicesId = $data["invoicesId"];
                    $displayedFees = intval($jsonResult["Values"]["Fees"]) + intval($jsonResult["Values"]["Fees1"]) + intval($jsonResult["Values"]["AdditionalFees"]);

                    $invoices =  $this->mr->getRepository(PostpaidRequest::class)->findOneBy(['id' => $invoicesId]);
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
                        ->setTransactionId($jsonResult["Values"]["TransactionId"])
                        ->seterrordesc($retrieveResults[2])
                        ->seterrorcode($retrieveResults[3])
                        ->setresponse($retrieveResults[4]);

                    $this->mr->persist($invoices);
                    $this->mr->flush();

                    $invoicesId = $invoices->getId();
                    $message = "connected";
                } else {
                    $invoicesId = $data["invoicesId"];
                    $invoices =  $this->mr->getRepository(PostpaidRequest::class)->findOneBy(['id' => $invoicesId]);
                    $invoices
                        ->seterrordesc($retrieveResults[1])
                        ->seterrorcode($retrieveResults[2])
                        ->setresponse($retrieveResults[3]);

                    $this->mr->persist($invoices);
                    $this->mr->flush();

                    $displayData = -1;
                    $message = $retrieveResults[2];
                    switch ($message) {
                        case 213:
                            $title = $retrieveResults[1];
                            $body = $retrieveResults[1] . "<br> Error code: " . $message;
                            break;
                        default:
                            $title = "No Available Bill";
                            $body = "There is no available bill for {$data["mobileNumber"]} at the moment. <br></br>Kindly try again later. ";
                            break;
                    }
                    $popup = [
                        "Title" => @$title,
                        "globalCode" => 0,
                        "flagCode" => 800,
                        "Message" => @$body,
                        "isPopup" => true
                    ];
                    $invoicesId = -1;
                }
            } else {
                $displayData = -1;
                $message = "No data retrived!!";
                $invoicesId = -1;
            }
            $parameters = [
                'postpayed' => $invoicesId,
                'displayData' => $displayData,
                'displayedFees' => $displayedFees,
                'Popup'=>@$popup
            ];
            return new JsonResponse([
                'status' => true,
                'message' => $message,
                'data' => $parameters
            ], 200);
        } else {
            return new JsonResponse([
                'status' => false
            ]);
        }
    }

    /**
     * @Route("/api/alfa/bill/pay", name="ap4_alfa_bill", methods="POST")
     */
    public function apialfapaypost(Request $request, BobServices $bobServices, NotificationServices $notificationServices)
    {
        $data = json_decode($request->getContent(), true);
        $webkey = apache_request_headers();
        $webkey = $webkey['Authorization'];
        $webkeyDecrypted = SuyoolServices::decryptWebKey($webkey);

        if ($notificationServices->checkUser($webkeyDecrypted['merchantId'], $webkeyDecrypted['lang']) &&  $webkeyDecrypted['devicesType'] == "CORPORATE") {
            $suyoolServices = new SuyoolServices($this->params->get('ALFA_POSTPAID_MERCHANT_ID'), null, null, null, $this->loggerInterface);
            $SuyoolUserId = $webkeyDecrypted['merchantId'];
            $suyooler = $this->not->getRepository(Users::class)->findOneBy(['suyoolUserId' => $SuyoolUserId]);
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
                    ->setamount($Postpaid_With_id->getamount() + $Postpaid_With_id->getdisplayedFees())
                    ->setfees($Postpaid_With_id->getdisplayedFees())
                    ->setcurrency("LBP");
                $this->mr->persist($order);
                $this->mr->flush();

                $order_id = $this->params->get('ALFA_POSTPAID_MERCHANT_ID') . "-" . $order->getId();

                //Take amount from .net
                $response = $suyoolServices->PushUtilities($SuyoolUserId, $order_id, $order->getamount(), $this->params->get('CURRENCY_LBP'), $order->getfees());
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
                    if ($billPay[0] != "") {
                        $billPayArray = json_decode($billPay[0], true);
                        //if payment from loto provider success insert prepaid data to db
                        $postpaid = new Postpaid;
                        $postpaid
                            ->settransactionDescription($billPayArray["TransactionDescription"])
                            ->setstatus(Order::$statusOrder['COMPLETED'])
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
                            'currency' => "L.L",
                            'mobilenumber' => $Postpaid_With_id->getGsmNumber(),
                        ]);
                        $additionalData = "";

                        if ($suyooler->getType() == 2) {
                            $content = $notificationServices->getContent('AcceptedAlfaPayment');
                            $bulk = 0; //1 for broadcast 0 for unicast
                            $notificationServices->addNotification($SuyoolUserId, $content, $params, $bulk, $additionalData);
                        } else {
                            $content = $notificationServices->getContent('AcceptedAlfaPayment');
                            $bulk = 1; //1 for broadcast 0 for unicast
                            $notificationServices->addNotification($data["getUsersToReceiveNotification"], $content, $params, $bulk, $additionalData);
                        }
                        $updateUtilitiesAdditionalData = json_encode([
                            'Fees' => $Postpaid_With_id->getfees(),
                            'TransactionId' => $Postpaid_With_id->getTransactionId(),
                            'Amount' => $Postpaid_With_id->getamount(),
                            'TotalAmount' => $Postpaid_With_id->gettotalamount(),
                            'Currency' => $Postpaid_With_id->getcurrency(),
                        ]);

                        //tell the .net that total amount is paid
                        $responseUpdateUtilities = $suyoolServices->UpdateUtilities($order->getamount(),  $updateUtilitiesAdditionalData, $orderupdate->gettransId());
                        if ($responseUpdateUtilities) {
                            $orderupdate5 = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $SuyoolUserId, 'status' => Order::$statusOrder['PURCHASED']]);
                            //update te status from purshased to completed
                            $orderupdate5
                                ->setstatus(Order::$statusOrder['COMPLETED'])
                                ->seterror("SUCCESS");
                            $this->mr->persist($orderupdate5);
                            $this->mr->flush();

                            $popup = [
                                "Title" => "Alfa Bill Paid Successfully",
                                "globalCode" => 0,
                                "flagCode" => 0,
                                "Message" => "You have successfully paid your Alfa bill of L.L " . number_format($order->getamount()) . ".",
                                "isPopup" => true
                            ];
                            $message = "Success";
                            $messageBack = "Success";
                        } else {
                            $orderupdate5 = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $SuyoolUserId, 'status' => Order::$statusOrder['PURCHASED']]);
                            $orderupdate5
                                ->setstatus(Order::$statusOrder['CANCELED'])
                                ->seterror($responseUpdateUtilities[1]);
                            $this->mr->persist($orderupdate5);
                            $this->mr->flush();

                            $messageBack = $responseUpdateUtilities[1];
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
                                ->setstatus(Order::$statusOrder['CANCELED'])
                                ->seterror("reversed error from alfa:" . $billPay[2]);;

                            $this->mr->persist($orderupdate4);
                            $this->mr->flush();

                            $messageBack = "Success return money!!";
                            $message = "Success return money!!";
                        } else {
                            $orderupdate4 = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $SuyoolUserId, 'status' => Order::$statusOrder['HELD']]);
                            $orderupdate4
                                ->setstatus(Order::$statusOrder['CANCELED'])
                                ->seterror($responseUpdateUtilities[1] . " " . $billPay[2]);
                            $this->mr->persist($orderupdate4);
                            $this->mr->flush();
                            $messageBack = "Can not return money!!";
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

    /**
     * PrePaid
     * Provider : LOTO
     * Desc: Fetch ReCharge vouchers
     * @Route("api/alfa/ReCharge", name="ap1_alfa_ReCharge",methods="GET")
     */
    public function ReChargeApi(LotoServices $lotoServices, Memcached $Memcached, NotificationServices $notificationServices)
    {
        try {
            $webkey = apache_request_headers();
            $webkey = $webkey['Authorization'];
            $webkeyDecrypted = SuyoolServices::decryptWebKey($webkey);

            if ($notificationServices->checkUser($webkeyDecrypted['merchantId'], $webkeyDecrypted['lang']) &&  $webkeyDecrypted['devicesType'] == "CORPORATE") {
                if ($_ENV['APP_ENV'] == "prod") {
                    $filter =  $Memcached->getVouchers($lotoServices);
                } else {
                    $filter =  $Memcached->getVouchers($lotoServices);
                }
                // dd($filter);

                return new JsonResponse([
                    'status' => true,
                    'message' => "Success retrieve data",
                    'data' => $filter
                ], 200);
            } else {
                return new JsonResponse([
                    'status' => false,
                    'message' => "Unauthorize"
                ], 401);
            }
        } catch (Exception $e) {
            $this->loggerInterface->error($e->getMessage());
            return new JsonResponse([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * PrePaid
     * Provider : LOTO
     * Desc: Buy PrePaid vouchers
     * @Route("api/alfa/BuyPrePaid", name="ap2_alfa_BuyPrePaid",methods="POST")
     */
    public function ReChargeBuyApi(LotoServices $lotoServices, Memcached $Memcached, NotificationServices $notificationServices, Request $request)
    {
        try {
            $webkey = apache_request_headers();
            $webkey = $webkey['Authorization'];
            $webkeyDecrypted = SuyoolServices::decryptWebKey($webkey);

            if ($notificationServices->checkUser($webkeyDecrypted['merchantId'], $webkeyDecrypted['lang']) &&  $webkeyDecrypted['devicesType'] == "CORPORATE") {
                $SuyoolUserId = $webkeyDecrypted['merchantId'];
                $suyoolServices = new SuyoolServices($this->params->get('ALFA_PREPAID_MERCHANT_ID'));
                $data = json_decode($request->getContent(), true);
                $flagCode = null;

                if ($data != null) {
                    $price = $this->getVoucherPriceByTypeAlfa($data["type"]);
                    //Initial order with status pending
                    $order = new Order;
                    $order
                        ->setsuyoolUserId($SuyoolUserId)
                        ->settransId(null)
                        ->setpostpaidId(null)
                        ->setprepaidId(null)
                        ->setstatus(Order::$statusOrder['PENDING'])
                        ->setamount($price)
                        ->setfees(0)
                        ->setcurrency("LBP");
                    $this->mr->persist($order);
                    $this->mr->flush();

                    $order_id = $this->params->get('ALFA_PREPAID_MERCHANT_ID') . "-" . $order->getId();

                    $suyooler = $this->not->getRepository(Users::class)->findOneBy(['suyoolUserId' => $SuyoolUserId]);

                    //Take amount from .net
                    $response = $suyoolServices->PushUtilities($SuyoolUserId, $order_id, $order->getamount(), $order->getcurrency(), 0);
                    if ($response[0]) {
                        //set order status to held
                        $orderupdate1 = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $SuyoolUserId, 'status' => Order::$statusOrder['PENDING']]);
                        $orderupdate1
                            ->settransId($response[1])
                            ->setstatus(Order::$statusOrder['HELD']);
                        $this->mr->persist($orderupdate1);
                        $this->mr->flush();

                        //buy voucher from loto Provider
                        // $BuyPrePaid = $lotoServices->BuyPrePaid($data["Token"], $data["category"], $data["type"]);
                        // $PayResonse = $BuyPrePaid[0]["d"];
                        $BuyPrePaid = array();
                        $PayResonse = array();
                        $dataPayResponse = $PayResonse;
                        $PayResonse["errorinfo"]["errorcode"] = 0;
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
                            $message = $PayResonse["errorinfo"]["errorcode"];

                            //if not purchase return money
                            $responseUpdateUtilities = $suyoolServices->UpdateUtilities(0, "", $orderupdate1->gettransId());
                            if ($responseUpdateUtilities[0]) {
                                $orderupdate4 = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $SuyoolUserId, 'status' => Order::$statusOrder['HELD']]);
                                $orderupdate4
                                    ->setstatus(Order::$statusOrder['CANCELED'])
                                    ->seterror("{reversed " . $message . "}");
                                $this->mr->persist($orderupdate4);
                                $this->mr->flush();
                            } else {
                                $orderupdate4 = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $SuyoolUserId, 'status' => Order::$statusOrder['HELD']]);
                                $orderupdate4
                                    ->setstatus(Order::$statusOrder['CANCELED'])
                                    ->seterror($responseUpdateUtilities[1]);
                            }
                        }
                        if ($PayResonse["errorinfo"]["errorcode"] == 0) {
                            //if payment from loto provider success insert prepaid data to db
                            $prepaid = new Prepaid;
                            // $prepaid
                            //     ->setvoucherSerial($PayResonse["voucherSerial"])
                            //     ->setvoucherCode($PayResonse["voucherCode"])
                            //     ->setvoucherExpiry($PayResonse["voucherExpiry"])
                            //     ->setdescription($PayResonse["desc"])
                            //     ->setdisplayMessage($PayResonse["displayMessage"])
                            //     ->settoken($PayResonse["token"])
                            //     ->setbalance($PayResonse["balance"])
                            //     ->seterrorMsg($PayResonse["errorinfo"]["errormsg"])
                            //     ->setinsertId($PayResonse["insertId"])
                            //     ->setSuyoolUserId($SuyoolUserId);

                            $prepaid
                                ->setvoucherSerial("123456789")
                                ->setvoucherCode("112233445566")
                                ->setvoucherExpiry("23-09-2024")
                                ->setdescription("")
                                ->setdisplayMessage("")
                                ->settoken("")
                                ->setbalance(0)
                                ->seterrorMsg("")
                                ->setinsertId(0)
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

                            // $dateString = $PayResonse["voucherExpiry"];
                            $dateString = "23-09-2024";
                            $dateTime = new DateTime($dateString);

                            $formattedDate = $dateTime->format('d/m/Y');

                            //intial notification
                            $params = json_encode([
                                'amount' => $order->getamount(),
                                'currency' => "L.L",
                                // 'plan' => $data["desc"],
                                'plan' => 1233,
                                // 'code' => $PayResonse["voucherCode"],
                                'code' => 1231223425,
                                // 'serial' => $PayResonse["voucherSerial"],
                                'serial' => 23455,
                                'expiry' => $formattedDate
                            ]);
                            // $additionalData = "*14*" . $PayResonse["voucherCode"] . "#";
                            $additionalData = "*14*" . "112233445566" . "#";
                            if ($suyooler->getType() == 2) {
                                $content = $notificationServices->getContent('AlfaCardPurchasedSuccessfully');
                                $bulk = 0; //1 for broadcast 0 for unicast
                                $notificationServices->addNotification($SuyoolUserId, $content, $params, $bulk, $additionalData);
                            }
                            $popup = [
                                "Title" => "Alfa Bill Paid Successfully",
                                "globalCode" => 0,
                                "flagCode" => 101,
                                "Message" => "You have successfully paid purchased the Alfa {$data['desc']}",
                                "code" => "*14*" . "112233445566" . "#",
                                "isPopup" => true
                            ];
                            //tell the .net that total amount is paid
                            $responseUpdateUtilities = $suyoolServices->UpdateUtilities($order->getamount(), "", $orderupdate->gettransId());
                            if ($responseUpdateUtilities[0]) {
                                $orderupdate5 = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $SuyoolUserId, 'status' => Order::$statusOrder['PURCHASED']]);
                                //update te status from purshased to completed
                                $orderupdate5
                                    ->setstatus(Order::$statusOrder['COMPLETED'])
                                    ->seterror($responseUpdateUtilities[1]);
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
                        $IsSuccess = false;

                        if (isset($response[2])) {
                            $message = json_decode($response[1], true);
                            $popup = [
                                "Title" => $message['Title'],
                                "globalCode" => 0,
                                "flagCode" => $message['ButtonOne']['Flag'],
                                "Message" => $message['SubTitle'],
                                "isPopup" => true
                            ];
                            $flagCode = $response[2];
                        } else {
                            $message = "You can not purchase now";
                        }
                        $dataPayResponse = -1;
                    }
                } else {
                    $IsSuccess = false;
                    $dataPayResponse = -1;
                    $message = "Can not retrive data !!";
                }
                $parameters = [
                    'message' => $message,
                    'IsSuccess' => $IsSuccess,
                    'flagCode' => $flagCode,
                    'Popup' => @$popup
                ];

                return new JsonResponse([
                    'status' => true,
                    'message' => $message,
                    'data' => $parameters
                ], 200);
            } else {
                return new JsonResponse([
                    'status' => false,
                    'message' => "Unauthorize"
                ], 401);
            }
        } catch (Exception $e) {
            $this->loggerInterface->error($e->getMessage());
            return new JsonResponse([
                'status' => false,
                'message' => "An error has occured",
            ], 500);
        }
    }
}
