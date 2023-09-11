<?php

namespace App\Service;

use App\Utils\Helper;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class SuyoolServices
{

    private $SUYOOL_API_HOST;
    private $NOTIFICATION_SUYOOL_HOST;
    private $client;
    private $merchantAccountID;
    private $certificate;
    private $hash_algo;
    private $logger;
    private $METHOD_POST;
    private $METHOD_GET;
    private $helper;


    public function __construct($merchantAccountID, LoggerInterface $logger = null,Helper $helper,ParameterBagInterface $params)
    {
        $this->certificate = $_ENV['CERTIFICATE'];
        $this->hash_algo = $_ENV['ALGO'];
        $this->merchantAccountID = $merchantAccountID;
        $this->METHOD_POST = $params->get('METHOD_POST');
        $this->METHOD_GET = $params->get('METHOD_GET');
        $this->helper = $helper;

        if ($_ENV['APP_ENV'] == 'prod') {
            $this->SUYOOL_API_HOST = 'https://externalservices.nicebeach-895ccbf8.francecentral.azurecontainerapps.io/api/GlobalAPIs/';
            $this->NOTIFICATION_SUYOOL_HOST = "https://suyoolnotificationservice.proudhill-9ff36be4.francecentral.azurecontainerapps.io/";
        } else {
            $this->SUYOOL_API_HOST = 'http://10.20.80.62/SuyoolGlobalAPIs/api/';
            $this->NOTIFICATION_SUYOOL_HOST = "http://10.20.80.62/NotificationServiceApi/";
        }
        $this->client = HttpClient::create();
        $this->logger = $logger;
    }

    /**
     * Push Utility Api
     */
    public function PushUtilities($SuyoolUserId, $id, $sum, $currency)
    {
        $sum = number_format((float) $sum, 1, '.', '');
        $Hash = base64_encode(hash($this->hash_algo, $SuyoolUserId . $this->merchantAccountID . $id . $sum . $currency . $this->certificate, true));
        try {
            $body = [
                'userAccountID' => $SuyoolUserId,
                "merchantAccountID" => $this->merchantAccountID,
                'orderID' => $id,
                'amount' => $sum,
                'currency' => $currency,
                'secureHash' =>  $Hash,
            ];
            $response = $this->helper->clientRequest($this->METHOD_POST, "{$this->SUYOOL_API_HOST}Utilities/PushUtilityPayment",  $body);

            $status = $response->getStatusCode(); // Get the status code
            if ($status == 500) {
                return array(false, 'Internal Server Error');
            }
            if ($status === 400) {
                $push_utility_response = $response->toArray(false);
            } else {
                $push_utility_response = $response->toArray();
            }

            $error = "";
            $globalCode = $push_utility_response['globalCode'];
            $message = $push_utility_response['data'];
            $flagCode = $push_utility_response['flagCode'];
            if (isset($push_utility_response['message'])) {
                $error = $push_utility_response['message'];
            }
            if ($globalCode) {
                $transId = $push_utility_response['data'];
                return array(true, $transId);
            } else {
                return array(false, $message, $flagCode, $error);
            }
        } catch (Exception $e) {
            return array(false, "", "", $e->getMessage());
        }
    }

    /*
     * Update utilities Api  
     */
    public function UpdateUtilities($sum, $additionalData, $transId)
    {
        $sum = number_format((float) $sum, 1, '.', '');
        $Hash = base64_encode(hash($this->hash_algo, $transId . $additionalData . $this->certificate, true));

        try {
            $body = [
                'transactionID' => $transId,
                "amountPaid" => $sum,
                "additionalData" => $additionalData,
                'secureHash' =>  $Hash,
            ];
            $response = $this->helper->clientRequest($this->METHOD_POST, "{$this->SUYOOL_API_HOST}Utilities/UpdateUtilityPayment",  $body);

            $status = $response->getStatusCode(); // Get the status code
            if ($status == 500) {
                return array(false, 'Internal Server Error');
            }

            $update_utility_response = $response->toArray(false);
            $globalCode = $update_utility_response['globalCode'];
            $message = $update_utility_response['message'];
            if ($globalCode) {
                return array(true, "reversed");
            } else {
                return array(false, $message);
            }
        } catch (Exception $e) {
            return array(false, $e->getMessage());
        }
    }

    /*
     * Gettin Suyool Users
     */
    public function GetAllUsers($ChannelID)
    {
        $Hash = base64_encode(hash($this->hash_algo, $ChannelID . $this->certificate, true));
        $response = $this->client->request('POST', "{$this->SUYOOL_API_HOST}User/GetAllUsers", [
            'query' => ['Data' => $Hash],
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        $getAllUsers = $response->toArray(false);
        $dataString = $getAllUsers["data"];
        $dataArray = json_decode($dataString, true);

        return $dataArray;
    }

    /*
     * Gettin Suyool User
     */
    public function GetUser($userId)
    {
        $Hash = base64_encode(hash($this->hash_algo, $userId . $this->certificate, true));
        $body = [
            'userAccountID' => $userId,
            "secureHash" => $Hash,
        ];
        $response = $this->helper->clientRequest($this->METHOD_POST, "{$this->SUYOOL_API_HOST}User/GetUser",  $body);

        $status = $response->getStatusCode(); // Get the status code
        if ($status === 400) {
            $push_get_response = $response->toArray(false);
        } else {
            $push_get_response = $response->toArray();
        }
        $data = json_decode($push_get_response["data"], true);

        return $data;
    }

    /*
     * Push Single Notification
     */
    public function PushSingleNotification($userId, $title, $subject, $body, $notification, $proceedButton, $isInbox, $flag, $notificationType, $isPayment, $isDebit, $additionalData)
    {
        $body = [
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
        ];
        $response = $this->helper->clientRequest($this->METHOD_POST, "{$this->NOTIFICATION_SUYOOL_HOST}Notification/PushSingleNotification",  $body);

        $status = $response->getStatusCode(); // Get the status code
        if ($status === 400) {
            $push_single_response = $response->toArray(false);
        } else {
            $push_single_response = $response->toArray();
        }

        return $push_single_response;
    }

    /*
     * Push Bulk Notification
     */
    public function PushBulkNotification($userId, $title, $subject, $body, $notification, $proceedButton, $isInbox, $flag, $notificationType, $isPayment, $isDebit, $additionalData)
    {
        $body = [
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
        ];
        $response = $this->helper->clientRequest($this->METHOD_POST, "{$this->NOTIFICATION_SUYOOL_HOST}Notification/PushBulkNotification",  $body);

        $status = $response->getStatusCode(); // Get the status code
        if ($status === 400) {
            $push_Bulk_response = $response->toArray(false);
        } else {
            $push_Bulk_response = $response->toArray();
        }

        return $push_Bulk_response;
    }

    public function PaymentDetails($code, $lang)
    {
        $Hash = base64_encode(hash($this->hash_algo, $code . date("ymdHis") . $lang . $this->certificate, true));
        $body = [
            'code' => $code,
            'dateSent' => date("ymdHis"),
            'hash' => $Hash,
            'lang' => $lang
        ];
        $response = $this->helper->clientRequest($this->METHOD_POST, "{$this->SUYOOL_API_HOST}Payment/PaymentDetails",  $body);

        $status = $response->getStatusCode(); // Get the status code
        if ($status === 400) {
            $payment_details_response = $response->toArray(false);
        } else {
            $payment_details_response = $response->toArray();
        }

        return $payment_details_response;
    }

    public function PaymentCashout($TranSimId, $fname, $lname)
    {
        $Hash = base64_encode(hash($this->hash_algo,  $TranSimId . $fname . $lname . $this->certificate, true));

        $body = [
            'transactionId' => $TranSimId,
            'receiverFname' => $fname,
            'hash' => $Hash,
            'receiverLname' => $lname,
        ];
        $response = $this->helper->clientRequest($this->METHOD_POST, "{$this->SUYOOL_API_HOST}NonSuyooler/NonSuyoolerCashOut",  $body);

        $status = $response->getStatusCode(); // Get the status code
        if ($status === 400) {
            $payment_details_response = $response->toArray(false);
        } else {
            $payment_details_response = $response->toArray();
        }

        return $payment_details_response;
    }

    public function RequestDetails($code, $lang)
    {
        $Hash = base64_encode(hash($this->hash_algo, $code . date("ymdHis") . $lang . $this->certificate, true));
        $body = [
            'code' => $code,
            'dateSent' => date("ymdHis"),
            'hash' => $Hash,
            'lang' => $lang
        ];
        $response = $this->helper->clientRequest($this->METHOD_POST, "{$this->SUYOOL_API_HOST}Payment/RequestDetails",  $body);

        $status = $response->getStatusCode(); // Get the status code
        if ($status === 400) {
            $request_details_response = $response->toArray(false);
        } else {
            $request_details_response = $response->toArray();
        }

        return $request_details_response;
    }

    public function PaymentCashin($TranSimId, $fname, $lname)
    {
        $Hash = base64_encode(hash($this->hash_algo,  $TranSimId . $fname . $lname . $this->certificate, true));

        $body = [
            'transactionId' => $TranSimId,
            'receiverFname' => $_POST['fname'],
            'receiverLname' => $_POST['lname'],
            'hash' =>  $Hash
        ];
        $response = $this->helper->clientRequest($this->METHOD_POST, "{$this->SUYOOL_API_HOST}NonSuyooler/NonSuyoolerCashIn",  $body);

        $status = $response->getStatusCode(); // Get the status code
        if ($status === 400) {
            $request_details_response = $response->toArray(false);
        } else {
            $request_details_response = $response->toArray();
        }

        return $request_details_response;
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

        $content = $response->toArray(false);

        return $content;
    }

    public function PushUserPrize($listWinners)
    {
<<<<<<< HEAD

=======
>>>>>>> 79c700f (parameters bag interface)
        try {
            $Hash = base64_encode(hash($this->hash_algo,  json_encode($listWinners, JSON_PRESERVE_ZERO_FRACTION) . $this->certificate, true));
            $this->logger->info(json_encode([
                'listWinners' => $listWinners,
                'secureHash' => $Hash
            ], JSON_PRESERVE_ZERO_FRACTION));
            $response = $this->client->request('POST', "{$this->SUYOOL_API_HOST}Utilities/PushUserPrize", [
                'body' => json_encode([
                    'listWinners' => $listWinners,
                    'secureHash' => $Hash
                ], JSON_PRESERVE_ZERO_FRACTION),
                'headers' => [
                    'Content-Type' => 'application/json'
                ]
            ]);

            $content = $response->toArray(false);

            if ($content['globalCode'] == 1) {
                return array(true, $content['data']);
            } else {
                return array(false);
            }
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
            return array(false);
        }
    }
}
