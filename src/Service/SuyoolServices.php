<?php

namespace App\Service;

use App\Utils\Helper;
use Exception;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class SuyoolServices
{

    private $SUYOOL_API_HOST;
    private $NOTIFICATION_SUYOOL_HOST = "http://10.20.80.62/NotificationServiceApi/";
    private $client;
    private $merchantAccountID;
    private $certificate;
    private $hash_algo;

    public function __construct($merchantAccountID)
    {
        $this->certificate = $_ENV['CERTIFICATE'];
        $this->hash_algo = $_ENV['ALGO'];
        $this->merchantAccountID = $merchantAccountID;
        if ($_ENV['APP_ENV'] == 'prod') {
            $this->SUYOOL_API_HOST = 'https://externalservices.nicebeach-895ccbf8.francecentral.azurecontainerapps.io/api/GlobalAPIs/';
        } else {
            $this->SUYOOL_API_HOST = 'http://10.20.80.62/SuyoolGlobalAPIs/api/';
        }
        $this->client = HttpClient::create();
    }

    /**
     * Push Utility Api
     */
    public function PushUtilities($SuyoolUserId, $id, $sum, $currency)
    {

        $Hash = base64_encode(hash($this->hash_algo, $SuyoolUserId . $this->merchantAccountID . $id . $sum . $currency . $this->certificate, true));
        // dd($Hash);
        try {

            $response = $this->client->request('POST', "{$this->SUYOOL_API_HOST}Utilities/PushUtilityPayment", [
                'body' => json_encode([
                    'userAccountID' => $SuyoolUserId,
                    "merchantAccountID" => $this->merchantAccountID,
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
            if ($status == 500) {
                return array(false, 'Internal Server Error');
            }
            if ($status === 400) {
                $push_utility_response = $response->toArray(false);
            } else {
                $push_utility_response = $response->toArray();
            }

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
        } catch (Exception $e) {
            // echo 'Exception occurred: ' . $e->getMessage();
            return array(false, $e->getMessage());
        }
    }

    /*
     * Update utilities Api  
     */
    public function UpdateUtilities($sum, $additionalData, $transId)
    {
        // dd($additionalData);
        // $additionalDataString = json_encode($additionalData);

        // $transId = $this->PushUtilities($session, $id, $sum, $currency, $hash_algo, $certificate);
        // $Hash = base64_encode(hash($hash_algo, $transId[1] . $additionalData . $certificate, true));
        $Hash = base64_encode(hash($this->hash_algo, $transId . $additionalData . $this->certificate, true));
        // intval($transId[1])
        // echo $Hash;
        try {

            $response = $this->client->request('POST', "{$this->SUYOOL_API_HOST}Utilities/UpdateUtilityPayment", [
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

            $status = $response->getStatusCode(); // Get the status code
            if ($status == 500) {
                return array(false, 'Internal Server Error');
            }

            $update_utility_response = $response->toArray(false);
            // echo ($update_utility_response);
            $globalCode = $update_utility_response['globalCode'];
            $message = $update_utility_response['message'];
            if ($globalCode) {
                return array(true);
            } else {
                // dd($globalCode);
                return array(false, $message);
            }
        } catch (Exception $e) {
            // echo 'Exception occurred: ' . $e->getMessage();
            return array(false, $e->getMessage());
        }
    }

    /*
     * Gettin Suyool Users
     */
    public function GetAllUsers($ChannelID)
    {
        $Hash = base64_encode(hash($this->hash_algo, $ChannelID . $this->certificate, true));

        // dd($Hash);
        $response = $this->client->request('POST', "{$this->SUYOOL_API_HOST}User/GetAllUsers", [
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
    public function GetUser($userId)
    {
        $Hash = base64_encode(hash($this->hash_algo, $userId . $this->certificate, true));
        // dd($Hash);
        $response = $this->client->request('POST', "{$this->SUYOOL_API_HOST}User/GetUser", [
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
            $push_get_response = $response->toArray(false);
        } else {
            $push_get_response = $response->toArray();
        }
        // $push_get_response['data']=null;
        $data = json_decode($push_get_response["data"], true);

        return $data;
    }

    /*
     * Push Single Notification
     */
    public function PushSingleNotification($userId, $title, $subject, $body, $notification, $proceedButton, $isInbox, $flag, $notificationType, $isPayment, $isDebit, $additionalData)
    {
        $response = $this->client->request('POST', "{$this->NOTIFICATION_SUYOOL_HOST}Notification/PushSingleNotification", [
            'body' => json_encode([
                'userID' => $userId,
                'title' => $title,
                'subject' => $subject,
                'body' => $body,
                'notification' => $notification,
                'isInbox' => $isInbox,
                'isPayment' => $isPayment,
                'isDebit' => $isDebit,
                'flag' => $flag,
                'additionalData' => $additionalData,
                'notifType' => $notificationType,
                'proceedButton' => $proceedButton,
                'cancelButton' => 'Cancel',
            ]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);

        $status = $response->getStatusCode(); // Get the status code
        if ($status === 400) {
            $push_single_response = $response->toArray(false);
        } else {
            $push_single_response = $response->toArray();
        }

        // dd($push_utility_response);
        // $data = $push_utility_response["message"];

        // $data = json_decode($push_utility_response["message"], true);
        // $data = $push_utility_response["message"];

        return $push_single_response;
    }

    /*
     * Push Bulk Notification
     */
    public function PushBulkNotification($userId, $title, $subject, $body, $notification, $proceedButton, $isInbox, $flag, $notificationType, $isPayment, $isDebit, $additionalData)
    {
        // dd($userId);
        $response = $this->client->request('POST', "{$this->NOTIFICATION_SUYOOL_HOST}Notification/PushBulkNotification", [
            'body' => json_encode([
                'userID' => $userId,
                'title' => $title,
                'subject' => $subject,
                'inboxBody' => $body,
                'notificationBody' => $notification,
                'isInbox' => $isInbox,
                'isPayment' => $isPayment,
                'isDebit' => $isDebit,
                'flag' => $flag,
                'additionalData' => $additionalData,
                'notifType' => $notificationType,
                'proceedButton' => $proceedButton,
                'cancelButton' => 'Cancel',
            ]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);

        $status = $response->getStatusCode(); // Get the status code
        if ($status === 400) {
            $push_Bulk_response = $response->toArray(false);
        } else {
            $push_Bulk_response = $response->toArray();
        }

        // dd($push_Bulk_response);

        // dd($push_utility_response);
        // $data = $push_utility_response["message"];

        // $data = json_decode($push_utility_response["message"], true);
        // $data = $push_utility_response["message"];

        return $push_Bulk_response;
    }

    public function PaymentDetails($code, $lang)
    {
        $Hash = base64_encode(hash($this->hash_algo, $code . date("ymdHis") . $lang . $this->certificate, true));
        // dd($userId);
        $response = $this->client->request('POST', "{$this->SUYOOL_API_HOST}Payment/PaymentDetails", [
            'body' => json_encode([
                'code' => $code,
                'dateSent' => date("ymdHis"),
                'hash' => $Hash,
                'lang' => $lang
            ]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);

        $status = $response->getStatusCode(); // Get the status code
        if ($status === 400) {
            $payment_details_response = $response->toArray(false);
        } else {
            $payment_details_response = $response->toArray();
        }

        // dd($push_Bulk_response);

        // dd($push_utility_response);
        // $data = $push_utility_response["message"];

        // $data = json_decode($push_utility_response["message"], true);
        // $data = $push_utility_response["message"];

        return $payment_details_response;
    }

    public function PaymentCashout($TranSimId, $fname, $lname)
    {
        $Hash = base64_encode(hash($this->hash_algo,  $TranSimId . $fname . $lname . $this->certificate, true));

        // dd(json_encode([
        //     'transactionId'=>$TranSimId,
        //     'receiverFname'=>$fname,
        //     'hash'=>$Hash,
        //     'receiverLname'=>$lname,
        // ]));

        // dd($userId);
        $response = $this->client->request('POST', "{$this->SUYOOL_API_HOST}NonSuyooler/NonSuyoolerCashOut", [
            'body' => json_encode([
                'transactionId' => $TranSimId,
                'receiverFname' => $fname,
                'hash' => $Hash,
                'receiverLname' => $lname,
            ]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);

        $status = $response->getStatusCode(); // Get the status code
        if ($status === 400) {
            $payment_details_response = $response->toArray(false);
        } else {
            $payment_details_response = $response->toArray();
        }

        // dd($push_Bulk_response);

        // dd($push_utility_response);
        // $data = $push_utility_response["message"];

        // $data = json_decode($push_utility_response["message"], true);
        // $data = $push_utility_response["message"];

        return $payment_details_response;
    }

    public function ValidateEmail($code)
    {
        $response = $this->client->request('GET', "{$this->SUYOOL_API_HOST}User/ValidateEmail", [
            'query' => [
                'Data' => $code,
            ],
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);

        $content=$response->toArray();

        return $content;
    }
}
