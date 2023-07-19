<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

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
        $res = $ApiResponse['Response'];
        $decodedString = $this->_decodeGzipString(base64_decode($res));
        // dd($decodedString);

        return $decodedString;
        // return $content;
    }

    public function RetrieveResults($currency, $mobileNumber, $Pin)
    {
        $Pin = implode("", $Pin);
        // dd($Pin);

        $response = $this->client->request('POST', $this->BOB_API_HOST . '/RetrieveChannelResults', [
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

        $ApiResponse = json_decode($content, true);
        $res = $ApiResponse['Response'];
        $decodedString = $this->_decodeGzipString(base64_decode($res));
        // dd($decodedString);

        return $decodedString;
        // return $content;
    }

    public function BillPay($Postpaid_With_id_Res)
    {
        // dd($Postpaid_With_id_Res->getCurrency());
        $response = $this->client->request('POST', $this->BOB_API_HOST . '/RetrieveChannelResults', [
            'body' => json_encode([
                "ChannelType" => "API",
                "ItemId" => "1",
                "VenId" => "1",
                "ProductId" => "4",
                "TransactionId" => $Postpaid_With_id_Res->gettransactionId(),

                "AlfaBillResult" => [
                    "Fees" => $Postpaid_With_id_Res->getfees(),
                    "TransactionId" => $Postpaid_With_id_Res->gettransactionId(),
                    "Amount" => $Postpaid_With_id_Res->getamount(),
                    "Amount1" => $Postpaid_With_id_Res->getamount1(),
                    "ReferenceNumber" => $Postpaid_With_id_Res->getreferenceNumber(),
                    "Fees1" => $Postpaid_With_id_Res->getfees1(),
                    "Amount2" => $Postpaid_With_id_Res->getamount2(),
                    "InformativeOriginalWSAmount" => $Postpaid_With_id_Res->getinformativeOriginalWSamount(),
                    "TotalAmount" => $Postpaid_With_id_Res->gettotalamount(),
                    "Currency" => $Postpaid_With_id_Res->getcurrency(),
                    "Rounding" => $Postpaid_With_id_Res->getrounding(),
                    "AdditionalFees" => $Postpaid_With_id_Res->getadditionalfees(),
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

        $content = $response->getContent(false);
        // $content = $response->toArray();
        dd($content);

        $ApiResponse = json_decode($content, true);
        $res = $ApiResponse['Response'];
        $decodedString = $this->_decodeGzipString(base64_decode($res));
        // dd($decodedString);

        return $decodedString;
    }
}
