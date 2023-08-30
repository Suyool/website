<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

use function Safe\json_encode;

class BobServices
{
    private $BOB_API_HOST;
    private $USERNAME;
    private $PASSWORD;

    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;

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
        $response = $this->client->request('POST', $this->BOB_API_HOST . 'SendPinRequest', [
            'body' => json_encode([
                "ChannelType" => "API",
                "AlfaPinParam" => [
                    "GSMNumber" => $gsmMobileNb
                    // "GSMNumber" => "70102030"
                    // "GSMNumber" => "03184740"
                ],
                "Credentials" => [
                    "User" => $this->USERNAME,
                    "Password" => $this->PASSWORD
                ]
            ]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);

        $content = $response->getContent();
        // $content = $response->toArray();
        // dd($content);

        $ApiResponse = json_decode($content, true);
        if($ApiResponse['Response'] == ""){
            $decodedString=$ApiResponse['ErrorDescription'];
        }else{
            $res = $ApiResponse['Response'];
            $decodedString = $this->_decodeGzipString(base64_decode($res));
        }
        // dd($decodedString);

        return $decodedString;
        // return $content;
    }

    public function RetrieveResults($currency, $mobileNumber, $Pin)
    {
        $Pin = implode("", $Pin);
        // dd($Pin);

        $response = $this->client->request('POST', $this->BOB_API_HOST . 'RetrieveChannelResults', [
            'body' => json_encode([
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
            ]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);

        $content = $response->getContent();
        // $content = $response->toArray();
        // dd($content);
        $reponse=json_encode($content);

        $ApiResponse = json_decode($content, true);
        // dd($ApiResponse);
        $res = $ApiResponse['Response'];
        if($res==""){
            return array(false,$ApiResponse['ErrorDescription'],$ApiResponse['ErrorCode'],$reponse);
        }
        $decodedString = $this->_decodeGzipString(base64_decode($res));
        // dd($decodedString);

        return array(true,$decodedString,$ApiResponse['ErrorDescription'],$ApiResponse['ErrorCode'],$reponse);
        // return $content;
    }

    public function BillPay($Postpaid_With_id_Res)
    {
        // dd($Postpaid_With_id_Res->getCurrency());
        $response = $this->client->request('POST', $this->BOB_API_HOST . 'InjectTransactionalPayment', [
            'body' => json_encode([
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
            ]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);

        $content = $response->getContent();

        $ApiResponse = json_decode($content, true);
        // dd($ApiResponse);
        $res = $ApiResponse['Response'];
        $decodedString = $this->_decodeGzipString(base64_decode($res));

        return $decodedString;
    }

    //Touch
    public function SendTouchPinRequest($gsmMobileNb)
    {
        $response = $this->client->request('POST', $this->BOB_API_HOST . 'SendTouchPinRequest', [
            'body' => json_encode([
                "ChannelType" => "API",
                "TouchPinParam" => [
                    "Service" => "invoice",
                    "GSMNumber" => $gsmMobileNb
                    // "GSMNumber" => "70102030"
                ],
                "Credentials" => [
                    "User" => $this->USERNAME,
                    "Password" => $this->PASSWORD
                ]
            ]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);

        $content = $response->getContent();

        $ApiResponse = json_decode($content, true);
        // dd($ApiResponse);
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
        // dd($Pin);

        $response = $this->client->request('POST', $this->BOB_API_HOST . 'RetrieveChannelResults', [
            'body' => json_encode([
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
            ]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);

        $content = $response->getContent();
        $response=json_encode($content);
        $ApiResponse = json_decode($content, true);

        // dd($ApiResponse);
        if ($ApiResponse["ErrorCode"] == 100) {
            $res = $ApiResponse['Response'];
            $decodedString = json_decode($this->_decodeGzipString(base64_decode($res)), true);
            // $decodedString = $decoded['token'];
            $isSuccess = true;
            $ErrorDescription = $ApiResponse['ErrorDescription'];
        } else {
            $decodedString = $ApiResponse['RequestId'];
            $isSuccess = false;
            $ErrorDescription = $ApiResponse['ErrorDescription'];
        }

        return array($isSuccess, $decodedString, $ErrorDescription,$ApiResponse["ErrorCode"],$response);
    }

    public function BillPayTouch($Postpaid_With_id_Res)
    {
        // dd($Postpaid_With_id_Res->getCurrency());
        $response = $this->client->request('POST', $this->BOB_API_HOST . 'InjectTransactionalPayment', [
            'body' => json_encode([
                "ChannelType" => "API",
                "ItemId" => "1",
                "VenId" => "2",
                "ProductId" => "2",
                "TransactionId" => strval($Postpaid_With_id_Res->gettransactionId()),

                "TouchAdvancedResult" => [
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
            ]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);

        $content = $response->getContent();

        $ApiResponse = json_decode($content, true);
        // dd($ApiResponse);
        // $res = $ApiResponse['Response'];
        // $decodedString = $this->_decodeGzipString(base64_decode($res));

        if ($ApiResponse["ErrorCode"] == 100) {
            $res = $ApiResponse['Response'];
            $decodedString = json_decode($this->_decodeGzipString(base64_decode($res)), true);
            // $decodedString = $decoded['token'];
            $isSuccess = true;
            $ErrorDescription = $ApiResponse['ErrorDescription'];
        } else {
            $decodedString = $ApiResponse['Response'];
            $isSuccess = false;
            $ErrorDescription = $ApiResponse['ErrorDescription'];
        }

        return array($isSuccess, $decodedString, $ErrorDescription);

        // return $decodedString;
    }

    //Ogero
    public function RetrieveChannelResults($gsmMobileNb)
    {
        $response = $this->client->request('POST', $this->BOB_API_HOST . 'RetrieveChannelResults', [
            'body' => json_encode([
                "ChannelType" => "API",
                "ItemId" => 1,
                "OgeroMeta" => [
                    "PhoneNumber" => $gsmMobileNb
                    // "GSMNumber" => "70102030"
                    // "GSMNumber" => "03184740"
                ],
                "VenId" => 3,
                "ProductId" => 16,
                "Credentials" => [
                    "User" => $this->USERNAME,
                    "Password" => $this->PASSWORD
                ]
            ]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        $content = $response->getContent();

        $response=json_encode($content);

        $ApiResponse = json_decode($content, true);
    

        if ($ApiResponse["ErrorCode"] == 100) {
            $res = $ApiResponse['Response'];
            $decodedString = json_decode($this->_decodeGzipString(base64_decode($res)), true);
            // $decodedString = $decoded['token'];
            $isSuccess = true;
            $ErrorDescription = $ApiResponse['ErrorDescription'];
        } else {
            $decodedString = $ApiResponse['Response'];
            $isSuccess = false;
            $ErrorDescription = $ApiResponse['ErrorDescription'];
        }

        return array($isSuccess, $decodedString, $ErrorDescription,$response);
    }

    public function BillPayOgero($Landline_With_id)
    {
        // dd(strval($Landline_With_id->getgsmNumber()));
        $response = $this->client->request('POST', $this->BOB_API_HOST . 'InjectTransactionalPayment', [
            'body' => json_encode([
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
            ]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);

        $myfile = fopen("../var/cache/ogerologs.txt", "a");
       

        $content = $response->getContent();

        $txt=json_encode(['response'=>$response,'content'=>$content]);
        fwrite($myfile, $txt);

        $ApiResponse = json_decode($content, true);

        // dd($ApiResponse);

        if ($ApiResponse["ErrorCode"] == 100) {
            $res = $ApiResponse['Response'];
            $decodedString = json_decode($this->_decodeGzipString(base64_decode($res)), true);
            // $decodedString = $decoded['token'];
            $isSuccess = true;
            $ErrorDescription = $ApiResponse['ErrorDescription'];
        } else {
            $decodedString = $ApiResponse['Response'];
            $isSuccess = false;
            $ErrorDescription = $ApiResponse['ErrorDescription'];
        }

        return array($isSuccess, $decodedString, $ErrorDescription);
    }
}
