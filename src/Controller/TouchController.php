<?php

namespace App\Controller;

use App\Entity\Touch\Logs;
use App\Entity\Touch\Order;
use App\Entity\Touch\Postpaid;
use App\Entity\Touch\Prepaid;
use App\Entity\Touch\PostpaidRequest;
use App\Service\LotoServices;
use App\Service\BobServices;
use App\Service\DecryptService;
use App\Service\Memcached;
use App\Service\NotificationServices;
use App\Service\SuyoolServices;
use Doctrine\Persistence\ManagerRegistry;
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


    public function __construct(ManagerRegistry $mr, $certificate, $hash_algo, ParameterBagInterface $params, SessionInterface $session)
    {
        $this->mr = $mr->getManager('touch');
        $this->hash_algo = $hash_algo;
        $this->certificate = $certificate;
        $this->params = $params;
        $this->session = $session;
    }

    /**
     * @Route("/touch", name="app_touch")
     */
    public function index(NotificationServices $notificationServices)
    {
        $useragent = $_SERVER['HTTP_USER_AGENT'];
        // $_POST['infoString']="3mzsXlDm5DFUnNVXA5Pu8T1d5nNACEsiiUEAo7TteE/x3BGT3Oy3yCcjUHjAVYk3";

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
            $response = $suyoolServices->PushUtilities($SuyoolUserId, $order_id, $order->getamount(), $this->params->get('CURRENCY_LBP'),0);

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

                        $dataPayResponse = ['amount' => $order->getamount(), 'currency' => $order->getcurrency(),'fees'=>0];
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
        $filter =  $Memcached->getVouchersTouch($lotoServices);

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
        $SuyoolUserId = $this->session->get('suyoolUserId');
        $suyoolServices = new SuyoolServices($this->params->get('TOUCH_PREPAID_MERCHANT_ID'));
        $data = json_decode($request->getContent(), true);
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
                ->setfees(0)
                ->setamount($data["amountLBP"])
                ->setcurrency("LBP");
            $this->mr->persist($order);
            $this->mr->flush();

            $order_id = $this->params->get('TOUCH_PREPAID_MERCHANT_ID') . "-" . $order->getId();

            //Take amount from .net
            $response = $suyoolServices->PushUtilities($SuyoolUserId, $order_id, $order->getamount(), $order->getcurrency(),0);

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

                    //intial notification
                    $params = json_encode([
                        'amount' => $order->getamount(),
                        'currency' => "L.L",
                        'plan' => $data["desc"],
                        'code' => $PayResonse["voucherCode"],
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
}
