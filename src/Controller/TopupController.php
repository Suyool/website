<?php

namespace App\Controller;

use App\Entity\Iveri\orders;
use App\Service\BobPaymentServices;
use App\Service\BobServices;
use App\Service\DecryptService;
use App\Service\IveriServices;
use App\Service\NotificationServices;
use App\Service\SuyoolServices;
use Doctrine\Persistence\ManagerRegistry;
use DOMDocument;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class TopupController extends AbstractController
{

    private $mr;
    private $suyoolServices;
    private $notificationServices;
    private $logger;
    private $sessionInterface;

    public function __construct(ManagerRegistry $mr, SuyoolServices $suyoolServices, NotificationServices $notificationServices, LoggerInterface $loggerInterface, SessionInterface $sessionInterface)
    {
        $this->mr = $mr->getManager('topup');
        $this->suyoolServices = $suyoolServices;
        $this->notificationServices = $notificationServices;
        $this->logger = $loggerInterface;
        $this->sessionInterface = $sessionInterface;
    }

    #[Route('/topup', name: 'app_topup')]
    public function index(SessionInterface $sessionInterface,BobPaymentServices $bobPaymentServices)
    {
        $parameters = array();
        $bobpayment=$bobPaymentServices->paymentGateWay();
        $parameters=[
            'session'=>$bobpayment[1]
        ];
        return $this->render('topup/hiddenForm.html.twig',$parameters);

        // $ivericall = $iveriServices->iveriService($sessionInterface);
        // if ($ivericall[0]) {
        //     $this->mr->persist($ivericall[1]);
        //     $this->mr->flush();
        //     $html = $iveriServices->IveriAuthInfo($this->sessionInterface->get('MerchantTrace'));
        //     $dom = new DOMDocument();
        //     $dom->loadHTML($html);
        //     $form = $dom->getElementsByTagName('form')->item(0);
        //     $formData = [];
        //     foreach ($form->getElementsByTagName('input') as $input) {
        //         $name = $input->getAttribute('name');
        //         $value = $input->getAttribute('value');
        //         $formData[$name] = $value;
        //     }
        //     $_POST = $formData;
        //     $code = $this->sessionInterface->get('Code');
        //     $sender = $this->sessionInterface->get('SenderInitials');
        //     $retrievedata = $iveriServices->retrievedata($this->mr, $code, $sender);
        //     if ($retrievedata[0]) {
        //         if (!is_null($retrievedata[1])) {
        //             $this->mr->persist($retrievedata[1]);
        //             $this->mr->flush();
        //         }
        //         return $this->render('iveri/index.html.twig', $retrievedata[2]);
        //     }
        // }
        // if (isset($_POST['Request'])) {
        //     $nonSuyooler = $this->suyoolServices->NonSuyoolerTopUpTransaction($sessionInterface->get('TranSimID'));
        //     $data = json_decode($nonSuyooler[1], true);
        //     $order = new orders();
        //     $order->settransId($sessionInterface->get('TranSimID'));
        //     $order->setamount($data['TotalAmount']);
        //     $order->setcurrency($data['Currency']);
        //     $order->setstatus(orders::$statusOrder['PENDING']);
        //     $this->mr->persist($order);
        //     $this->mr->flush();
        //     $token = $iveriServices->GenerateTransactionToken("/Lite/Authorise.aspx", $data['TotalAmount'] * 100, "it@suyool.com");
        //     $senderName = $sessionInterface->get('SenderInitials');
        //     $parameters = [
        //         'amount' => $data['TotalAmount'],
        //         'currency' => $data['Currency'],
        //         'transactionId' => $sessionInterface->get('TranSimID'),
        //         'userid' => NULL,
        //         'timestamp' => time(),
        //         'topup' => "false",
        //         'token' => $token,
        //         'merchanttrace' => time() . $sessionInterface->get('TranSimID'),
        //         'senderName' => $senderName,
        //         'codeReq' => $sessionInterface->get('Code')
        //     ];
        //     return $this->render('iveri/index.html.twig', $parameters);
        // }
        // // $_POST['infoString']="fmh1M9oF9lrMsRTdmDc+Om1P0JiMZYj4DuzE6A2MdABCy55LM4VsTfqafInpV8DY!#!2.0!#!USD!#!15791";
        // if (isset($_POST['infoString'])) {
        //     if ($_POST['infoString'] == "") return $this->render('ExceptionHandling.html.twig');
        //     $suyoolUserInfoForTopUp = explode("!#!", $_POST['infoString']);
        //     $decrypted_string = SuyoolServices::decrypt($suyoolUserInfoForTopUp[0]);
        //     $suyoolUserInfo = explode("!#!", $decrypted_string);
        //     $devicetype = stripos($_SERVER['HTTP_USER_AGENT'], $suyoolUserInfo[1]);
        //     if ($this->notificationServices->checkUser($suyoolUserInfo[0], $suyoolUserInfo[2]) && $devicetype) {
        //         $token = $iveriServices->GenerateTransactionToken("/Lite/Authorise.aspx", $suyoolUserInfoForTopUp[1] * 100, "it@suyool.com");
        //         $amount = $suyoolUserInfoForTopUp[1];
        //         $currency = $suyoolUserInfoForTopUp[2];
        //         $userid = $suyoolUserInfo[0];
        //         $timestamp = time();
        //         $transactionId = $suyoolUserInfoForTopUp[3];
        //         $order = new orders();
        //         $order->settransId($transactionId);
        //         $order->setsuyoolUserId($suyoolUserInfo[0]);
        //         $order->setamount($suyoolUserInfoForTopUp[1]);
        //         $order->setcurrency($suyoolUserInfoForTopUp[2]);
        //         $order->setstatus(orders::$statusOrder['PENDING']);
        //         $this->mr->persist($order);
        //         $this->mr->flush();
        //         $parameters = [
        //             'amount' => $amount,
        //             'currency' => $currency,
        //             'userid' => $userid,
        //             'timestamp' => $timestamp,
        //             'transactionId' => $transactionId,
        //             'merchanttrace' => time() . $transactionId,
        //             'topup' => "true",
        //             'token' => $token
        //         ];
        //         return $this->render('iveri/index.html.twig', $parameters);
        //     } else return $this->render('ExceptionHandling.html.twig');
        // } else return $this->render('ExceptionHandling.html.twig');
    }
}
