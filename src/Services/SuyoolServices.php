<?php

namespace App\Services;

use App\Utils\Helper;
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

    /**
     * Push Utility Api
     */
    public function PushUtilities($session, $id, $sum, $currency, $hash_algo, $certificate)
    {
        $Hash = base64_encode(hash($hash_algo, $session . 1 . $id . $sum . $currency . $certificate, true));
        // dd($Hash);

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
        $status = $response->getStatusCode(); // Get the status code
        if($status == 500){
            return array(false,'Internal Server Error');
        }
        if ($status === 400) {
            $push_utility_response = $response->toArray(false);
        }else{
            $push_utility_response = $response->toArray();

        }

        // dd($push_utility_response);

        $globalCode = $push_utility_response['globalCode'];
        $message=$push_utility_response['data'];
        $flagCode=$push_utility_response['flagCode'];


        // $form_data = [
        //     'userAccountID' => $session,
        //     "merchantAccountID" => 1,
        //     'orderID' => $id,
        //     'amount' => $sum,
        //     'currency' => $currency,
        //     'secureHash' =>  $Hash,
        // ];
        // $params['data'] = json_encode($form_data);
        // $params['url'] = 'SuyoolGlobalAPIs/api/Utilities/PushUtilityPayment';
        // /*** Call the api ***/
        // $response = Helper::send_curl($params);
        // $parameters['push_utility_response'] = json_decode($response, true);
        // dd($response);

        if ($globalCode) {
            $transId = $push_utility_response['data'];
            return array(true, $transId);
        } else {
            return array(false,$message,$flagCode);
        }
    }

    /*
     * Update utilities Api  
     */
    public function UpdateUtilities($sum,$hash_algo, $certificate,$additionalData,$transId)
    {
        // dd($additionalData);
        // $additionalDataString = json_encode($additionalData);
        
        // $transId = $this->PushUtilities($session, $id, $sum, $currency, $hash_algo, $certificate);
        // $Hash = base64_encode(hash($hash_algo, $transId[1] . $additionalData . $certificate, true));
        $Hash = base64_encode(hash($hash_algo, $transId . $additionalData . $certificate, true));
        // intval($transId[1])
        // echo $Hash;

        $response = $this->client->request('POST', "{$this->SUYOOL_API_HOST}SuyoolGlobalAPIs/api/Utilities/UpdateUtilityPayment", [
            'body' => json_encode([
                'transactionID' => $transId,
                "amountPaid" => $sum,
                "additionalData" => $additionalData,
                'secureHash' =>  $Hash,
            ]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        $update_utility_response = $response->toArray(false);
        // dd($update_utility_response);
        $globalCode = $update_utility_response['globalCode'];
        $message = $update_utility_response['message'];
        if ($globalCode) {
            return true;
        } else {
            return array(false,$message);
        }
    }
}
