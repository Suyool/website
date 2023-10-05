<?php

namespace App\Controller;

use App\Entity\Transaction;
use App\Form\iveriFormType;
use App\Service\NotificationServices;
use App\Service\SuyoolServices;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IveriController extends AbstractController
{

    public $cipher_algorithme = "AES128";
    public $key = "SY1X24elh9eG3fpOaHcWlQ9h2bHaqimdIDoyoOaFoi0rukAj3Z";
    public $iv = "fgu26y9e43wc8dj2"; //initiallization vector for decrypt
    private $CURRENCY_LBP;
    private $CURRENCY_USD;
    private $mr;
    private $suyoolServices;
    private $notificationServices;
    private $logger;

    public function __construct(ManagerRegistry $mr, SuyoolServices $suyoolServices,NotificationServices $notificationServices,LoggerInterface $loggerInterface)
    {
        $this->mr = $mr->getManager();
        $this->suyoolServices=$suyoolServices;
        $this->notificationServices=$notificationServices;
        $this->logger=$loggerInterface;
    }

    #[Route('/topup', name: 'app_topup')]
    public function index(Request $request)
    {
        $useragent = $_SERVER['HTTP_USER_AGENT'];
        if (isset($_POST['ECOM_PAYMENT_CARD_PROTOCOLS'])) {
            // dd($_POST);
            $transaction = new Transaction;
            if ($_POST['LITE_PAYMENT_CARD_STATUS'] == 0) {
                $topup=$this->suyoolServices->UpdateCardTopUpTransaction($_POST['TRANSACTIONID'],3);
                if($topup[0]){
                    $amount = number_format($_POST['LITE_ORDER_AMOUNT'] / 100);
                    $_POST['LITE_CURRENCY_ALPHACODE'] == "USD" ? $parameters['currency'] = "$" : $parameters['currency'] = "LL";
                    $parameters['status'] = true;
                    $parameters['imgsrc'] = "build/images/Loto/success.png";
                    $parameters['title'] = "Top Up Successful";
                    $parameters['description'] = "Your wallet has been topped up with {$parameters['currency']} {$amount}. <br>Check your new balance";
                    $parameters['button'] = "Continue";
                }
                else{
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
                $topup=$this->suyoolServices->UpdateCardTopUpTransaction($_POST['TRANSACTIONID'],9);
                if($topup[0]){
                    $parameters['status'] = false;
                    $parameters['imgsrc'] = "build/images/Loto/error.png";
                    $parameters['title'] = "Top Up Failed";
                    $parameters['description'] = "An error has occurred with your top up. <br>Please try again later or use another top up method.";
                    $parameters['button'] = "Try Again";
                }
                else{
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
            $this->mr->persist($transaction);
            $this->mr->flush();
            return $this->render('iveri/index.html.twig', $parameters);
        }

        if (isset($_POST['Request'])) {
                $parameters['amount'] = $_POST['ORDER_AMOUNT'];
                $parameters['currency'] = $_POST['Currency_AlphaCode'];
                $parameters['userid'] = NULL;
                $parameters['timestamp'] = time();
            return $this->render('iveri/index.html.twig', $parameters);
        }
        // $_POST['infoString'] = "Mwx9v3bq3GNGIWBYFJ1f1B/VZbvSmMG/HFhNWN4KAr27gxgh6vEJCjTb6gwJJWxD!#!2!#!USD!#!15580";

        if (isset($_POST['infoString'])) {
            // $this->logger->info($_POST['infoString']);
            // dd($_POST['infoString']);
            $string_to_decrypt = $_POST['infoString'];
            $suyoolUserInfoForTopUp = explode("!#!", $string_to_decrypt);
            if ($_POST['infoString'] == "") return $this->render('ExceptionHandling.html.twig');
            $decrypted_string = openssl_decrypt($suyoolUserInfoForTopUp[0], $this->cipher_algorithme, $this->key, 0, $this->iv);
            $suyoolUserInfo = explode("!#!", $decrypted_string);
            // dd($suyoolUserInfo);
            $devicetype = stripos($useragent, $suyoolUserInfo[1]);
            if ($this->notificationServices->checkUser($suyoolUserInfo[0], $suyoolUserInfo[2]) && $devicetype) {
                    $parameters['amount'] = $suyoolUserInfoForTopUp[1];
                    
                    $parameters['currency'] = $suyoolUserInfoForTopUp[2];
                    $parameters['userid'] = $suyoolUserInfo[0];
                    $parameters['timestamp'] = time();
                    $parameters['transactionId']=$suyoolUserInfoForTopUp[3];
                    $parameters['topup']=true;
                return $this->render('iveri/index.html.twig', $parameters);
            } else return $this->render('ExceptionHandling.html.twig');
        } else return $this->render('ExceptionHandling.html.twig');
    }
}
