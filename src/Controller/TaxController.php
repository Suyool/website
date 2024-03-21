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
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class TaxController extends AbstractController
{
    private $mr;
    private $params;
    private $session;
    private $bobServices;
    private $notificationServices;
    private $filesystem;
    private $suyoolServices;

    public function __construct(ManagerRegistry $mr, ParameterBagInterface $params, SessionInterface $sessionInterface, BobServices $bobServices, NotificationServices $notificationServices, Filesystem $filesystem)
    {
        $this->mr = $mr->getManager('tax');
        $this->params = $params;
        $this->session = $sessionInterface;
        $this->bobServices = $bobServices;
        $this->notificationServices = $notificationServices;
        $this->filesystem = $filesystem;
        $this->suyoolServices = new SuyoolServices($params->get('TAX_MERCHANT_ID_PROD'));

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

            if ($this->validateUser($webkeyDecrypted)) {
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
                    $RetrieveChannel = $this->retrieveChannelData($requestData);
                    $this->handleLogs($RetrieveChannel);
                    if ($RetrieveChannel[0] == true) {
                        $taxReq = $this->createTaxRequest($data, $suyoolUserId, $RetrieveChannel);
                        $billPayResponse = $this->billPayApi($suyoolUserId, $taxReq, $data['usersToReceiveNotification']);
                        $billPayData = $billPayResponse->getContent();
                        $billPayDataArray = json_decode($billPayData, true);
                        $parameters = $this->prepareSuccessResponseParameters($taxReq, $RetrieveChannel, $billPayDataArray);
                    } else {
                        $parameters = $this->prepareErrorResponseParameters($data, $RetrieveChannel);
                    }
                } else {
                    $parameters = $this->prepareNoDataParameters();
                }
                return new JsonResponse([
                    'status' => true,
                    'message' => @$parameters['message'],
                    'data' => @$parameters['data']
                ], 200);

            } else {
                return new JsonResponse([
                    'status' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }
        } catch (Exception $e) {
            return new JsonResponse([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function billPayApi($suyoolUserId, $taxReq, $usersToReceiveNotification)
    {
        try {
            $flagCode = null;
            // Initialize order
            $order = $this->initializeOrder($suyoolUserId, $taxReq);
            $orderTst = $this->params->get('TAX_MERCHANT_ID_PROD') ."-". $order->getId() ;

            //Take amount from .net
            $response = $this->suyoolServices->PushUtilities($suyoolUserId, $orderTst, $order->getamount(), $this->params->get('CURRENCY_LBP'), $taxReq->getfees());
            $pushlog = new LogsService($this->mr);
            $pushlog->pushLogs(new Logs, "PushUtility", @$response[4], @$response[5], @$response[7], @$response[6]);

            if ($response[0]) {
                // Set order status to held
                $orderupdate1 = $this->updateOrderStatus($order, $suyoolUserId, $response);
                $requestData = $this->prepareRequestData($taxReq);
                $BillTranPayment = $this->bobServices->BillTranPayment($requestData);
                $pushlog->pushLogs(new Logs, "ap3_tax_bill_inject", @$BillTranPayment[4], @$BillTranPayment[3], @$BillTranPayment[5], @$BillTranPayment[6]);

                if ($BillTranPayment[0]) {
                    //if payment from Bob provider success insert tax data to db
                    $tax = $this->createTaxData($suyoolUserId, $taxReq, $BillTranPayment);
                    $orderupdate = $this->updateOrderAfterPayment($order, $tax,$suyoolUserId);

                    $IsSuccess = true;
                    //intial notification

                    $this->sendInitialNotification($order, $taxReq, $usersToReceiveNotification);

                    $popup = [
                        "Title" => "Tax Bill Paid Successfully",
                        "globalCode" => 0,
                        "flagCode" => 0,
                        "Message" => "You have successfully paid your Tax bill of L.L " . number_format($order->getamount()) . ".",
                        "isPopup" => true
                    ];

                    $responseUpdateUtilities = $this->updateUtilitiesAfterPayment($taxReq,$order,$orderupdate);
                    $pushlog->pushLogs(new Logs, "UpdateUtility", @$responseUpdateUtilities[3], @$responseUpdateUtilities[2], @$responseUpdateUtilities[4], @$responseUpdateUtilities[5]);

                    $message = "Success";
                    $messageBack = "Success";

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
                    }
                } else {
                    $logs = new Logs;
                    $logs->setidentifier("Tax error");
                    $this->mr->persist($logs);
                    $this->mr->flush();

                    $IsSuccess = false;
                    //if not purchase return money
                    $responseUpdateUtilities = $this->suyoolServices->UpdateUtilities(0.0, "", $orderupdate1->gettransId());
                    $pushlog->pushLogs(new Logs, "UpdateUtility", @$responseUpdateUtilities[3], @$responseUpdateUtilities[2], @$responseUpdateUtilities[4], @$responseUpdateUtilities[5]);
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
                $messageBack = $response[3];
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

    private function handleFileUpload($file): ?string
    {
        if ($file instanceof UploadedFile) {
            // Check if the directory exists, if not, create it
            $uploadDirectory = $this->getParameter('kernel.project_dir') . '/resources/TAX';
            if (!$this->filesystem->exists($uploadDirectory)) {
                $this->filesystem->mkdir($uploadDirectory);
            }

            // Move the file to the upload directory
            $newFilename = uniqid() . '.' . $file->guessExtension();
            $file->move($uploadDirectory, $newFilename);
            $filePath = $uploadDirectory . '/' . $newFilename;

            return $filePath;
        }

        return null;
    }

    private function validateUser($webkeyDecrypted): bool
    {
        return $this->notificationServices->checkUser($webkeyDecrypted['merchantId'], $webkeyDecrypted['lang']) && $webkeyDecrypted['devicesType'] == "CORPORATE";
    }

    private function retrieveChannelData($requestData): array
    {
        return $this->bobServices->RetrieveReqResults($requestData);
    }

    private function handleLogs($RetrieveChannel): void
    {
        $pushlog = new LogsService($this->mr);
        $pushlog->pushLogs(new Logs, "ap2_tax_bill", @$RetrieveChannel[7], @$RetrieveChannel[4], @$RetrieveChannel[5], @$RetrieveChannel[6]);
    }

    private function createTaxRequest($data, $suyoolUserId, $RetrieveChannel): TaxRequest
    {
        $taxReq = new TaxRequest();
        $resp = $RetrieveChannel[1]["Values"];
        $displayedFees = intval($resp["Fees"]) + intval($resp["Fees1"]) + intval($resp["AdditionalFees"]);
        $taxReq
            ->setsuyoolUserId($suyoolUserId)
            ->setCompanyName($data["companyName"])
            ->setDocumentNumber($data["documentNumber"])
            ->setPickerName($data['pickerName'])
            ->setPickerNumber($data['pickerNumber'])
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

        if (isset($data['uploadedFile'])) {
            $file = $this->handleFileUpload($data['uploadedFile']);
            if ($file) {
                $taxReq->setUploadedFile($file);
            }
        }

        $this->mr->persist($taxReq);
        $this->mr->flush();

        return $taxReq;
    }

    private function prepareSuccessResponseParameters($taxReq, $RetrieveChannel, $billPayDataArray): array
    {
        $taxReqId = $taxReq->getId();
        $resp = $RetrieveChannel[1]["Values"];
        $displayedFees = intval($resp["Fees"]) + intval($resp["Fees1"]) + intval($resp["AdditionalFees"]);
        $message = $billPayDataArray['data']['message'];
        $isSuccess = $billPayDataArray['data']['IsSuccess'];
        $flagCode = $billPayDataArray['data']['flagCode'];
        $popup = $billPayDataArray['data']['Popup']; // Add this line to retrieve the Popup

        return [
            'messageBack' => "Success",
            'message' => $message,
            'documentNumber' => $taxReq->getDocumentNumber(),

            'data' => [
                'documentNumber' => $taxReq->getDocumentNumber(),
                'displayBill' => $resp,
                'TaxReqId' => $taxReqId,
                'displayedFees' => $displayedFees,
                'IsSuccess' => $isSuccess,
                'flagCode' => $flagCode,
                'Popup' => $popup, // Include the Popup in the data array
            ]
        ];
    }

    private function prepareErrorResponseParameters($data, $RetrieveChannel): array
    {
        $error = explode("-", $RetrieveChannel[2]);
        switch ($error[1]) {
            case 111:
            case 108:
                $title = "No Pending Bill";
                $body = "There is no pending bill on the document number {$data["documentNumber"]}<br/>Kindly try again later";
                break;
            case 112:
            default:
                $title = "Document Not Found";
                $body = "The Document you entered was not found in the system.<br>Kindly try another Document.";
                break;
        }
        $popup = [
            "Title" => @$title,
            "globalCode" => 0,
            "flagCode" => 800,
            "Message" => @$body,
            "isPopup" => true
        ];
        return [
            'messageBack' => $RetrieveChannel[2],
            'parameters' => [
                'Popup' => $popup
            ]
        ];
    }

    private function prepareNoDataParameters(): array
    {
        return [
            'message' => "not connected",
            'TaxReqId' => -1,
            'documentNumber' => -1,
        ];
    }

    private function initializeOrder($suyoolUserId, $taxReq)
    {
        $order = new Order();
        $order
            ->setsuyoolUserId($suyoolUserId)
            ->settransId(null)
            ->setTaxId(null)
            ->setstatus(Order::$statusOrder['PENDING'])
            ->setamount($taxReq->getamount() + $taxReq->getfees())
            ->setfees($taxReq->getfees())
            ->setcurrency("LBP");
        $this->mr->persist($order);
        $this->mr->flush();
        return $order;
    }

    private function updateOrderStatus($order, $suyoolUserId, $response)
    {
        $orderupdate1 = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $suyoolUserId, 'status' => Order::$statusOrder['PENDING']]);
        $orderupdate1
            ->settransId($response[1])
            ->setstatus(Order::$statusOrder['HELD']);
        $this->mr->persist($orderupdate1);
        $this->mr->flush();
        return $orderupdate1;
    }

    private function prepareRequestData($taxReq)
    {
        return [
            "ChannelType" => "API",
            "ItemId" => 2497,
            "VenId" => 20,
            "ProductId" => 25,
            "TransactionId" => strval($taxReq->gettransactionId()),
            "TaxResult" => [
                "Amount" => strval($taxReq->getamount()),
                "DocumentNumber" => strval($taxReq->getDocumentNumber()),
                "Fees" => strval($taxReq->getfees()),
                "TotalAmount" => strval($taxReq->gettotalAmount()),
                "AdditionalFees" => strval($taxReq->getadditionalFees()),
            ],
        ];
    }

    private function createTaxData($suyoolUserId, $taxReq, $BillTranPayment)
    {
        $tax = new Tax();
        $tax
            ->setsuyoolUserId($suyoolUserId)
            ->setDocumentNumber($taxReq->getDocumentNumber())
            ->settransactionId($taxReq->gettransactionId())
            ->setreferenceNumber($BillTranPayment[1]["TransactionReference"])
            ->setPayerName($taxReq->getCompanyName())
            ->setdisplayedFees($taxReq->getdisplayedFees())
            ->setcurrency($taxReq->getcurrency())
            ->setamount($taxReq->getamount())
            ->setamount1($taxReq->getamount1())
            ->setamount2($taxReq->getamount2())
            ->settotalAmount($taxReq->gettotalAmount())
            ->setMoFTotalAmount($taxReq->getMoFTotalAmount())
            ->setadditionalFees($taxReq->getadditionalFees())
            ->setfees($taxReq->getfees())
            ->setfees1($taxReq->getfees1())
            ->setrounding($taxReq->getrounding());
        $this->mr->persist($tax);
        $this->mr->flush();
        return $tax;
    }

    private function updateOrderAfterPayment($order, $tax,$suyoolUserId)
    {
        $orderupdate = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $suyoolUserId, 'status' => Order::$statusOrder['HELD']]);
        $orderupdate
            ->setTaxId($tax)
            ->setstatus(Order::$statusOrder['PURCHASED']);
        $this->mr->persist($orderupdate);
        $this->mr->flush();

        return $orderupdate;
    }

    private function sendInitialNotification($order, $taxReq, $usersToReceiveNotification)
    {
        $params = json_encode([
            'amount' => number_format($order->getamount()),
            'currency' => "L.L",
            'documentNumber' => $taxReq->getDocumentNumber(),
            'name' => $taxReq->getCompanyName()
        ]);
        $additionalData = "";

        $content = $this->notificationServices->getContent('AcceptedOgeroPaymentCorporate');
        $bulk = 1; //1 for broadcast 0 for unicast
        $this->notificationServices->addNotification($usersToReceiveNotification, $content, $params, $bulk, $additionalData);
    }

    private function updateUtilitiesAfterPayment($taxReq,$order,$orderupdate) {
        $updateUtilitiesAdditionalData = json_encode([
            'Amount' => $taxReq->getamount(),
            'Amount1' => $taxReq->getamount1(),
            'Amount2' => $taxReq->getamount2(),
            'TransactionId' => $taxReq->gettransactionId(),
            'Fees' => $taxReq->getfees(),
            'Fees1' => $taxReq->getfees1(),
            'TotalAmount' => $taxReq->gettotalAmount(),
            'Currency' => $taxReq->getcurrency(),
            'Rounding' => $taxReq->getrounding(),
            'AdditionalFees' => $taxReq->getadditionalFees(),
            'documentNumber' => $taxReq->getDocumentNumber()
        ]);

        //tell the .net that total amount is paid
        $responseUpdateUtilities = $this->suyoolServices->UpdateUtilities($order->getamount(), $updateUtilitiesAdditionalData, $orderupdate->gettransId());

        return $responseUpdateUtilities;
    }
}