<?php

namespace App\Controller;

use App\Entity\Notification\Users;
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
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class TouchController extends AbstractController
{
    private $mr;
    private $hash_algo;
    private $certificate;
    private $params;
    public $cipher_algorithme = "AES128";
    public $key = "SY1X24elh9eG3fpOaHcWlQ9h2bHaqimdIDoyoOaFoi0rukAj3Z";
    public $iv = "fgu26y9e43wc8dj2"; //initiallization vector for decrypt
    private $session;
    private $lotoServices;
    private $memcached;
    private $not;


    public function __construct(ManagerRegistry $mr, $certificate, $hash_algo, ParameterBagInterface $params, SessionInterface $session, LotoServices $lotoServices, Memcached $memcached)
    {
        $this->mr = $mr->getManager('touch');
        $this->hash_algo = $hash_algo;
        $this->not = $mr->getManager('notification');
        $this->certificate = $certificate;
        $this->params = $params;
        $this->session = $session;
        $this->lotoServices = $lotoServices;
        $this->memcached = $memcached;
    }

    /**
     * @Route("/touch", name="app_touch")
     */
    public function index(NotificationServices $notificationServices)
    {
        $useragent = $_SERVER['HTTP_USER_AGENT'];
        //$_POST['infoString']="3mzsXlDm5DFUnNVXA5Pu8T1d5nNACEsiiUEAo7TteE/x3BGT3Oy3yCcjUHjAVYk3";

        if (isset($_POST['infoString'])) {
            $decrypted_string = SuyoolServices::decrypt($_POST['infoString']);
            $suyoolUserInfo = explode("!#!", $decrypted_string);
            $devicetype = stripos($useragent, $suyoolUserInfo[1]);

            if ($notificationServices->checkUser($suyoolUserInfo[0], $suyoolUserInfo[2]) && $devicetype) {
                $SuyoolUserId = $suyoolUserInfo[0];
                $SuyoolUserId = $this->session->set('suyoolUserId', $SuyoolUserId);

                $parameters['deviceType'] = $suyoolUserInfo[1];

                return $this->render('touch/index.html.twig', [
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
     * @Route("/touch/bill", name="app_touch_bill",methods="POST")
     */
    public function bill(Request $request, BobServices $bobServices)
    {
        $SuyoolUserId = $this->session->get('suyoolUserId');
        $data = json_decode($request->getContent(), true);

        if ($data != null) {
            $sendBill = $bobServices->SendTouchPinRequest($data["mobileNumber"]);

            if (isset($sendBill[1]['TouchResponse'])) {
                $sendBill[1] = "Invalid Number";
            }
            $postpaidRequest = new PostpaidRequest;
            if ($sendBill[0]) {
                $postpaidRequest = $this->mr->getRepository(PostpaidRequest::class)->insertbill($postpaidRequest, $SuyoolUserId, $data["mobileNumber"], $sendBill[1], $sendBill[2]);
            } else {
                $postpaidRequest = $this->mr->getRepository(PostpaidRequest::class)->insertbill($postpaidRequest, $SuyoolUserId, $data["mobileNumber"], $sendBill[1], $sendBill[2]);
            }

            $this->mr->persist($postpaidRequest);
            $this->mr->flush();
            $postpaidRequestId = $postpaidRequest->getId();
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

        if ($data != null) {
            $postpaidRequestId = $data["invoicesId"];
            $postpaidRequest =  $this->mr->getRepository(PostpaidRequest::class)->findOneBy(['id' => $postpaidRequestId]);
            $retrieveResults = $bobServices->RetrieveResultsTouch($data["currency"], $data["mobileNumber"], $data["Pin"], $postpaidRequest->gettoken());

            $Pin = implode("", $data["Pin"]);
            if ($retrieveResults[0]) {
                $values = $retrieveResults[1]["Values"];
                $displayedFees = intval($values["Fees"]) + intval($values["Fees1"]) + intval($values["AdditionalFees"]);

                $postpaidRequest =  $this->mr->getRepository(PostpaidRequest::class)->findOneBy(['id' => $postpaidRequestId]);
                $postpaidrequest = $this->mr->getRepository(PostpaidRequest::class)->insertRetrieveResults($postpaidRequest, $retrieveResults[2], $Pin, $retrieveResults[3], $retrieveResults[4], $values["transactionId"], $values["Currency"], $values["Fees"], $values["Fees1"], $values["Amount"], $values["Amount1"], $values["Amount2"], $values["referenceNumber"], $displayedFees, $values["InformativeOriginalWSAmount"], $values["TotalAmount"], $values["Rounding"], $values["AdditionalFees"], $values["InvoiceNumber"], $values["PaymentId"]);
                $this->mr->persist($postpaidrequest);
                $this->mr->flush();
            } else {
                $values = 0;
                $postpaidRequest =  $this->mr->getRepository(PostpaidRequest::class)->findOneBy(['id' => $postpaidRequestId]);
                $postpaidRequest = $this->mr->getRepository(PostpaidRequest::class)->insertRetrieveResults($postpaidRequest, $retrieveResults[2], $Pin, $retrieveResults[3], $retrieveResults[4]);
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
            'errorCode' => $retrieveResults[3],
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
        $suyoolServices = new SuyoolServices($this->params->get('TOUCH_POSTPAID_MERCHANT_ID'));
        $data = json_decode($request->getContent(), true);
        $SuyoolUserId = $this->session->get('suyoolUserId');
        $Postpaid_With_id = $this->mr->getRepository(PostpaidRequest::class)->findOneBy(['id' => $data["ResponseId"]]);
        $flagCode = null;

        if ($data != null) {
            //Initial order with status pending
            $order = new Order;
            $order
                ->setsuyoolUserId($SuyoolUserId)
                ->setstatus(Order::$statusOrder['PENDING'])
                ->setamount($Postpaid_With_id->getamount() + $Postpaid_With_id->getfees())
                ->setfees($Postpaid_With_id->getfees())
                ->setcurrency("LBP");
            $this->mr->persist($order);
            $this->mr->flush();

            $order_id = $this->params->get('TOUCH_POSTPAID_MERCHANT_ID') . "-" . $order->getId();

            //Take amount from .net
            $response = $suyoolServices->PushUtilities($SuyoolUserId, $order_id, $order->getamount(), $this->params->get('CURRENCY_LBP'), $Postpaid_With_id->getfees());

            if ($response[0]) {
                //set order status to held
                $orderupdate1 = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $SuyoolUserId, 'status' => Order::$statusOrder['PENDING']]);
                $orderupdate1
                    ->settransId($response[1])
                    ->setstatus(Order::$statusOrder['HELD']);
                $this->mr->persist($orderupdate1);
                $this->mr->flush();

                //paid postpaid from bob Provider
                $billPay = $bobServices->BillPayTouch($Postpaid_With_id);
                if ($billPay[0]) {
                    //if payment from loto provider success insert prepaid data to db
                    $postpaid = new Postpaid;
                    $postpaid = $this->mr->getRepository(PostpaidRequest::class)->insertBillPay($postpaid, $Postpaid_With_id->getSuyoolUserId(), $Postpaid_With_id->getGsmNumber(), $Postpaid_With_id->gettoken(), $billPay[1]["TransactionDescription"], $billPay[1]["TransactionReference"], $Postpaid_With_id->getpin(), $Postpaid_With_id->getTransactionId(), $Postpaid_With_id->getcurrency(), $Postpaid_With_id->getfees(), $Postpaid_With_id->getfees1(), $Postpaid_With_id->getamount(), $Postpaid_With_id->getamount1(), $Postpaid_With_id->getamount2(), $Postpaid_With_id->getreferenceNumber(), $Postpaid_With_id->getinformativeOriginalWSamount(), $Postpaid_With_id->gettotalamount(), $Postpaid_With_id->getrounding(), $Postpaid_With_id->getadditionalfees(), $Postpaid_With_id->getinvoiceNumber(), $Postpaid_With_id->getpaymentId(), $billPay[2]);
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
                    $content = $notificationServices->getContent('AcceptedTouchPayment');
                    $bulk = 0; //1 for broadcast 0 for unicast
                    $notificationServices->addNotification($SuyoolUserId, $content, $params, $bulk, $additionalData);

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
                    $responseUpdateUtilities = $suyoolServices->UpdateUtilities($order->getamount(), $updateUtilitiesAdditionalData, $orderupdate->gettransId());
                    if ($responseUpdateUtilities) {
                        $orderupdate5 = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $SuyoolUserId, 'status' => Order::$statusOrder['PURCHASED']]);

                        //update te status from purshased to completed
                        $orderupdate5
                            ->setstatus(Order::$statusOrder['COMPLETED']);
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
                            ->seterror("Not found BillPayTouch");
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
     * PrePaid
     * Provider : LOTO
     * Desc: Fetch ReCharge vouchers
     * @Route("/touch/ReCharge", name="app_touch_ReCharge",methods="POST")
     */
    public function ReCharge(LotoServices $lotoServices, Memcached $Memcached)
    {
        if ($_ENV['APP_ENV'] == "prod") {
            $filter =  $Memcached->getVouchersTouch($lotoServices);
        } else {
            // $filter =  $Memcached->getVouchersTouch($lotoServices);
        }

        return new JsonResponse([
            'status' => true,
            'message' => $filter
        ], 200);
    }

    public function getVoucherPriceByTypeTouch($type)
    {
        $filterAlfa = $this->memcached->getVouchersTouch($this->lotoServices);
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
     * @Route("/touch/BuyPrePaid", name="app_touch_BuyPrePaid",methods="POST")
     */
    public function BuyPrePaid(Request $request, LotoServices $lotoServices, NotificationServices $notificationServices)
    {
        $SuyoolUserId = $this->session->get('suyoolUserId');
        $suyoolServices = new SuyoolServices($this->params->get('TOUCH_PREPAID_MERCHANT_ID'));
        $data = json_decode($request->getContent(), true);
        $flagCode = null;

        if ($data != null) {
            $price = $this->getVoucherPriceByTypeTouch($data["type"]);
            $cardsPerDay = $this->mr->getRepository(Order::class)->purchaseCardsPerDay($SuyoolUserId, $data["type"]);
            // dd($cardsPerDay);
            if (!is_null($cardsPerDay) && $cardsPerDay['numberofcompletedordersprepaid'] >= $this->params->get('CARDS_PER_DAY_PREPAID')) {
                return new JsonResponse([
                    'status' => true,
                    'IsSuccess' => false,
                    'flagCode' => 210,
                    'Title' => 'Daily Limit Exceeded',
                    'message' => "In our effort to accommodate all our Suyoolers fairly, we are temporarily limiting the purchase to 2 recharge cards per type per day.We plan to remove this limitation as soon as possible."
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
                ->setfees(0)
                ->setVoucherTypeId($data['type'])
                ->setamount($price)
                ->setcurrency("LBP");
            $this->mr->persist($order);
            $this->mr->flush();

            $order_id = $this->params->get('TOUCH_PREPAID_MERCHANT_ID') . "-" . $order->getId();

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

                        //if not purchase return money
                        $responseUpdateUtilities = $suyoolServices->UpdateUtilities(0, "", $orderupdate1->gettransId());
                        if ($responseUpdateUtilities[0]) {
                            $orderupdate4 = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $SuyoolUserId, 'status' => Order::$statusOrder['HELD']]);
                            $orderupdate4
                                ->setstatus(Order::$statusOrder['CANCELED'])
                                ->seterror("{reversed " . $PayResonse["errorinfo"]["errormsg"] . "}");
                            $this->mr->persist($orderupdate4);
                            $this->mr->flush();

                            $message = $PayResonse["errorinfo"]["errorcode"];
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
                            ->setstatus("purchased");
                        $this->mr->persist($orderupdate);
                        $this->mr->flush();

                        $dateString = $PayResonse["voucherExpiry"];
                        $dateTime = new DateTime($dateString);

                        $formattedDate = $dateTime->format('d/m/Y');

                        // //intial notification
                        $params = json_encode([
                            'amount' => $order->getamount(),
                            'currency' => "L.L",
                            'plan' => $data["desc"],
                            'code' => $PayResonse["voucherCode"],
                            'serial' => $PayResonse["voucherSerial"],
                            'expiry' => $formattedDate
                        ]);
                        $content = $notificationServices->getContent('TouchCardPurchasedSuccessfully');
                        $bulk = 0; //1 for broadcast 0 for unicast
                        $notificationServices->addNotification($SuyoolUserId, $content, $params, $bulk, "*200*" . $PayResonse["voucherCode"] . "#");
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
     * @Route("api/touch", name="ap1_touch_bill_pay",methods="GET")
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
     * @Route("/api/touch/bill", name="ap2_touch_bill", methods="POST")
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
                    $sendBill = $bobServices->SendTouchPinRequest($data["mobileNumber"]);

                    if (isset($sendBill[1]['TouchResponse'])) {
                        $sendBill[1] = "Invalid Number";
                    }
                    $postpaidRequest = new PostpaidRequest;
                    $postpaidRequest = $this->mr->getRepository(PostpaidRequest::class)->insertbill($postpaidRequest, $SuyoolUserId, $data["mobileNumber"], $sendBill[1], $sendBill[2]);
                    $this->mr->persist($postpaidRequest);
                    $this->mr->flush();
                    if ($sendBill[0]) {
                        $postpaidRequestId = $postpaidRequest->getId();
                    } else {
                        $postpaidRequestId = -1;
                    }
                    $messageBack = $sendBill[2];
                }
                $parameters = [
                    'isSuccess' => $sendBill[0],
                    'postpaidRequestId' => $postpaidRequestId
                ];
                return new JsonResponse([
                    'status' => true,
                    'message' => $messageBack,
                    'data' => $parameters
                ], 200);
            } else {
                return new JsonResponse([
                    'status' => false,
                    'message' => 'Unauthorize'
                ], 401);
            }
        } catch (Exception $e) {
            // $this->loggerInterface->error($e->getMessage());
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
     * @Route("/api/touch/bill/RetrieveResults", name="ap3_touch_RetrieveResults",methods="POST")
     */
    public function RestAlfaBillApiResults(Request $request, BobServices $bobServices, NotificationServices $notificationServices)
    {
        try {
            $data = json_decode($request->getContent(), true);
            $webkey = apache_request_headers();
            $webkey = $webkey['Authorization'];
            $webkeyDecrypted = SuyoolServices::decryptWebKey($webkey);

            if ($notificationServices->checkUser($webkeyDecrypted['merchantId'], $webkeyDecrypted['lang']) &&  $webkeyDecrypted['devicesType'] == "CORPORATE") {
                $displayedFees = 0;
                $SuyoolUserId = $webkeyDecrypted['merchantId'];

                if ($data != null) {
                    $postpaidRequestId = $data["invoicesId"];
                    $postpaidRequest =  $this->mr->getRepository(PostpaidRequest::class)->findOneBy(['id' => $postpaidRequestId]);
                    $retrieveResults = $bobServices->RetrieveResultsTouch($data["currency"], $data["mobileNumber"], $data["Pin"], $postpaidRequest->gettoken());

                    $Pin = implode("", $data["Pin"]);
                    if ($retrieveResults[0]) {
                        $values = $retrieveResults[1]["Values"];
                        $displayedFees = intval($values["Fees"]) + intval($values["Fees1"]) + intval($values["AdditionalFees"]);
                        $postpaidRequest =  $this->mr->getRepository(PostpaidRequest::class)->findOneBy(['id' => $postpaidRequestId]);
                        $postpaidrequest = $this->mr->getRepository(PostpaidRequest::class)->insertRetrieveResults($postpaidRequest, $retrieveResults[2], $Pin, $retrieveResults[3], $retrieveResults[4], $values["transactionId"], $values["Currency"], $values["Fees"], $values["Fees1"], $values["Amount"], $values["Amount1"], $values["Amount2"], $values["referenceNumber"], $displayedFees, $values["InformativeOriginalWSAmount"], $values["TotalAmount"], $values["Rounding"], $values["AdditionalFees"], $values["InvoiceNumber"], $values["PaymentId"]);
                        $this->mr->persist($postpaidrequest);
                        $this->mr->flush();
                        $invoicesId = $postpaidRequest->getId();
                    } else {
                        $values = 0;
                        $postpaidRequest =  $this->mr->getRepository(PostpaidRequest::class)->findOneBy(['id' => $postpaidRequestId]);
                        $postpaidRequest = $this->mr->getRepository(PostpaidRequest::class)->insertRetrieveResults($postpaidRequest, $retrieveResults[2], $Pin, $retrieveResults[3], $retrieveResults[4]);
                        $this->mr->persist($postpaidRequest);
                        $this->mr->flush();
                        $invoicesId = -1;
                    }
                    $messageBack = $retrieveResults[2];
                } else {
                    $values = -1;
                    $invoicesId = -1;
                }
                $parameters = [
                    'isSuccess' => $retrieveResults[0],
                    'errorCode' => $retrieveResults[2],
                    'postpayed' => $invoicesId,
                    'displayData' => $values,
                    'displayedFees' => $displayedFees,
                ];

                return new JsonResponse([
                    'status' => true,
                    'message' => @$messageBack,
                    'data' => $parameters
                ], 200);
            } else {
                return new JsonResponse([
                    'status' => false,
                    'message' => 'unauthorize'
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
     * @Route("/api/touch/bill/pay", name="ap4_touch_bill", methods="POST")
     */
    public function apitouchpaypost(Request $request, BobServices $bobServices, NotificationServices $notificationServices)
    {
        try {
            $data = json_decode($request->getContent(), true);
            $webkey = apache_request_headers();
            $webkey = $webkey['Authorization'];
            $webkeyDecrypted = SuyoolServices::decryptWebKey($webkey);

            if ($notificationServices->checkUser($webkeyDecrypted['merchantId'], $webkeyDecrypted['lang']) &&  $webkeyDecrypted['devicesType'] == "CORPORATE") {
                $suyoolServices = new SuyoolServices($this->params->get('TOUCH_POSTPAID_MERCHANT_ID'));
                $Postpaid_With_id = $this->mr->getRepository(PostpaidRequest::class)->findOneBy(['id' => $data["ResponseId"]]);
                $SuyoolUserId = $webkeyDecrypted['merchantId'];
                $flagCode = null;

                if ($data != null) {
                    //Initial order with status pending
                    $order = new Order;
                    $order
                        ->setsuyoolUserId($SuyoolUserId)
                        ->setstatus(Order::$statusOrder['PENDING'])
                        ->setamount($Postpaid_With_id->getamount() + $Postpaid_With_id->getfees())
                        ->setfees($Postpaid_With_id->getfees())
                        ->setcurrency("LBP")
                        ->setIsCorporate(true);
                    $this->mr->persist($order);
                    $this->mr->flush();

                    $order_id = $this->params->get('TOUCH_POSTPAID_MERCHANT_ID') . "-" . $order->getId();

                    //Take amount from .net
                    $response = $suyoolServices->PushUtilities($SuyoolUserId, $order_id, $order->getamount(), $this->params->get('CURRENCY_LBP'), $Postpaid_With_id->getfees());

                    if ($response[0]) {
                        //set order status to held
                        $orderupdate1 = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $SuyoolUserId, 'status' => Order::$statusOrder['PENDING']]);
                        $orderupdate1
                            ->settransId($response[1])
                            ->setstatus(Order::$statusOrder['HELD']);
                        $this->mr->persist($orderupdate1);
                        $this->mr->flush();

                        //paid postpaid from bob Provider
                        $billPay = $bobServices->BillPayTouch($Postpaid_With_id);
                        if ($billPay[0]) {
                            //if payment from loto provider success insert prepaid data to db
                            $postpaid = new Postpaid;
                            $postpaid = $this->mr->getRepository(PostpaidRequest::class)->insertBillPay($postpaid, $Postpaid_With_id->getSuyoolUserId(), $Postpaid_With_id->getGsmNumber(), $Postpaid_With_id->gettoken(), $billPay[1]["TransactionDescription"], $billPay[1]["TransactionReference"], $Postpaid_With_id->getpin(), $Postpaid_With_id->getTransactionId(), $Postpaid_With_id->getcurrency(), $Postpaid_With_id->getfees(), $Postpaid_With_id->getfees1(), $Postpaid_With_id->getamount(), $Postpaid_With_id->getamount1(), $Postpaid_With_id->getamount2(), $Postpaid_With_id->getreferenceNumber(), $Postpaid_With_id->getinformativeOriginalWSamount(), $Postpaid_With_id->gettotalamount(), $Postpaid_With_id->getrounding(), $Postpaid_With_id->getadditionalfees(), $Postpaid_With_id->getinvoiceNumber(), $Postpaid_With_id->getpaymentId(), $billPay[2]);
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
                            $content = $notificationServices->getContent('AcceptedTouchPayment');
                            $bulk = 0; //1 for broadcast 0 for unicast
                            $notificationServices->addNotification($SuyoolUserId, $content, $params, $bulk, $additionalData);

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
                            $responseUpdateUtilities = $suyoolServices->UpdateUtilities($order->getamount(), $updateUtilitiesAdditionalData, $orderupdate->gettransId());
                            if ($responseUpdateUtilities) {
                                $orderupdate5 = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $SuyoolUserId, 'status' => Order::$statusOrder['PURCHASED']]);

                                //update te status from purshased to completed
                                $orderupdate5
                                    ->setstatus(Order::$statusOrder['COMPLETED']);
                                $this->mr->persist($orderupdate5);
                                $this->mr->flush();

                                $popup = [
                                    "Title" => "Touch Bill Paid Successfully",
                                    "globalCode" => 0,
                                    "flagCode" => 0,
                                    "Message" => "You have successfully paid your Touch bill of L.L " . number_format($order->getamount()) . ".",
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
                                    ->seterror("Not found BillPayTouch");
                                $this->mr->persist($orderupdate4);
                                $this->mr->flush();

                                $messageBack = "Success return money!!";
                                $message = "Success return money!!";
                            } else {
                                $orderupdate4 = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $SuyoolUserId, 'status' => Order::$statusOrder['HELD']]);
                                $orderupdate4
                                    ->setstatus(Order::$statusOrder['CANCELED'])
                                    ->seterror($responseUpdateUtilities[1]);
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
        } catch (Exception $e) {
            return new JsonResponse([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * PrePaid
     * Provider : LOTO
     * Desc: Fetch ReCharge vouchers
     * @Route("api/touch/ReCharge", name="ap1_touch_ReCharge",methods="GET")
     */
    public function ReChargeApi(LotoServices $lotoServices, Memcached $Memcached, NotificationServices $notificationServices)
    {
        try {
            $webkey = apache_request_headers();
            $webkey = $webkey['Authorization'];
            $webkeyDecrypted = SuyoolServices::decryptWebKey($webkey);

            if ($notificationServices->checkUser($webkeyDecrypted['merchantId'], $webkeyDecrypted['lang']) &&  $webkeyDecrypted['devicesType'] == "CORPORATE") {
                if ($_ENV['APP_ENV'] == "prod") {
                    $filter =  $Memcached->getVouchersTouch($lotoServices);
                } else {
                    $filter =  $Memcached->getVouchersTouch($lotoServices);
                }
                // dd($filter);

                return new JsonResponse([
                    'status' => true,
                    'message' => "Success retrieve data",
                    'data' => array_merge($filter)
                ], 200);
            } else {
                return new JsonResponse([
                    'status' => false,
                    'message' => "Unauthorize",
                    'data'=>[
                        'Popup'=>[
                            "Title" => "Unauthorize",
                            "globalCode" => 0,
                            "flagCode" => 801,
                            "Message" => "You have been unauthorized",
                            "isPopup" => true
                        ]
                    ]
                ], 401);
            }
        } catch (Exception $e) {
            return new JsonResponse([
                'status' => false,
                'message' => "An error has occured",
                'data' => [
                    'Popup' => [
                        "Title" => "An error has occured",
                        "globalCode" => 0,
                        "flagCode" => 802,
                        "Message" => "An error has occured",
                        "isPopup" => true
                    ]
                ]
            ], 500);
        }
    }

    /**
     * PrePaid
     * Provider : LOTO
     * Desc: Buy PrePaid vouchers
     * @Route("api/touch/BuyPrePaid", name="ap2_touch_BuyPrePaid",methods="POST")
     */
    public function ReChargeBuyApi(LotoServices $lotoServices, Memcached $Memcached, NotificationServices $notificationServices, Request $request)
    {
        try {
            $webkey = apache_request_headers();
            $webkey = $webkey['Authorization'];
            $webkeyDecrypted = SuyoolServices::decryptWebKey($webkey);

            if ($notificationServices->checkUser($webkeyDecrypted['merchantId'], $webkeyDecrypted['lang']) &&  $webkeyDecrypted['devicesType'] == "CORPORATE") {
                $SuyoolUserId = $webkeyDecrypted['merchantId'];
                $suyoolServices = new SuyoolServices($this->params->get('TOUCH_PREPAID_MERCHANT_ID'));
                $data = json_decode($request->getContent(), true);
                $flagCode = null;

                if ($data != null) {
                    $price = $this->getVoucherPriceByTypeTouch($data["type"]);
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

                    $order_id = $this->params->get('TOUCH_PREPAID_MERCHANT_ID') . "-" . $order->getId();

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
                        $BuyPrePaid = $lotoServices->BuyPrePaid($data["Token"], $data["category"], $data["type"]);
                        $PayResonse = $BuyPrePaid[0]["d"];
                        // $BuyPrePaid = array();
                        // $PayResonse = array();
                        $dataPayResponse = $PayResonse;
                        // $PayResonse["errorinfo"]["errorcode"] = 0;
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
                                $popup = [
                                    "Title" => "Return money Successfully",
                                    "globalCode" => 0,
                                    "flagCode" => 800,
                                    "Message" => "Return money Successfully",
                                    // "code" => "*14*" . "112233445566" . "#",
                                    "isPopup" => true
                                ];
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

                            // $prepaid
                            //     ->setvoucherSerial("123456789")
                            //     ->setvoucherCode("112233445566")
                            //     ->setvoucherExpiry("23-09-2024")
                            //     ->setdescription("")
                            //     ->setdisplayMessage("")
                            //     ->settoken("")
                            //     ->setbalance(0)
                            //     ->seterrorMsg("")
                            //     ->setinsertId(0)
                            //     ->setSuyoolUserId($SuyoolUserId);

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
                            // $dateString = "23-09-2024";
                            $dateTime = new DateTime($dateString);

                            $formattedDate = $dateTime->format('d/m/Y');

                            //intial notification
                            $params = json_encode([
                                'amount' => $order->getamount(),
                                'currency' => "L.L",
                                'plan' => $data["desc"],
                                // 'plan' => 1233,
                                'code' => $PayResonse["voucherCode"],
                                // 'code' => 1231223425,
                                'serial' => $PayResonse["voucherSerial"],
                                // 'serial' => 23455,
                                'expiry' => $formattedDate,
                                'name'=> $data['PayerName']
                            ]);
                            $additionalData = "*14*" . $PayResonse["voucherCode"] . "#";
                            // $additionalData = "*14*" . "112233445566" . "#";
                            if ($suyooler->getType() == 2) {
                                $content = $notificationServices->getContent('TouchCardPurchasedSuccessfully');
                                $bulk = 0; //1 for broadcast 0 for unicast
                                $notificationServices->addNotification($SuyoolUserId, $content, $params, $bulk, $additionalData);
                            }else{
                                $content = $notificationServices->getContent('TouchCardPurchasedSuccessfullyCorporate');
                                $bulk = 1; //1 for broadcast 0 for unicast
                                $notificationServices->addNotification($data['getUsersToReceiveNotification'], $content, $params, $bulk, $additionalData);
                            }
                            $popup = [
                                "Title" => "Touch Bill Paid Successfully",
                                "globalCode" => 0,
                                "flagCode" => 101,
                                "Message" => "You have successfully paid purchased the Touch {$data['desc']}",
                                "code"=>"*14*" . "112233445566" . "#",
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
                        $message = json_decode($response[1], true);
                        if (isset($response[2]) && isset($message['Title'])) {
                            $message = json_decode($response[1], true);
                            $popup = [
                                "Title" => $message['Title'],
                                "globalCode" => 0,
                                "flagCode" => $message['ButtonOne']['Flag'],
                                "Message" => $message['SubTitle'],
                                "isPopup" => true
                            ];
                            $flagCode = $response[2];
                        }else {
                            $message = "You can not purchase now";
                            $popup = [
                                "Title" => "Error has occured",
                                "globalCode" => 0,
                                "flagCode" => 800,
                                "Message" => "Please try again <br> Error: {$response[3]}",
                                "isPopup" => true
                            ];
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
                    'message' => "Unauthorize",
                    'data'=>[
                        'Popup'=>[
                            "Title" => "Unauthorize",
                            "globalCode" => 0,
                            "flagCode" => 801,
                            "Message" => "You have been unauthorized",
                            "isPopup" => true
                        ]
                    ]
                ], 401);
            }
        } catch (Exception $e) {
            return new JsonResponse([
                'status' => false,
                'message' => "An error has occured",
                'data' => [
                    'Popup' => [
                        "Title" => "An error has occured",
                        "globalCode" => 0,
                        "flagCode" => 802,
                        "Message" => "An error has occured",
                        "isPopup" => true
                    ]
                ]
            ], 500);
        }
    }
}
