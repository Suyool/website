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
    public function index()
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
                $parameters=[
                    'amount'=>$_POST['ORDER_AMOUNT'],
                    'currency'=>$_POST['Currency_AlphaCode'],
                    'transactionId'=>$_POST['transactionId'],
                    'userid'=>NULL,
                    'timestamp'=>time(),
                    'topup'=> "false"
                ];
                $this->suyoolServices->NonSuyoolerTopUpTransaction($_POST['transactionId']);
            return $this->render('iveri/index.html.twig', $parameters);
        }
        // $_POST['infoString'] = "Mwx9v3bq3GNGIWBYFJ1f1B/VZbvSmMG/HFhNWN4KAr27gxgh6vEJCjTb6gwJJWxD!#!2!#!USD!#!15580";
        if (isset($_POST['infoString'])) {
            if ($_POST['infoString'] == "") return $this->render('ExceptionHandling.html.twig');
            $suyoolUserInfoForTopUp = explode("!#!", $_POST['infoString']);
            $decrypted_string = DecryptService::decrypt($suyoolUserInfoForTopUp[0]);
            $suyoolUserInfo = explode("!#!", $decrypted_string);
            $devicetype = stripos($_SERVER['HTTP_USER_AGENT'], $suyoolUserInfo[1]);
            if ($this->notificationServices->checkUser($suyoolUserInfo[0], $suyoolUserInfo[2]) && $devicetype) {
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
                        'topup'=>"true"
                    ];
                return $this->render('iveri/index.html.twig', $parameters);
            } else return $this->render('ExceptionHandling.html.twig');
        } else return $this->render('ExceptionHandling.html.twig');
    }
}
