<?php

namespace App\Service;

use App\Entity\Iveri\orders;
use App\Entity\Iveri\trace;
use App\Entity\Iveri\transactions;
use App\Entity\topup\orders as TopupOrders;
use App\Entity\topup\trace as TopupTrace;
use App\Entity\topup\transactions as TopupTransactions;
use DateTime;
use Exception;
use Symfony\Component\HttpClient\HttpClient;

class IveriServices
{
    private $mr;
    private $suyoolServices;
    private $logger;
    private static $applicationId;
    private static $secretKey;

    public function __construct($mr,$suyoolServices, $loggerInterface)
    {
        $this->suyoolServices = $suyoolServices;
        $this->logger = $loggerInterface;
        $this->mr=$mr;
        if ($_ENV['APP_ENV'] == "dev") {
            self::$applicationId = "{A7576A69-DAF9-4ED8-AD7E-8EBB9A13E44E}";
            self::$secretKey = "BsV6TrjgOV0Mw87vgJ7eQ9tPrjdAGYRH";
        } else {
            self::$applicationId = "{67DCBA56-B893-44AD-AC90-DAE0DDB539BA}";
            self::$secretKey = "42GJxBZOrrM9y0aSLzI3MbkrlA0jhdQx";
        }
    }


    public function IveriAuthInfo($merchanttrace)
    {
        try {
            $formData = [
                'Lite_Merchant_ApplicationId' => self::$applicationId,
                'Lite_Merchant_Trace' => $merchanttrace
            ];
            $client = HttpClient::create();
            $response = $client->request('POST', 'https://portal.cscacquiring.com/Lite/AuthoriseInfo.aspx', [
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
                'body' => http_build_query($formData),
            ]);
            $content = $response->getContent();
            return $content;
        } catch (Exception $e) {
            return $this->logger->error($e->getMessage());
        }
    }

    public function iveriService($session)
    {
        $trace = new TopupTrace;
        $transaction = new TopupTransactions;
        $parameters = array();
        if (isset($_POST['ECOM_PAYMENT_CARD_PROTOCOLS'])) {
            $session->set('MerchantTrace', @$_POST['LITE_MERCHANT_TRACE']);
            $session->set(
                "Code",
                isset($_POST['CODEREQ'])
                    ? $_POST['CODEREQ']
                    : ''
            );
            $session->set(
                "SenderInitials",
                isset($_POST['SENDERNAME'])
                    ? $_POST['SENDERNAME']
                    : ''
            );
            if ($_POST['LITE_PAYMENT_CARD_STATUS'] == 0) {
            } else {
            }
            $order=$this->mr->getRepository(orders::class)->findOneBy(['status'=>TopupOrders::$statusOrder['PENDING'],'transId'=>$_POST['TRANSACTIONID']],['created'=>'DESC']);
            $order->setstatus(TopupOrders::$statusOrder['HELD']);
            $trace->setOrders($order);
            $trace->setTrace(@$_POST['LITE_MERCHANT_TRACE']);
            $this->mr->persist($order);
            $this->mr->persist($trace);
            $this->mr->flush();
            $trace=$this->mr->getRepository(trace::class)->findOneBy(['orders'=>$order->getId()]);
            $transaction->setTrace($trace);
            $transaction->setMerchantReference($_POST['MERCHANTREFERENCE']);
            $transaction->setDescription($_POST['LITE_RESULT_DESCRIPTION']);
            $transaction->setRespCode($_POST['LITE_PAYMENT_CARD_STATUS']);
            $transaction->setResponse(json_encode($_POST));
            $transaction->setAuthCode(@$_POST['LITE_ORDER_AUTHORISATIONCODE']);
            $statusForIveri = true;
            $parameters = array(
            );
        } else $statusForIveri = false;

        return array($statusForIveri, $transaction, $parameters);
    }

    public function retrievedata($entity, $code,$sender)
    {
        $parameters = array();
        if (isset($_POST['Lite_Payment_Card_Status'])) {
            $transaction=null;
            if(isset($_POST['MerchantReference'])){
            $transaction = $entity->getRepository(transactions::class)->findOneBy(['merchantReference' => $_POST['MerchantReference'], 'flagCode' => NULL]);
            }
            // dd($transaction->getTrace()->getOrders()->gettransId());
            // dd($transaction);
            $redirect = null;
            $topupforbuttonSuccess = false;
            $topupforbuttonFailed = false;

            if (!is_null($transaction)) {
                $orderSts=TopupOrders::$statusOrder['CANCELED'];
                $additionalInfo = [
                    'authCode' => @$transaction->getAuthCode(),
                    'cardStatus' => $_POST['Lite_Payment_Card_Status'],
                    'desc' => $_POST['Lite_Result_Description']
                ];
                if ($_POST['Lite_Payment_Card_Status'] == 0) {
                    $topup = $this->suyoolServices->UpdateCardTopUpTransaction($transaction->getTrace()->getOrders()->gettransId(), 3, $transaction->getMerchantReference(), (float)$transaction->getTrace()->getOrders()->getamount(), $transaction->getTrace()->getOrders()->getcurrency(), json_encode($additionalInfo));
                    // dd($topup);
                    if ($topup[0]) {
                        if (!is_null($transaction->getTrace()->getOrders()->getsuyoolUserId())) $topupforbuttonSuccess = true;
                        $amount = number_format($transaction->getTrace()->getOrders()->getAmount());
                        $transaction->getTrace()->getOrders()->getCurrency() == "USD" ? $parameters['currency'] = "$" : $parameters['currency'] = "LL";
                        $status = true;
                        $imgsrc = "build/images/Loto/success.png";
                        $title = "Money Added Succesfully";
                        $description = "You have succesfully added {$parameters['currency']} {$amount} to your Suyool wallet. <br>Check your new balance";
                        if (is_null($transaction->getTrace()->getOrders()->getsuyoolUserId())) $description = "you have succesfully added {$parameters['currency']} {$amount} to {$sender}' Suyool wallet.";
                        $button = "Continue";
                        $orderSts=TopupOrders::$statusOrder['COMPLETED'];
                    } else {
                        if (!is_null($transaction->getTrace()->getOrders()->getsuyoolUserId())) $topupforbuttonFailed = true;
                        $status = false;
                        $imgsrc = "build/images/Loto/error.png";
                        $title = "Please Try Again";
                        $description = "An error has occurred with your top up. <br>Please try again later or use another top up method.";
                        $button = "Try Again";
                        if (is_null($transaction->getTrace()->getOrders()->getsuyoolUserId())) $redirect = $code;
                    }
                } else {
                    $topup = $this->suyoolServices->UpdateCardTopUpTransaction($transaction->getTrace()->getOrders()->gettransId(), 9,  $transaction->getMerchantReference(), (float)$transaction->getTrace()->getOrders()->getamount(), $transaction->getTrace()->getOrders()->getcurrency(), json_encode($additionalInfo));
                    // dd($topup);
                    if ($topup[0]) {
                        if (!is_null($transaction->getTrace()->getOrders()->getsuyoolUserId())) $topupforbuttonFailed = true;
                        $status = false;
                        $imgsrc = "build/images/Loto/error.png";
                        $title = "Unable to Add Money";
                        $description = "An error has occurred while adding money. <br>Please try again later or use another method.";
                        if (is_null($transaction->getTrace()->getOrders()->getsuyoolUserId())) $description = "An error has occurred while adding money. <br>Please try again or use another card";
                        $button = "Try Again";
                        if (is_null($transaction->getTrace()->getOrders()->getsuyoolUserId())) $redirect = $code;
                    } else {
                        if (!is_null($transaction->getTrace()->getOrders()->getsuyoolUserId())) $topupforbuttonFailed = true;
                        $status = false;
                        $imgsrc = "build/images/Loto/error.png";
                        $title = "Please Try Again";
                        $description = "An error has occurred while adding money. <br>Please try again later or use another method.";
                        $button = "Try Again";
                        if (is_null($transaction->getTrace()->getOrders()->getsuyoolUserId())) $redirect =$code;
                    }
                }
                $this->logger->info(json_encode($topup));
                $transaction->setflagCode($topup[2]);
                $transaction->setError($topup[3]);
                $order=$transaction->getTrace()->getOrders();
                $order->setstatus($orderSts);
                $statusForIveri = true;
                $parameters = array(
                    'status' => $status,
                    'imgsrc' => $imgsrc,
                    'title' => $title,
                    'description' => $description,
                    'button' => $button,
                    'infoSuccess' => $topupforbuttonSuccess,
                    'infoFailed' => $topupforbuttonFailed,
                    'redirect' => $redirect
                );
            } else {
                $statusForIveri = true;
                $status = false;
                $imgsrc = "build/images/Loto/error.png";
                $title = "Please Try Again";
                $description = "An error has occurred with your top up. <br>Please try again later or use another top up method.";
                $button = "Try Again";
                if ($code != "") $redirect =$code;
            }
            $parameters = array(
                'status' => $status,
                'imgsrc' => $imgsrc,
                'title' => $title,
                'description' => $description,
                'button' => $button,
                'infoSuccess' => $topupforbuttonSuccess,
                'infoFailed' => $topupforbuttonFailed,
                'redirect' => $redirect
            );
        } else $statusForIveri = false;

        return array($statusForIveri, $transaction, $parameters);
    }

    public static function GenerateTransactionToken($resource, $amount,  $emailAddress)
    {
        $time = self::UnixTimeStampUTC();
        $token = self::$secretKey . $time . $resource . self::$applicationId . $amount . $emailAddress;
        return  $time . ":" . self::GetHashSha256($token);
    }

    public static function UnixTimeStampUTC()
    {
        $currentTime = new DateTime('now');
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
