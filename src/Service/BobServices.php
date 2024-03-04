<?php

namespace App\Service;

use App\Utils\Helper;
use Exception;
use PDO;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

use function Safe\json_encode;

class BobServices
{
    private $BOB_API_HOST;
    private $BOB_PAYMENT_GATEWAY;
    private $USERNAME;
    private $PASSWORD;
    private $helper;
    private $client;
    private $METHOD_POST;
    private $logger;

    public function __construct(HttpClientInterface $client, ParameterBagInterface $params, Helper $helper, LoggerInterface $logger)
    {
        $this->client = $client;
        $this->METHOD_POST = $params->get('METHOD_POST');
        $this->helper = $helper;
        $this->logger = $logger;
        if ($_ENV['APP_ENV'] == 'prod') {
            $this->BOB_API_HOST = 'https://services.bob-finance.com:8445/BoBFinanceAPI/WS/';
            $this->USERNAME = "suyool";
            $this->PASSWORD = "p@123123";
        } else {
            $this->BOB_API_HOST = 'https://185.174.240.230:8445/BoBFinanceAPI/WS/';
            $this->USERNAME = "bobfn";
            $this->PASSWORD = "bobfn";
        }
    }

    private function _decodeGzipString(string $gzipString): string
    {
        // Decode GZip string
        $decodedString = '';
        $decodedData = @gzdecode($gzipString);

        // Check if the decoding was successful
        if ($decodedData !== false) {
            $decodedString = $decodedData;
        }

        return $decodedString;
    }

    //Alfa
    public function Bill($gsmMobileNb)
    {
        try {
            $body = [
                "ChannelType" => "API",
                "AlfaPinParam" => [
                    "GSMNumber" => $gsmMobileNb
                ],
                "Credentials" => [
                    "User" => $this->USERNAME,
                    "Password" => $this->PASSWORD
                ]
            ];
            $response = $this->helper->clientRequest($this->METHOD_POST, $this->BOB_API_HOST . 'SendPinRequest',  $body);
            $url = $this->BOB_API_HOST . 'SendPinRequest';
            $status = $response->getStatusCode(); // Get the status code
            $this->logger->error("Alfa postpaid status: {$status}");
            // if ($status == 500) {
            //     $decodedString = "not connected";
            //     return $decodedString;
            // }
            $content = $response->getContent();


            $ApiResponse = json_decode($content, true);
            // $this->logger->error("Alfa postpaid error: {$ApiResponse}");
            if ($ApiResponse['Response'] == "") {
                $decodedString = $ApiResponse['ErrorDescription'];
            } else {
                $res = $ApiResponse['Response'];
                $decodedString = $this->_decodeGzipString(base64_decode($res));
            }

            return array($decodedString,@$status,$url);
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
            $decodedString = "not connected";
            return $decodedString;
        }
    }

    public function RetrieveResults($currency, $mobileNumber, $Pin)
    {
        try {
            $Pin = implode("", $Pin);
            $body = [
                "ChannelType" => "API",
                "ItemId" => "1",
                "VenId" => "1",
                "ProductId" => "4",
                "AlfaBillMeta" => [
                    "Currency" => $currency,
                    "GSMNumber" => $mobileNumber,
                    "PIN" => $Pin,
                ],
                "Credentials" => [
                    "User" => $this->USERNAME,
                    "Password" => $this->PASSWORD
                ]
            ];
            $response = $this->helper->clientRequest($this->METHOD_POST, $this->BOB_API_HOST . 'RetrieveChannelResults',  $body);
            $status = $response->getStatusCode(); // Get the status code
            $url=$this->BOB_API_HOST . 'RetrieveChannelResults';
            // if ($status == 500) {
            //     return array(false, "error 500 timeout", 255, "error 500 timeout");
            // }
            $content = $response->getContent();
            $reponse = json_encode($content);
            $ApiResponse = json_decode($content, true);
            $res = $ApiResponse['Response'];
            if ($res == "") {
                return array(false,"", $ApiResponse['ErrorDescription'], $ApiResponse['ErrorCode'], $reponse,$url,$status,json_encode($body));
            }
            $decodedString = $this->_decodeGzipString(base64_decode($res));

            return array(true, $decodedString, $ApiResponse['ErrorDescription'], $ApiResponse['ErrorCode'], $reponse,$url,$status,json_encode($body));
        } catch (Exception $e) {
            $this->logger->error("Alfa postpaid error retrieve: {$e->getMessage()}");
            return array(false, $e->getMessage(), 255, $e->getMessage());
        }
    }

    public function BillPay($Postpaid_With_id_Res)
    {
        try {
            $body = [
                "ChannelType" => "API",
                "ItemId" => "1",
                "VenId" => "1",
                "ProductId" => "4",
                "TransactionId" => strval($Postpaid_With_id_Res->gettransactionId()),
                "AlfaBillResult" => [
                    "Fees" => strval($Postpaid_With_id_Res->getfees()),
                    "TransactionId" => $Postpaid_With_id_Res->gettransactionId(),
                    "Amount" => strval($Postpaid_With_id_Res->getamount()),
                    "Amount1" => strval($Postpaid_With_id_Res->getamount1()),
                    "ReferenceNumber" => strval($Postpaid_With_id_Res->getreferenceNumber()),
                    "Fees1" => strval($Postpaid_With_id_Res->getfees1()),
                    "Amount2" => strval($Postpaid_With_id_Res->getamount2()),
                    "InformativeOriginalWSAmount" => strval($Postpaid_With_id_Res->getinformativeOriginalWSamount()),
                    "TotalAmount" => strval($Postpaid_With_id_Res->gettotalamount()),
                    "Currency" => strval($Postpaid_With_id_Res->getcurrency()),
                    "Rounding" => strval($Postpaid_With_id_Res->getrounding()),
                    "AdditionalFees" => strval($Postpaid_With_id_Res->getadditionalfees()),
                ],
                "Credentials" => [
                    "User" => $this->USERNAME,
                    "Password" => $this->PASSWORD
                ]
            ];

            $response = $this->helper->clientRequest($this->METHOD_POST, $this->BOB_API_HOST . 'InjectTransactionalPayment',  $body);
            $status = $response->getStatusCode(); // Get the status code

            // if ($status == 500) {
            //     return array("", "211", "timeout");
            // }
            $content = $response->getContent();

            $txt = json_encode(['response' => $response, 'content' => $content]);
            $this->logger->info("Alfa postpaid Response: {$txt}");
            $ApiResponse = json_decode($content, true);
            $res = $ApiResponse['Response'];
            $ErrorDescription = $ApiResponse['ErrorDescription'];
            $ErrorCode = $ApiResponse['ErrorCode'];
            $decodedString = $this->_decodeGzipString(base64_decode($res));

            return array($decodedString, $ErrorCode, $ErrorDescription,$this->BOB_API_HOST . 'InjectTransactionalPayment',$status,json_encode($body));
        } catch (Exception $e) {
            $this->logger->error("Alfa postpaid error: {$e->getMessage()}");
            return array("", "211", $e->getMessage());
        }
    }

    //Touch
    public function SendTouchPinRequest($gsmMobileNb)
    {
        $body = [
            "ChannelType" => "API",
            "TouchPinParam" => [
                "Service" => "invoice",
                "GSMNumber" => $gsmMobileNb
            ],
            "Credentials" => [
                "User" => $this->USERNAME,
                "Password" => $this->PASSWORD
            ]
        ];
        $response = $this->helper->clientRequest($this->METHOD_POST, $this->BOB_API_HOST . 'SendTouchPinRequest',  $body);

        $content = $response->getContent();
        $ApiResponse = json_decode($content, true);
        if ($ApiResponse["ErrorCode"] == 100) {
            $res = $ApiResponse['Response'];
            $decoded = json_decode($this->_decodeGzipString(base64_decode($res)), true);
            $decodedString = $decoded['token'];
            $isSuccess = true;
            $ErrorDescription = $ApiResponse['ErrorDescription'];
        } else {
            $decodedString = $ApiResponse['Response'];
            $isSuccess = false;
            $ErrorDescription = $ApiResponse['ErrorDescription'];
        }

        return array($isSuccess, $decodedString, $ErrorDescription);
    }

    public function RetrieveResultsTouch($currency, $mobileNumber, $Pin, $token)
    {
        $Pin = implode("", $Pin);
        $body = [
            "ChannelType" => "API",
            "ItemId" => "1",
            "VenId" => "2",
            "ProductId" => "1",
            "TouchDueMeta" => [
                "Currency" => $currency,
                "GSMNumber" => $mobileNumber,
                "PIN" => $Pin,
                "Token" => $token,
            ],
            "Credentials" => [
                "User" => $this->USERNAME,
                "Password" => $this->PASSWORD
            ]
        ];
        $response = $this->helper->clientRequest($this->METHOD_POST, $this->BOB_API_HOST . 'RetrieveChannelResults',  $body);

        $content = $response->getContent();
        $response = json_encode($content);
        $ApiResponse = json_decode($content, true);

        if ($ApiResponse["ErrorCode"] == 100) {
            $res = $ApiResponse['Response'];
            $decodedString = json_decode($this->_decodeGzipString(base64_decode($res)), true);
            $isSuccess = true;
            $ErrorDescription = $ApiResponse['ErrorDescription'];
        } else {
            $decodedString = $ApiResponse['RequestId'];
            $isSuccess = false;
            $ErrorDescription = $ApiResponse['ErrorDescription'];
        }

        return array($isSuccess, $decodedString, $ErrorDescription, $ApiResponse["ErrorCode"], $response);
    }

    public function BillPayTouch($Postpaid_With_id_Res)
    {
        $body = [
            "ChannelType" => "API",
            "ItemId" => "1",
            "VenId" => "2",
            "ProductId" => "1",
            "TransactionId" => strval($Postpaid_With_id_Res->gettransactionId()),
            "TouchDueResult" => [
                "Fees" => strval($Postpaid_With_id_Res->getfees()),
                "transactionId" => $Postpaid_With_id_Res->gettransactionId(),
                "Amount" => strval($Postpaid_With_id_Res->getamount()),
                "Amount1" => strval($Postpaid_With_id_Res->getamount1()),
                "referenceNumber" => strval($Postpaid_With_id_Res->getreferenceNumber()),
                "Fees1" => strval($Postpaid_With_id_Res->getfees1()),
                "Amount2" => strval($Postpaid_With_id_Res->getamount2()),
                "InformativeOriginalWSAmount" => strval($Postpaid_With_id_Res->getinformativeOriginalWSamount()),
                "TotalAmount" => strval($Postpaid_With_id_Res->gettotalamount()),
                "Currency" => strval($Postpaid_With_id_Res->getcurrency()),
                "Rounding" => strval($Postpaid_With_id_Res->getrounding()),
                "AdditionalFees" => strval($Postpaid_With_id_Res->getadditionalfees()),
                "PaymentId" => strval($Postpaid_With_id_Res->getpaymentId()),
                "InvoiceNumber" => strval($Postpaid_With_id_Res->getinvoiceNumber()),
            ],
            "Credentials" => [
                "User" => $this->USERNAME,
                "Password" => $this->PASSWORD
            ]
        ];

        $response = $this->helper->clientRequest($this->METHOD_POST, $this->BOB_API_HOST . 'InjectTransactionalPayment',  $body);
        $content = $response->getContent();
        $this->logger->info(("Bill pay Touch TransId {$Postpaid_With_id_Res->gettransactionId()} Response {$content} "));

        $ApiResponse = json_decode($content, true);

        if ($ApiResponse["ErrorCode"] == 100) {
            $res = $ApiResponse['Response'];
            $decodedString = json_decode($this->_decodeGzipString(base64_decode($res)), true);
            $isSuccess = true;
            $ErrorDescription = $ApiResponse['ErrorDescription'];
        } else {
            $decodedString = $ApiResponse['Response'];
            $isSuccess = false;
            $ErrorDescription = $ApiResponse['ErrorDescription'];
        }

        return array($isSuccess, $decodedString, $ErrorDescription,$content,json_encode($body));
    }

    //Ogero
    public function RetrieveChannelResults($gsmMobileNb)
    {
        $body = [
            "ChannelType" => "API",
            "ItemId" => 1,
            "OgeroMeta" => [
                "PhoneNumber" => $gsmMobileNb
            ],
            "VenId" => 3,
            "ProductId" => 16,
            "Credentials" => [
                "User" => $this->USERNAME,
                "Password" => $this->PASSWORD
            ]
        ];
        $response = $this->helper->clientRequest($this->METHOD_POST, $this->BOB_API_HOST . 'RetrieveChannelResults',  $body);

        $content = $response->getContent();
        $response = json_encode($content);
        $ApiResponse = json_decode($content, true);

        if ($ApiResponse["ErrorCode"] == 100) {
            $res = $ApiResponse['Response'];
            $decodedString = json_decode($this->_decodeGzipString(base64_decode($res)), true);
            $isSuccess = true;
            $ErrorDescription = $ApiResponse['ErrorDescription'];
        } else {
            $decodedString = $ApiResponse['Response'];
            $isSuccess = false;
            $ErrorDescription = $ApiResponse['ErrorDescription'];
        }

        return array($isSuccess, $decodedString, $ErrorDescription, $response);
    }

    public function BillPayOgero($Landline_With_id)
    {
        $body = [
            "ChannelType" => "API",
            "ItemId" => 1,
            "VenId" => 3,
            "ProductId" => 16,
            "TransactionId" => strval($Landline_With_id->gettransactionId()),
            "OgeroResult" => [
                "Amount" => strval($Landline_With_id->getamount()),
                "PayerName" => $Landline_With_id->getogeroClientName(),
                "PayerMobileNumber" => strval($Landline_With_id->getgsmNumber()),
                "Fees" => strval($Landline_With_id->getfees()),
                "Rounding" => strval($Landline_With_id->getrounding()),
                "AdditionalFees" => strval($Landline_With_id->getadditionalFees()),
                "Currency" => strval($Landline_With_id->getcurrency()),
                "TotalAmount" => strval($Landline_With_id->gettotalAmount()),
                "PhoneNumber" => strval($Landline_With_id->getgsmNumber()),
            ],
            "Credentials" => [
                "User" => $this->USERNAME,
                "Password" => $this->PASSWORD
            ]
        ];
        $response = $this->helper->clientRequest($this->METHOD_POST, $this->BOB_API_HOST . 'InjectTransactionalPayment',  $body);

        $myfile = fopen("../var/cache/ogerologs.txt", "a");
        $content = $response->getContent();
        $txt = json_encode(['response' => $response, 'content' => $content]) . " " . date('Y/m/d H:i:s ', time()) . " \n";
        fwrite($myfile, $txt);
        $ApiResponse = json_decode($content, true);
        if ($ApiResponse["ErrorCode"] == 100) {
            $res = $ApiResponse['Response'];
            $decodedString = json_decode($this->_decodeGzipString(base64_decode($res)), true);
            $isSuccess = true;
            $ErrorDescription = $ApiResponse['ErrorDescription'];
        } else {
            $decodedString = $ApiResponse['Response'];
            $isSuccess = false;
            $ErrorDescription = $ApiResponse['ErrorDescription'];
        }

        return array($isSuccess, $decodedString, $ErrorDescription,$content,json_encode($body));
    }
}
