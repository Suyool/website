<?php

namespace App\Service;

use App\Utils\Helper;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class SuyoolServices1
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
    public function PushUtilities($session, $id, $sum, $currency, $hash_algo, $certificate, $app_id)
    {
        $Hash = base64_encode(hash($hash_algo, $session . $app_id . $id . $sum . $currency . $certificate, true));
        // dd(json_encode([
        //     'userAccountID' => $session,
        //     "merchantAccountID" => $app_id,
        //     'orderID' => $id,
        //     'amount' => $sum,
        //     'currency' => $currency,
        //     'secureHash' =>  $Hash,
        // ]));
        // dd ($Hash);
        $response = $this->client->request('POST', "{$this->SUYOOL_API_HOST}SuyoolGlobalAPIs/api/Utilities/PushUtilityPayment", [
            'body' => json_encode([
                'userAccountID' => $session,
                "merchantAccountID" => $app_id,
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
        if ($status === 400) {
            $push_utility_response = $response->toArray(false);
        } else {
            $push_utility_response = $response->toArray();
        }
        // dd($push_utility_response);
        // dd($push_utility_response);
        $globalCode = $push_utility_response['globalCode'];
        $message = $push_utility_response['data'];
        $flagCode = $push_utility_response['flagCode'];
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
            return array(false, $message, $flagCode);
        }
    }

    /*
     * Update utilities Api
     */
    public function UpdateUtilities($sum, $hash_algo, $certificate, $additionalData, $transId)
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
            return array(false, $message);
        }
    }

    /*
     * Gettin Suyool Users
     */
    public function GetAllUsers($ChannelID, $hash_algo, $certificate)
    {
        $Hash = base64_encode(hash($hash_algo, $ChannelID . $certificate, true));

        // dd($Hash);
        $response = $this->client->request('POST', "{$this->SUYOOL_API_HOST}SuyoolGlobalAPIs/api/User/GetAllUsers", [
            'query' => ['SecureHash' => $Hash],
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        $update_utility_response = $response->toArray(true);

        $dataString = $update_utility_response["data"];
        $dataArray = json_decode($dataString, true);

        return $dataArray;
    }

    /*
     * Gettin Suyool User
     */
    public function GetUser($userId, $hash_algo, $certificate)
    {
        $Hash = base64_encode(hash($hash_algo, $userId . $certificate, true));
        // dd($Hash);
        $response = $this->client->request('POST', "{$this->SUYOOL_API_HOST}SuyoolGlobalAPIs/api/User/GetUser", [
            'body' => json_encode([
                'userAccountID' => $userId,
                "secureHash" => $Hash,
            ]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        $status = $response->getStatusCode(); // Get the status code
        if ($status === 400) {
            $push_utility_response = $response->toArray(false);
        } else {
            $push_utility_response = $response->toArray();
        }
        // dd($push_utility_response);
        $data = json_decode($push_utility_response["data"], true);

        return $data;
    }

    /*
     * Push Single Notification
     */
    public function PushSingleNotification($userId, $title, $subject, $body, $notification,$proceedButton,$isInbox,$flag)
    {
        if($isInbox == 0){
            $inbox = false;
        }else{
            $inbox = true;
        }
        $response = $this->client->request('POST', "{$this->SUYOOL_API_HOST}NotificationServiceApi/Notification/PushSingleNotification", [
            'body' => json_encode([
                'userID' => $userId,
                'title' => $title,
                'subject' => $subject,
                'body' => $body,
                'notification' => $notification,
                // 'body' => 'Test inside body notif',
                // 'notification' => "test outside",
                // 'generator' => '20',
                // 'isNotification' => true,
                'isInbox' => $inbox,
                // 'isInbox' => false,
                'isPayment' => true,
                'isDebit' => true,
                // 'isRequest' => true,
                // 'isMerchant' => true,
                // 'isAuthentication' => true,
                'flag' => $flag,
                // 'flag' => 94,
                'additionalData' => '*14*763737378387#',
                // 'refNo' => 'string',
                // 'imageURL' => 'string',
                'notifType' => 1,
                // 'actionButtons' => [[
                //     'flag' => 0,
                //     'description' => $proceedButton,
                //     'additionalData' => "string",
                //     'isAuthentication' => true,
                // ]],
                'proceedButton' => $proceedButton,
                'cancelButton' => 'Cancel',
            ]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);

        $status = $response->getStatusCode(); // Get the status code
        if ($status === 400) {
            $push_utility_response = $response->toArray(false);
        } else {
            $push_utility_response = $response->toArray();
        }

        // dd($push_utility_response);
        // $data = $push_utility_response["message"];

        // $data = json_decode($push_utility_response["message"], true);
        // $data = $push_utility_response["message"];

        return $push_utility_response;
    }
}
