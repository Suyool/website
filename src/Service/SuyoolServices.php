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
    private $winning;
    private $cashout;
    private $cashin;
    private $METHOD_POST = "POST";
    private $helper;

    public function __construct($merchantAccountID = null, LoggerInterface $winning = null, LoggerInterface $cashout = null, LoggerInterface $cashin = null)
    {
        $this->certificate = $_ENV['CERTIFICATE'];
        $this->hash_algo = $_ENV['ALGO'];
        $this->merchantAccountID = $merchantAccountID;

        if ($_ENV['APP_ENV'] == 'prod') {
            $this->SUYOOL_API_HOST = 'https://externalservices.nicebeach-895ccbf8.francecentral.azurecontainerapps.io/api/GlobalAPIs/';
            $this->NOTIFICATION_SUYOOL_HOST = "https://suyoolnotificationservice.proudhill-9ff36be4.francecentral.azurecontainerapps.io/";
        } else {
            $this->SUYOOL_API_HOST = 'http://10.20.80.62/SuyoolGlobalAPIs/api/';
            $this->NOTIFICATION_SUYOOL_HOST = "http://10.20.80.62/NotificationServiceApi/";
        }

        $this->client = HttpClient::create();
        $this->winning = $winning;
        $this->cashin = $cashin;
        $this->cashout = $cashout;
        $this->helper = new Helper($this->client);
    }

    /**
     * Decrypt the webkey for bills security
     */
    public static function decrypt($stringToDecrypt)
    {
        $decrypted_string = openssl_decrypt($stringToDecrypt, $_ENV['CIPHER_ALGORITHME'], $_ENV['DECRYPT_KEY'], 0, $_ENV['INITIALLIZATION_VECTOR']);
        return $decrypted_string;
    }

    public static function aesDecryptString($base64StringToDecrypt) {
            // $decryptedData = openssl_decrypt($base64StringToDecrypt, 'AES128', "hdjs812k389dksd5", 0, $_ENV['INITIALLIZATION_VECTOR']);
            // return $decryptedData;
            try {
                $passphraseBytes = utf8_encode("hdjs812k389dksd5");
                $decryptedData = openssl_decrypt($base64StringToDecrypt, 'AES128', $passphraseBytes, 0, $_ENV['INITIALLIZATION_VECTOR']);
        
                return $decryptedData;
            } catch (Exception $e) {
                return $base64StringToDecrypt;
            }
    }

    /**
     * Push Utility Api
     * @param $suyoolUserId , $id , $sum, $currency, $fees
     */
    public function PushUtilities($SuyoolUserId, $id, $sum, $currency, $fees)
    {
        $sum = number_format((float) $sum, 1, '.', ''); //nuymber format to decimal
        $fees = number_format((float) $fees, 1, '.', ''); //number format to decimal
        $Hash = base64_encode(hash($this->hash_algo, $SuyoolUserId . $this->merchantAccountID . $id . $sum . $fees . $currency . $this->certificate, true));
        try {
            $body = [
                'userAccountID' => $SuyoolUserId,
                "merchantAccountID" => $this->merchantAccountID,
                'orderID' => $id,
                'amount' => $sum,
                'fees' => $fees,
                'currency' => $currency,
                'secureHash' =>  $Hash,
            ];
            $response = $this->helper->clientRequest($this->METHOD_POST, "{$this->SUYOOL_API_HOST}Utilities/PushUtilityPayment",  $body);
            $status = $response->getStatusCode(); // Get the status code
            if ($status == 500) {
                return array(false, 'Internal Server Error');
            }
            if ($status === 400) {
                $push_utility_response = $response->toArray(false); //get the push utility response array even if the status code is 400
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

    /** 
     * Update utilities Api  
     * @param $sum, $additionalData, $transId
     * @return array
     */
    public function UpdateUtilities($sum, $additionalData, $transId)
    {
        $sum = number_format((float) $sum, 1, '.', ''); //to decimal
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
                return array(true, "success");
            } else {
                return array(false, $message);
            }
        } catch (Exception $e) {
            return array(false, $e->getMessage());
        }
    }

    /**
     * Fetch Suyool Users
     * @param $channelID: 
     */
    public function GetAllUsers($channelID)
    {
        $Hash = base64_encode(hash($this->hash_algo, $channelID . $this->certificate, true));
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

    /** 
     * Fetch Suyool User info by id
     * @param $userId
     * @return array information user
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

    /** 
     * Push Single Notification
     * @param userId,title,subject,body,notification,proceedButton,isInbox,flag,notificationType,isPayment,isDebit,additionalData
     * @return array
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

    /** 
     * Push Bulk Notification
     * @param userId,title,subject,body,notification,proceedButton,isInbox,flag,notificationType,isPayment,isDebit,additionalData
     * @return array
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

    /**
     * PaymentDetails using in payment RTP 
     * @param code,lang
     * @return array
     */
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

    /**
     * PaymentCashout using in payment 
     * @param TransSimId,fname,lname
     * @return array
     */
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
        $this->cashout->info(json_encode($payment_details_response));
        return $payment_details_response;
    }

    /**
     * RequestDetails using in RTP 
     * @param code,lang
     * @return array
     */
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
            $request_details_response = $response->toArray(false);
        }
        return $request_details_response;
    }

    /**
     * Cashin using in RTP 
     * @param TransSimId,fname,lname
     * @return array
     */
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
        $this->cashin->info(json_encode($request_details_response));
        return $request_details_response;
    }

    /**
     * Email validation 
     * @param code
     * @return array
     */
    public function ValidateEmail($code)
    {
        $Hash = base64_encode(hash($this->hash_algo, $code . $this->certificate, true));
        $response = $this->client->request('POST', "{$this->SUYOOL_API_HOST}User/ValidateEmail", [
            'body' => json_encode([
                'uniqueCode' => $code,
                'hash' => $Hash
            ]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        $content = $response->toArray(false);
        return $content;
    }

    /**
     * Unsubscribe marketing
     * @param $code , $flag , $key
     */
    public function UnsubscribeMarketing($code, $flag, $key)
    {
        $Hash = base64_encode(hash($this->hash_algo, $code . $flag . $this->certificate, true));
        $replacement_string = str_replace(' ', '+', $key);

        if ($Hash == $replacement_string) {
            $response = $this->client->request('POST', "{$this->SUYOOL_API_HOST}MarketingException/UnsubscribeMarketing", [
                'body' => json_encode([
                    'uniqueCode' => $code,
                    "flag" => $flag,
                    'hash' => $Hash
                ]),
                'headers' => [
                    'Content-Type' => 'application/json'
                ]
            ]);

            $content = $response->toArray(false);
            return $content;
        } else
            return false;
    }

    /**
     * Resubscribe marketing
     * @param $code , $flag
     * @return array 
     */
    public function resubscribeMarketing($code, $flag)
    {
        $Hash = base64_encode(hash($this->hash_algo, $code . $flag . $this->certificate, true));

        $response = $this->client->request('POST', "{$this->SUYOOL_API_HOST}MarketingException/subscribeMarketing", [
            'body' => json_encode([
                'uniqueCode' => $code,
                "flag" => $flag,
                'hash' => $Hash
            ]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);

        $content = $response->toArray(false);
        return $content;
    }

    /**
     * Push user prize for loto winners
     * @param $listWinners
     * @return array
     */
    public function PushUserPrize($listWinners)
    {
        try {
            $Hash = base64_encode(hash($this->hash_algo,  json_encode($listWinners, JSON_PRESERVE_ZERO_FRACTION) . $this->certificate, true));
            $this->winning->info(json_encode([
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
            $this->winning->error($e->getMessage());
            return array(false);
        }
    }

    /**
     * UpdateCardTopUpTransaction for topUp
     * @param $transId, $statusId, $referenceNo, $amount, $currency, $additionalInfo
     * @return array
     */
    public function UpdateCardTopUpTransaction($transId, $statusId, $referenceNo, $amount, $currency, $additionalInfo)
    {
        try {
            $amount = number_format($amount,3,'.','');
            $Hash = base64_encode(hash($this->hash_algo,  $transId . $statusId . $referenceNo . $amount . $currency . $additionalInfo . $this->certificate, true));
            $body =[
                'transactionId' => $transId,
                'statusId' => $statusId,
                'referenceNo'=>$referenceNo,
                'amount'=>$amount,
                'currency'=>$currency,
                'additionalInfo'=>$additionalInfo,
                'secureHash' => $Hash
            ];
            // dd(json_encode($body));
            $this->cashin->info(json_encode($body));
            $response = $this->helper->clientRequest($this->METHOD_POST, "{$this->SUYOOL_API_HOST}Payment/UpdateCardTopUpTransaction",  $body);
            
            $content = $response->toArray(false);
            $this->cashin->info(json_encode($content));
            if ($content['globalCode'] == 1 && $content['flagCode'] == 1) {
                return array(true, $content['data'], $content['flagCode'], $content['message']);
            } else {
                return array(false, $content['data'], $content['flagCode'], $content['message']);
            }
        } catch (Exception $e) {
            return array(false,null,$e->getMessage(),$e->getMessage());
        }
    }

    /**
     * NonSuyoolerTopUpTransaction for topUp
     * @param $transId, $statusId, $referenceNo, $additionalInfo
     * @return array
     */
    public function NonSuyoolerTopUpTransaction($transId, $statusId = null, $referenceNo = null, $additionalInfo = null)
    {
        try {
            $Hash = base64_encode(hash($this->hash_algo,  $transId . $this->certificate, true));
            $body = [
                'transactionId' => $transId,
                'secureHash' => $Hash
            ];

            $response = $this->helper->clientRequest($this->METHOD_POST, "{$this->SUYOOL_API_HOST}NonSuyooler/NonSuyoolerCardTopUp",  $body);
            
            $content = $response->toArray(false);
            if ($content['globalCode'] == 1 && $content['flagCode'] == 1) {
                return array(true, $content['data'], $content['flagCode'], $content['message']);
            } else {
                return array(false, $content['data'], $content['flagCode'], $content['message']);
            }
        } catch (Exception $e) {
            return array(false);
        }
    }

    /**
     * sendDotNetEmail 
     * @param $subject, $to, $plainTextContent, $attachmentName, $attachmentsBase64, $fromEmail, $fromName, $flag, $channelID
     */
    public function sendDotNetEmail($subject, $to, $plainTextContent, $attachmentName, $attachmentsBase64, $fromEmail, $fromName, $flag, $channelID)
    {
        try {
            $Hash = "0e9Q6zJLdoKty9U6OuDZHVas9GisPCfGUFWpFrUq9sfLBgaaY6";
            $emailMessage = [
                'subject' => $subject,
                'to' => $to,
                'plainTextContent' => $plainTextContent,
                'attachment' => []
            ];
            $body = [
                'emailMsg' => [$emailMessage],
                'fromEmail' => $fromEmail,
                'fromName' => $fromName,
                'flag' => $flag,
                'channelID' => $channelID
            ];
            $response = $this->helper->clientRequest($this->METHOD_POST, "{$this->NOTIFICATION_SUYOOL_HOST}Email/SendEmail?Hash=" . $Hash,  $body);
            $content = $response->toArray(false);
            if ($content['statusCode'] == 0) {
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            return array(false);
        }
    }
}
