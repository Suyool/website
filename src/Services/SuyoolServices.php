<?php

namespace App\Services;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class SuyoolServices
{

    private $client;
    private $SUYOOL_API_HOST;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
        if ($_ENV['APP_ENV'] == 'prod') {
            $this->SUYOOL_API_HOST = 'http://10.20.80.62/';
        } else {
            $this->SUYOOL_API_HOST = 'http://10.20.80.62/';
        }
    }

    public function PushUtilities($session, $id, $sum, $currency, $hash_algo, $certificate)
    {
        $Hash = base64_encode(hash($hash_algo, $session . 1 . $id . $sum . $currency . $certificate, true));

        $response = $this->client->request('POST', "{$this->SUYOOL_API_HOST}SuyoolGlobalAPIs/api/Utilities/PushUtilityPayment", [
            'body' => json_encode([
                'userAccountID' => $session,
                "merchantAccountID" => 1,
                'orderID' => $id,
                'amount' => $sum,
                'currency' => $currency,
                'secureHash' =>  $Hash,
            ]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        $push_utility_response = $response->toArray();
        $globalCode = $push_utility_response['globalCode'];
        if($globalCode){
            $transId=$push_utility_response['data'];
            return $transId;
        }else{
            return;
        }
    }

    public function UpdateUtilities($session, $id, $sum, $currency, $hash_algo, $certificate)
    {
        $transId=$this->PushUtilities($session,$id,$sum,$currency,$hash_algo,$certificate);
        $Hash = base64_encode(hash($hash_algo, $transId . "testing" . $certificate, true));

        $response = $this->client->request('POST', "{$this->SUYOOL_API_HOST}SuyoolGlobalAPIs/api/Utilities/UpdateUtilityPayment", [
            'body' => json_encode([
                'transactionID' => $transId,
                "additionalData" => "testing",
                'secureHash' =>  $Hash,
            ]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        $update_utility_response = $response->toArray();
        $globalCode = $update_utility_response['globalCode'];
        if($globalCode){
            return true;
        }else{
            return false;
        }
    }
}
