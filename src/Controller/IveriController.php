<?php

namespace App\Controller;

use App\Entity\Transaction;
use App\Form\iveriFormType;
use App\Service\DecryptService;
use App\Service\IveriServices;
use App\Service\NotificationServices;
use App\Service\SuyoolServices;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use DOMDocument;
use PDO;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class IveriController extends AbstractController
{

    private $mr;
    private $suyoolServices;
    private $notificationServices;
    private $logger;
    private $sessionInterface;

    public function __construct(ManagerRegistry $mr, SuyoolServices $suyoolServices, NotificationServices $notificationServices, LoggerInterface $loggerInterface, SessionInterface $sessionInterface)
    {
        $this->mr = $mr->getManager();
        $this->suyoolServices = $suyoolServices;
        $this->notificationServices = $notificationServices;
        $this->logger = $loggerInterface;
        $this->sessionInterface = $sessionInterface;
    }

    #[Route('/topup', name: 'app_topup')]
    public function index(SessionInterface $sessionInterface)
    {
        $parameters = array();
        $iveriServices = new IveriServices($this->suyoolServices, $this->logger);
        $ivericall = $iveriServices->iveriService($sessionInterface);
        if ($ivericall[0]) {
            $this->mr->persist($ivericall[1]);
            $this->mr->flush();
            $html = $iveriServices->IveriAuthInfo($this->sessionInterface->get('MerchantTrace'));
            $dom = new DOMDocument();
            $dom->loadHTML($html);
            $form = $dom->getElementsByTagName('form')->item(0);
            $formData = [];
            foreach ($form->getElementsByTagName('input') as $input) {
                $name = $input->getAttribute('name');
                $value = $input->getAttribute('value');
                $formData[$name] = $value;
            }
            $_POST = $formData;
            $code = $this->sessionInterface->get('Code');
            $sender = $this->sessionInterface->get('SenderInitials');
            $retrievedata = $iveriServices->retrievedata($this->mr, $code, $sender);
            if ($retrievedata[0]) {
                if (!is_null($retrievedata[1])) {
                    $this->mr->persist($retrievedata[1]);
                    $this->mr->flush();
                }
                return $this->render('iveri/index.html.twig', $retrievedata[2]);
            }
        }
        if (isset($_POST['Request'])) {
            $nonSuyooler = $this->suyoolServices->NonSuyoolerTopUpTransaction($sessionInterface->get('TranSimID'));
            $data = json_decode($nonSuyooler[1], true);
            $token = $iveriServices->GenerateTransactionToken("/Lite/Authorise.aspx", $data['TotalAmount'] * 100, "it@suyool.com");
            $senderName = $sessionInterface->get('SenderInitials');



            $parameters = [
                'amount' => $data['TotalAmount'],
                'currency' => $data['Currency'],
                'transactionId' => $sessionInterface->get('TranSimID'),
                'userid' => NULL,
                'timestamp' => time(),
                'topup' => "false",
                'token' => $token,
                'merchanttrace' => time() . $sessionInterface->get('TranSimID'),
                'senderName' => $senderName,
                'codeReq' => $sessionInterface->get('Code')
            ];
            return $this->render('iveri/index.html.twig', $parameters);
        }

        // $_POST['infoString']="fmh1M9oF9lrMsRTdmDc+OvfRjRPMWs1smgxX96GPEHMY56ga9HGaBr5k2STgz+5p!#!2.0!#!USD!#!15791";
        if (isset($_POST['infoString'])) {
            if ($_POST['infoString'] == "") return $this->render('ExceptionHandling.html.twig');
            $suyoolUserInfoForTopUp = explode("!#!", $_POST['infoString']);
            $decrypted_string = DecryptService::decrypt($suyoolUserInfoForTopUp[0]);
            $suyoolUserInfo = explode("!#!", $decrypted_string);
            $devicetype = stripos($_SERVER['HTTP_USER_AGENT'], $suyoolUserInfo[1]);
            if ($this->notificationServices->checkUser($suyoolUserInfo[0], $suyoolUserInfo[2]) && $devicetype) {
                $token = $iveriServices->GenerateTransactionToken("/Lite/Authorise.aspx", $suyoolUserInfoForTopUp[1] * 100, "it@suyool.com");
                $amount = $suyoolUserInfoForTopUp[1];
                $currency = $suyoolUserInfoForTopUp[2];
                $userid = $suyoolUserInfo[0];
                $timestamp = time();
                $transactionId = $suyoolUserInfoForTopUp[3];
                $parameters = [
                    'amount' => $amount,
                    'currency' => $currency,
                    'userid' => $userid,
                    'timestamp' => $timestamp,
                    'transactionId' => $transactionId,
                    'merchanttrace' => time() . $transactionId,
                    'topup' => "true",
                    'token' => $token
                ];
                return $this->render('iveri/index.html.twig', $parameters);
            } else return $this->render('ExceptionHandling.html.twig');
        } else return $this->render('ExceptionHandling.html.twig');
    }

    #[Route('/requestToPay', name: 'app_requesttopay')]
    public function requestToPay()
    {
        if ($_ENV['APP_ENV'] == "prod") {
            return $this->render('ExceptionHandling.html.twig');
        }
        $iveriServices = new IveriServices($this->suyoolServices, $this->logger);

        if (isset($_POST['ECOM_PAYMENT_CARD_PROTOCOLS'])) {
            // dd($_SERVER);
            $transaction = new Transaction;
            if ($_POST['LITE_PAYMENT_CARD_STATUS'] == 0) { //successful
                $amount = number_format($_POST['LITE_ORDER_AMOUNT'] / 100);
                $_POST['LITE_CURRENCY_ALPHACODE'] == "USD" ? $parameters['currency'] = "$" : $parameters['currency'] = "LL";
                $parameters['status'] = true;
                $parameters['imgsrc'] = "build/images/Loto/success.png";
                $parameters['title'] = "Top Up Successful";
                $parameters['description'] = "Your wallet has been topped up with {$parameters['currency']} {$amount}. <br>Check your new balance";
                $parameters['button'] = "Continue";
            } else { //failed
                $parameters['status'] = false;
                $parameters['imgsrc'] = "build/images/Loto/error.png";
                $parameters['title'] = "Top Up Failed";
                $parameters['description'] = "An error has occurred with your top up. <br>Please try again later or use another top up method.";
                $parameters['button'] = "Try Again";
            }
            $parameters['info'] = false;
            $transaction->setOrderId($_POST['ECOM_CONSUMERORDERID']);
            $transaction->setAmount($_POST['LITE_ORDER_AMOUNT'] / 100);
            $transaction->setCurrency($_POST['LITE_CURRENCY_ALPHACODE']);
            $transaction->setDescription($_POST['LITE_RESULT_DESCRIPTION']);
            $transaction->setRespCode($_POST['LITE_PAYMENT_CARD_STATUS']);
            $transaction->setResponse(json_encode($_POST));
            $transaction->setflagCode("testing");
            $transaction->setError("testing");
            $transaction->setAuthCode("testing");
            $transaction->setTransactionId(2);

            $this->mr->persist($transaction);
            $this->mr->flush();

            return $this->render('iveri/index.html.twig', $parameters);
        }

        $token = $iveriServices->GenerateTransactionToken("/Lite/Authorise.aspx", 50 * 100, "it@suyool.com");

        $parameters = [
            'amount' => 50,
            'currency' => "USD",
            'timestamp' => time(),
            'topup' => "false",
            'token' => $token
        ];
        return $this->render('iveri/test.html.twig', $parameters);
    }

    #[Route('/data', name: 'app_data')]
    public function testing()
    {
        $iveriServices = new IveriServices($this->suyoolServices, $this->logger);

        $html = $iveriServices->IveriAuthInfo($this->sessionInterface->get('MerchantTrace'));
        $dom = new DOMDocument();
        $dom->loadHTML($html);
        $form = $dom->getElementsByTagName('form')->item(0);
        $formData = [];
        foreach ($form->getElementsByTagName('input') as $input) {
            $name = $input->getAttribute('name');
            $value = $input->getAttribute('value');
            $formData[$name] = $value;
        }
        // dd($formData);
        $_POST = $formData;
        $code = $this->sessionInterface->get('Code');
        $sender = $this->sessionInterface->get('SenderInitials');
        $retrievedata = $iveriServices->retrievedata($this->mr, $code, $sender);
        if ($retrievedata[0]) {
            if (!is_null($retrievedata[1])) {
                $this->mr->persist($retrievedata[1]);
                $this->mr->flush();
            }
            return $this->render('iveri/index.html.twig', $retrievedata[2]);
        }
    }
}
