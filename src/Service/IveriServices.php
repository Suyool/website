<?php

namespace App\Service;

use App\Entity\Transaction;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;

class IveriServices
{
    private $mr;
    private $suyoolServices;
    private $logger;

    public function __construct($suyoolServices, $loggerInterface)
    {
        $this->suyoolServices = $suyoolServices;
        $this->logger = $loggerInterface;
    }

    public function iveriService()
    {

        $transaction = new Transaction;
        $parameters = array();
        if (isset($_POST['ECOM_PAYMENT_CARD_PROTOCOLS'])) {
            dd($_POST);
            $topupforbutton = false;
            if (isset($_POST['USERID'])) $topupforbutton = true;
            $additionalInfo = [
                'authCode' => $_POST['LITE_ORDER_AUTHORISATIONCODE'],
                'cardStatus' => $_POST['LITE_PAYMENT_CARD_STATUS'],
                'desc' => $_POST['LITE_RESULT_DESCRIPTION']
            ];
            if ($_POST['LITE_PAYMENT_CARD_STATUS'] == 0) {
                $topup = $this->suyoolServices->UpdateCardTopUpTransaction($_POST['TRANSACTIONID'], 3, $_POST['ECOM_CONSUMERORDERID'], json_encode($additionalInfo));
                if ($topup[0]) {
                    $amount = number_format($_POST['LITE_ORDER_AMOUNT'] / 100);
                    $_POST['LITE_CURRENCY_ALPHACODE'] == "USD" ? $parameters['currency'] = "$" : $parameters['currency'] = "LL";
                    $status = true;
                    $imgsrc = "build/images/Loto/success.png";
                    $title = "Top Up Successful";
                    $description = "Your wallet has been topped up with {$parameters['currency']} {$amount}. <br>Check your new balance";
                    $button = "Continue";
                } else {
                    $status = false;
                    $imgsrc = "build/images/Loto/error.png";
                    $title = "Please Try Again";
                    $description = "An error has occurred with your top up. <br>Please try again later or use another top up method.";
                    $button = "Try Again";
                }
            } else {
                $topup = $this->suyoolServices->UpdateCardTopUpTransaction($_POST['TRANSACTIONID'], 9, $_POST['ECOM_CONSUMERORDERID'], json_encode($additionalInfo));
                if ($topup[0]) {
                    $status = false;
                    $imgsrc = "build/images/Loto/error.png";
                    $title = "Top Up Failed";
                    $description = "An error has occurred with your top up. <br>Please try again later or use another top up method.";
                    $button = "Try Again";
                } else {
                    $status = false;
                    $imgsrc = "build/images/Loto/error.png";
                    $title = "Please Try Again";
                    $description = "An error has occurred with your top up. <br>Please try again later or use another top up method.";
                    $button = "Try Again";
                }
            }
            $transaction->setOrderId($_POST['ECOM_CONSUMERORDERID']);
            $transaction->setAmount($_POST['LITE_ORDER_AMOUNT'] / 100);
            $transaction->setCurrency($_POST['LITE_CURRENCY_ALPHACODE']);
            $transaction->setDescription($_POST['LITE_RESULT_DESCRIPTION']);
            $transaction->setRespCode($_POST['LITE_PAYMENT_CARD_STATUS']);
            if (isset($_POST['USERID'])) $transaction->setUsersId($_POST['USERID']);
            $transaction->setResponse(json_encode($_POST));
            $transaction->setflagCode($topup[2]);
            $transaction->setError($topup[3]);
            $transaction->setAuthCode($_POST['LITE_ORDER_AUTHORISATIONCODE']);
            $transaction->setTransactionId($_POST['TRANSACTIONID']);
            $statusForIveri = true;
            $parameters = array(
                'status' => $status,
                'imgsrc' => $imgsrc,
                'title' => $title,
                'description' => $description,
                'button' => $button,
                'info' => $topupforbutton
            );
        } else $statusForIveri = false;

        return array($statusForIveri, $transaction, $parameters);
    }

    public static function GenerateTransactionToken($secretKey, $resource,  $applicationId, $amount,  $emailAddress)
    {
        $time = (string)self::UnixTimeStampUTC();
        // $time="1471358394";

        $token = $secretKey . time() . $resource . $applicationId . $amount . $emailAddress;
        echo $token;

        return  time() . ":" . self::GetHashSha256($token);
    }

    public static function UnixTimeStampUTC()
    {
        $currentTime = new DateTime();
        $zuluTime = $currentTime->format('U');
        $unixEpoch = new DateTime("1970-01-01");
        $unixTimeStamp = (int) ($zuluTime - $unixEpoch->format('U'));
        return $unixTimeStamp;
    }

    public static function GetHashSha256($text)
    {
        $bytes = mb_convert_encoding($text, 'ASCII');
        $hash = hash('sha256', $bytes, false);
        $hashString = '';
        foreach (str_split($hash, 2) as $byte) {
            $hashString .= $byte;
        }
        return $hashString;
    }
}
