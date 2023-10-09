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

    public function __construct(ManagerRegistry $mr, SuyoolServices $suyoolServices,NotificationServices $notificationServices,LoggerInterface $loggerInterface)
    {
        $this->mr = $mr->getManager();
        $this->suyoolServices=$suyoolServices;
        $this->notificationServices=$notificationServices;
        $this->logger=$loggerInterface;
    }

    #[Route('/topup', name: 'app_topup')]
    public function index(SessionInterface $sessionInterface)
    {
        $parameters=array();
        $iveriServices=new IveriServices($this->suyoolServices,$this->logger);
           $ivericall=$iveriServices->iveriService();
            if($ivericall[0]){
                $this->mr->persist($ivericall[1]);
                $this->mr->flush();
                return $this->render('iveri/index.html.twig', $ivericall[2]);
            }     
        if (isset($_POST['Request'])) {
            $token = $iveriServices->GenerateTransactionToken("/Lite/Authorise.aspx",$sessionInterface->get('amount') * 100,"it@suyool.com");
            
                $parameters=[
                    'amount'=>$sessionInterface->get('amount'),
                    'currency'=>$sessionInterface->get('currency'),
                    'transactionId'=>$sessionInterface->get('TranSimID'),
                    'userid'=>NULL,
                    'timestamp'=>time(),
                    'topup'=> "false",
                    'token'=>$token
                ];
                $this->suyoolServices->NonSuyoolerTopUpTransaction($sessionInterface->get('TranSimID'));
            return $this->render('iveri/index.html.twig', $parameters);
        }
        if (isset($_POST['infoString'])) {
            if ($_POST['infoString'] == "") return $this->render('ExceptionHandling.html.twig');
            $suyoolUserInfoForTopUp = explode("!#!", $_POST['infoString']);
            $decrypted_string = DecryptService::decrypt($suyoolUserInfoForTopUp[0]);
            $suyoolUserInfo = explode("!#!", $decrypted_string);
            $devicetype = stripos($_SERVER['HTTP_USER_AGENT'], $suyoolUserInfo[1]);
            if ($this->notificationServices->checkUser($suyoolUserInfo[0], $suyoolUserInfo[2]) && $devicetype) {
                $token = $iveriServices->GenerateTransactionToken("/Lite/Authorise.aspx",$suyoolUserInfoForTopUp[1] * 100,"it@suyool.com");
                    $amount = $suyoolUserInfoForTopUp[1];
                    $currency = $suyoolUserInfoForTopUp[2];
                    $userid = $suyoolUserInfo[0];
                    $timestamp = time();
                    $transactionId=$suyoolUserInfoForTopUp[3];
                    $parameters=[
                        'amount'=>$amount,
                        'currency'=>$currency,
                        'userid'=>$userid,
                        'timestamp'=>$timestamp,
                        'transactionId'=>$transactionId,
                        'topup'=>"true",
                        'token'=>$token
                    ];
                return $this->render('iveri/index.html.twig', $parameters);
            } else return $this->render('ExceptionHandling.html.twig');
        } else return $this->render('ExceptionHandling.html.twig');
    }
}
