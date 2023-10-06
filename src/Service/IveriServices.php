<?php

namespace App\Service;

use App\Entity\Transaction;
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
        $parameters=array();
        if (isset($_POST['ECOM_PAYMENT_CARD_PROTOCOLS'])) {
            if ($_POST['LITE_PAYMENT_CARD_STATUS'] == 0) {
                $topup = $this->suyoolServices->UpdateCardTopUpTransaction($_POST['TRANSACTIONID'], 3);
                if ($topup[0]) {
                    $amount = number_format($_POST['LITE_ORDER_AMOUNT'] / 100);
                    $_POST['LITE_CURRENCY_ALPHACODE'] == "USD" ? $parameters['currency'] = "$" : $parameters['currency'] = "LL";
                    $parameters['status'] = true;
                    $parameters['imgsrc'] = "build/images/Loto/success.png";
                    $parameters['title'] = "Top Up Successful";
                    $parameters['description'] = "Your wallet has been topped up with {$parameters['currency']} {$amount}. <br>Check your new balance";
                    $parameters['button'] = "Continue";
                } else {
                    $parameters['status'] = false;
                    $parameters['imgsrc'] = "build/images/Loto/error.png";
                    $parameters['title'] = "Please Try Again";
                    $parameters['description'] = "An error has occurred with your top up. <br>Please try again later or use another top up method.";
                    $parameters['button'] = "Try Again";
                }
                $transaction->setOrderId($_POST['ECOM_CONSUMERORDERID']);
                $transaction->setAmount($_POST['LITE_ORDER_AMOUNT'] / 100);
                $transaction->setCurrency($_POST['LITE_CURRENCY_ALPHACODE']);
                $transaction->setDescription("Successfully payment for " . $_POST['LITE_ORDER_AMOUNT'] / 100 . " " . $_POST['LITE_CURRENCY_ALPHACODE']);
                $transaction->setRespCode($_POST['LITE_PAYMENT_CARD_STATUS']);
                if (isset($_POST['USERID'])) $transaction->setUsersId($_POST['USERID']);
                $transaction->setResponse(json_encode($_POST));
                $transaction->setflagCode($topup[2]);
                $transaction->setError($topup[3]);
            } else {
                $topup = $this->suyoolServices->UpdateCardTopUpTransaction($_POST['TRANSACTIONID'], 9);
                if ($topup[0]) {
                    $parameters['status'] = false;
                    $parameters['imgsrc'] = "build/images/Loto/error.png";
                    $parameters['title'] = "Top Up Failed";
                    $parameters['description'] = "An error has occurred with your top up. <br>Please try again later or use another top up method.";
                    $parameters['button'] = "Try Again";
                } else {
                    $parameters['status'] = false;
                    $parameters['imgsrc'] = "build/images/Loto/error.png";
                    $parameters['title'] = "Please Try Again";
                    $parameters['description'] = "An error has occurred with your top up. <br>Please try again later or use another top up method.";
                    $parameters['button'] = "Try Again";
                }
                $transaction->setOrderId($_POST['ECOM_CONSUMERORDERID']);
                $transaction->setAmount($_POST['LITE_ORDER_AMOUNT'] / 100);
                $transaction->setCurrency($_POST['LITE_CURRENCY_ALPHACODE']);
                $transaction->setDescription("Successfully payment for " . $_POST['LITE_ORDER_AMOUNT'] / 100 . " " . $_POST['LITE_CURRENCY_ALPHACODE']);
                $transaction->setRespCode($_POST['LITE_PAYMENT_CARD_STATUS']);
                if (isset($_POST['USERID'])) $transaction->setUsersId($_POST['USERID']);
                $transaction->setResponse(json_encode($_POST));
                $transaction->setflagCode($topup[2]);
                $transaction->setError($topup[3]);
            }
            $status=true;
        }
        else $status=false;
        
        return array($status,$transaction,$parameters);
    }


}
