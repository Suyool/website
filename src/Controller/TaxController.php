<?php


namespace App\Controller;


use App\Entity\Tax\Logs;
use App\Entity\Tax\Order;
use App\Entity\Tax\Tax;
use App\Entity\Tax\TaxRequest;
use App\Service\BobServices;
use App\Service\LogsService;
use App\Service\NotificationServices;
use App\Service\SuyoolServices;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class TaxController extends AbstractController
{
    private $mr;
    private $params;
    private $session;

    public function __construct(ManagerRegistry $mr, ParameterBagInterface $params, SessionInterface $sessionInterface)
    {
        $this->mr = $mr->getManager('tax');
        $this->params = $params;
        $this->session = $sessionInterface;
    }

    /**
     * @Route("api/tax/retrieve", name="app_tax_retrieve")
     */
    public function index(Request $request, BobServices $bobServices, NotificationServices $notificationServices): Response
    {
        try {
            $data = json_decode($request->getContent(), true);
            $webkey = apache_request_headers();
            $webkeyDecrypted = SuyoolServices::decryptWebKey($webkey);

            if ($notificationServices->checkUser($webkeyDecrypted['merchantId'], $webkeyDecrypted['lang']) &&  $webkeyDecrypted['devicesType'] == "CORPORATE") {
                $suyoolUserId = $webkeyDecrypted['merchantId'];

                if ($data != null) {
                    $requestData = [
                        "ChannelType" => "API",
                        "ItemId" => 2497,
                        "VenId" => 20,
                        "ProductId" => 25,
                        "TaxMeta" => [
                            "Amount" => $data["amount"],
                            "DocumentNumber" => $data['documentNumber']
                        ]
                    ];

                    $RetrieveChannel = $bobServices->RetrieveReqResults($requestData);
                    $pushlog = new LogsService($this->mr);
                    $pushlog->pushLogs(new Logs,"ap2_tax_bill",@$RetrieveChannel[7],@$RetrieveChannel[4],@$RetrieveChannel[5],@$RetrieveChannel[6]);

                    $RetrieveChannel[0] = false;
                    if ($RetrieveChannel[0] == true) {
                        $resp = $RetrieveChannel[1]["Values"];
                        $displayedFees = intval($resp["Fees"]) + intval($resp["Fees1"]) + intval($resp["AdditionalFees"]);

                        $taxReq = new TaxRequest();
                        $taxReq
                            ->setsuyoolUserId($suyoolUserId)
                            ->setDocumentNumber($data["documentNumber"])
                            ->setresponse($RetrieveChannel[4])
                            ->seterrordesc($RetrieveChannel[2])
                            ->settransactionId($resp["TransactionId"])
                            ->setcurrency($resp["Currency"])
                            ->setamount($resp["Amount"])
                            ->setamount1($resp["Amount1"])
                            ->setamount2($resp["Amount2"])
                            ->settotalAmount($resp["TotalAmount"])
                            ->setMoFTotalAmount($resp["MoFTotalAmount"])
                            ->setMoFFiscalStamp($resp["MoFFiscalStamp"])
                            ->setadditionalFees($resp["AdditionalFees"])
                            ->setfees($resp["Fees"])
                            ->setfees1($resp["Fees1"])
                            ->setdisplayedFees($displayedFees)
                            ->setrounding($resp["Rounding"]);

                        $this->mr->persist($taxReq);
                        $this->mr->flush();

                        $taxReqId = $taxReq->getId();
                        $messageBack = "Success";
                        $message = $resp;
                        $documentNumber = $data["documentNumber"];
                        $parameters = [
                            'documentNumber' => $documentNumber,
                            'displayBill' => $message,
                            'TaxReqId' => $taxReqId,
                            'displayedFees' => $displayedFees,
                        ];
                    } else {
                        $taxReq = new TaxRequest;
                        $taxReq
                            ->setsuyoolUserId($suyoolUserId)
                            ->setDocumentNumber($data["documentNumber"])
                            ->setresponse($RetrieveChannel[4])
                            ->seterrordesc($RetrieveChannel[2]);
                        $this->mr->persist($taxReq);
                        $this->mr->flush();
                        $error = explode("-", $RetrieveChannel[2]);

                        $messageBack = $RetrieveChannel[2];
                        switch ($error[1]) {
                            case 111:
                                $title = "No Pending Bill";
                                $body = "There is no pending bill on the document number {$data["documentNumber"]}<br/>Kindly try again later";
                                break;
                            case 108:
                                $title = "No Pending Bill";
                                $body = "There is no pending bill on the document number {$data["documentNumber"]}<br/>Kindly try again later";
                                break;
                            case 112:
                                $title = "Number Not Found";
                                $body = "The number you entered was not found in the system.<br>Kindly try another number.";
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
                        $parameters = [
                            'Popup' => $popup
                        ];
                    }
                } else {
                    $message = "not connected";
                    $TaxReqId = -1;
                    $mobileNb = -1;
                }

                return new JsonResponse([
                    'status' => true,
                    'message' => @$messageBack,
                    'data' => @$parameters
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
     * @Route("/api/tax/pay", name="ap3_tax_bill", methods="POST")
     */
    public function billPayApi(Request $request, BobServices $bobServices, NotificationServices $notificationServices)
    {
        try {
            $data = json_decode($request->getContent(), true);
            $webkey = apache_request_headers();
            $webkeyDecrypted = SuyoolServices::decryptWebKey($webkey);

            if ($notificationServices->checkUser($webkeyDecrypted['merchantId'], $webkeyDecrypted['lang']) &&  $webkeyDecrypted['devicesType'] == "CORPORATE") {
                $suyoolServices = new SuyoolServices($this->params->get('TAX_MERCHANT_ID_PROD'));
                $suyoolUserId = $webkeyDecrypted['merchantId'];
                $tax_With_id = $this->mr->getRepository(TaxRequest::class)->findOneBy(['id' => $data["ResponseId"]]);
                $flagCode = null;

                if ($data != null) {
                    //Initial order with status pending
                    $order = new Order();
                    $order
                        ->setsuyoolUserId($suyoolUserId)
                        ->settransId(null)
                        ->setTaxId(null)
                        ->setstatus(Order::$statusOrder['PENDING'])
                        ->setamount($tax_With_id->getamount() + $tax_With_id->getfees())
                        ->setfees($tax_With_id->getfees())
                        ->setcurrency("LBP");
                    $this->mr->persist($order);
                    $this->mr->flush();

                    $orderTst = $this->params->get('TAX_MERCHANT_ID_PROD') . "-" . $order->getId();
                    //Take amount from .net
                    $response = $suyoolServices->PushUtilities($suyoolUserId, $orderTst, $order->getamount(), $this->params->get('CURRENCY_LBP'), $tax_With_id->getfees());

                    $pushlog = new LogsService($this->mr);
                    $pushlog->pushLogs(new Logs,"PushUtility",@$response[4],@$response[5],@$response[7], @$response[6]);
                    if ($response[0]) {
                        //set order status to held
                        $orderupdate1 = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $suyoolUserId, 'status' => Order::$statusOrder['PENDING']]);
                        $orderupdate1
                            ->settransId($response[1])
                            ->setstatus(Order::$statusOrder['HELD']);
                        $this->mr->persist($orderupdate1);
                        $this->mr->flush();

                        $requestData = [
                            "ChannelType" => "API",
                            "ItemId" => 2497,
                            "VenId" => 20,
                            "ProductId" => 25,
                            "TransactionId" => strval($tax_With_id->gettransactionId()),
                            "TaxResult" => [
                                "Amount" => strval($tax_With_id->getamount()),
                                "DocumentNumber" => strval($tax_With_id->getDocumentNumber()),
                                "Fees" => strval($tax_With_id->getfees()),
                                "TotalAmount" => strval($tax_With_id->gettotalAmount()),
                                "AdditionalFees" => strval($tax_With_id->getadditionalFees()),
                            ],
                        ];
                        $BillTranPayment = $bobServices->BillTranPayment($requestData);
                        $pushlog->pushLogs(new Logs,"ap3_tax_bill_inject",@$BillTranPayment[4],@$BillTranPayment[3],@$BillTranPayment[5],@$BillTranPayment[6]);
                        if ($BillTranPayment[0]) {
                            //if payment from Bob provider success insert tax data to db
                            $tax = new Tax();
                            $tax
                                ->setsuyoolUserId($suyoolUserId)
                                ->setDocumentNumber($tax_With_id->getDocumentNumber())
                                ->settransactionId($tax_With_id->gettransactionId())
                                ->settransactionDescription($BillTranPayment[1]["TransactionDescription"])
                                ->setreferenceNumber($BillTranPayment[1]["ReferenceNumber"])
                                ->setdisplayedFees($tax_With_id->getdisplayedFees())
                                ->setcurrency($tax_With_id->getcurrency())
                                ->setamount($tax_With_id->getamount())
                                ->setamount1($tax_With_id->getamount1())
                                ->setamount2($tax_With_id->getamount2())
                                ->settotalAmount($tax_With_id->gettotalAmount())
                                ->setMoFTotalAmount($tax_With_id->getMoFTotalAmount())
                                ->setadditionalFees($tax_With_id->getadditionalFees())
                                ->setfees($tax_With_id->getfees())
                                ->setfees1($tax_With_id->getfees1())
                                ->setrounding($tax_With_id->getrounding());
                            $this->mr->persist($tax);
                            $this->mr->flush();

                            $IsSuccess = true;

                            $taxId = $tax->getId();
                            $tax = $this->mr->getRepository(Tax::class)->findOneBy(['id' => $taxId]);

                            //update order by passing prepaidId to order and set status to purshased
                            $orderupdate = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $suyoolUserId, 'status' => Order::$statusOrder['HELD']]);
                            $orderupdate
                                ->setTaxId($tax)
                                ->setstatus(Order::$statusOrder['PURCHASED']);
                            $this->mr->persist($orderupdate);
                            $this->mr->flush();

                            //intial notification
                            $params = json_encode([
                                'amount' => number_format($order->getamount()),
                                'currency' => "L.L",
                                'mobilenumber' => $tax_With_id->getGsmNumber(),
                                'name' => $data['PayerName']
                            ]);
                            $additionalData = "";
                            $popup = [
                                "Title" => "Tax Bill Paid Successfully",
                                "globalCode" => 0,
                                "flagCode" => 0,
                                "Message" => "You have successfully paid your Tax bill of L.L " . number_format($order->getamount()) . ".",
                                "isPopup" => true
                            ];
                            $content = $notificationServices->getContent('AcceptedOgeroPaymentCorporate');
                            $bulk = 1; //1 for broadcast 0 for unicast
                            $notificationServices->addNotification($data["getUsersToReceiveNotification"], $content, $params, $bulk, $additionalData);

                            $updateUtilitiesAdditionalData = json_encode([
                                'Amount' => $tax_With_id->getamount(),
                                'Amount1' => $tax_With_id->getamount1(),
                                'Amount2' => $tax_With_id->getamount2(),
                                'TransactionId' => $tax_With_id->gettransactionId(),
                                'Fees' => $tax_With_id->getfees(),
                                'Fees1' => $tax_With_id->getfees1(),
                                'TotalAmount' => $tax_With_id->gettotalAmount(),
                                'Currency' => $tax_With_id->getcurrency(),
                                'Rounding' => $tax_With_id->getrounding(),
                                'AdditionalFees' => $tax_With_id->getadditionalFees(),
                                'documentNumber' => $tax_With_id->getDocumentNumber()
                            ]);

                            $message = "Success";
                            $messageBack = "Success";
                            //tell the .net that total amount is paid
                            $responseUpdateUtilities = $suyoolServices->UpdateUtilities($order->getamount(), $updateUtilitiesAdditionalData, $orderupdate->gettransId());

                            $pushlog->pushLogs(new Logs,"UpdateUtility",@$responseUpdateUtilities[3],@$responseUpdateUtilities[2], @$responseUpdateUtilities[4],@$responseUpdateUtilities[5]);
                            if ($responseUpdateUtilities[0]) {
                                $orderupdate5 = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $suyoolUserId, 'status' => Order::$statusOrder['PURCHASED']]);

                                //update te status from purshased to completed
                                $orderupdate5
                                    ->setstatus(Order::$statusOrder['COMPLETED'])
                                    ->seterror($responseUpdateUtilities[1]);
                                $this->mr->persist($orderupdate5);
                                $this->mr->flush();
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
                            $logs->setidentifier("Tax error");

                            $this->mr->persist($logs);
                            $this->mr->flush();

                            $IsSuccess = false;
                            $dataPayResponse = -1;
                            //if not purchase return money
                            $responseUpdateUtilities = $suyoolServices->UpdateUtilities(0.0, "", $orderupdate1->gettransId());
                            $pushlog->pushLogs(new Logs,"UpdateUtility",@$responseUpdateUtilities[3],@$responseUpdateUtilities[2], @$responseUpdateUtilities[4],@$responseUpdateUtilities[5]);
                            if ($responseUpdateUtilities[0]) {
                                $orderupdate4 = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $suyoolUserId, 'status' => Order::$statusOrder['HELD']]);
                                $orderupdate4
                                    ->setstatus(Order::$statusOrder['CANCELED'])
                                    ->seterror("reversed");
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
                                $messageBack = "Success return money!!";
                                $message = "Success return money!!";
                            } else {
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
                        if (isset($message['Title'])) {
                            $popup = [
                                "Title" => @$message['Title'],
                                "globalCode" => 0,
                                "flagCode" => @$message['ButtonOne']['Flag'],
                                "Message" => @$message['SubTitle'],
                                "isPopup" => true
                            ];
                        } else {
                            $popup = [
                                "Title" => "Error has occured",
                                "globalCode" => 0,
                                "flagCode" => 800,
                                "Message" => "Please try again <br> Error: {$response[3]}",
                                "isPopup" => true
                            ];
                        }
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
                    'message' => "Unauthorize",
                    'data' => [
                        'Popup' => [
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
     * @Route("api/tax", name="ap1_ogero_bill_pay",methods="GET")
     */
    public function checkWebkey(Request $request, BobServices $bobServices, NotificationServices $notificationServices)
    {
        $data = json_decode($request->getContent(), true);
        $webkey = apache_request_headers();
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
}