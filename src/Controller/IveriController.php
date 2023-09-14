<?php

namespace App\Controller;

use App\Entity\Transaction;
use App\Form\iveriFormType;
use App\Service\NotificationServices;
use App\Service\SuyoolServices;
use Doctrine\Persistence\ManagerRegistry;
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

    public function __construct(ManagerRegistry $mr,SuyoolServices $suyoolServices,NotificationServices $notificationServices)
    {
        $this->mr=$mr->getManager();
        $this->notificationServices=$notificationServices;
    }

    #[Route('/topup', name: 'app_topup')]
    public function index(Request $request)
    {
        $useragent = $_SERVER['HTTP_USER_AGENT'];
        if(isset($_POST['ECOM_PAYMENT_CARD_PROTOCOLS'])){
            // dd($_POST);
            $transaction=new Transaction;
            if($_POST['LITE_PAYMENT_CARD_STATUS'] == 0){
                $transaction->setOrderId($_POST['ECOM_CONSUMERORDERID']);
                $transaction->setAmount($_POST['LITE_ORDER_AMOUNT']/100);
                $transaction->setCurrency($_POST['LITE_CURRENCY_ALPHACODE']);
                $transaction->setDescription("Successfully payment for " . $_POST['LITE_ORDER_AMOUNT']/100 . " " . $_POST['LITE_CURRENCY_ALPHACODE']);
                $transaction->setRespCode($_POST['LITE_PAYMENT_CARD_STATUS']);
                if(isset($_POST['USERID'])) $transaction->setUsersId($_POST['USERID']); 
                $transaction->setResponse(json_encode($_POST));
                $parameters['message']="Successfully payment for " . number_format($_POST['LITE_ORDER_AMOUNT']/100) . " " . $_POST['LITE_CURRENCY_ALPHACODE'];
            }else{
                $transaction->setOrderId($_POST['ECOM_CONSUMERORDERID']);
                $transaction->setAmount($_POST['LITE_ORDER_AMOUNT']/100);
                $transaction->setCurrency($_POST['LITE_CURRENCY_ALPHACODE']);
                $transaction->setDescription($_POST['LITE_RESULT_DESCRIPTION']);
                $transaction->setRespCode($_POST['LITE_PAYMENT_CARD_STATUS']);
                if(isset($_POST['USERID'])) $transaction->setUsersId($_POST['USERID']);
                $transaction->setResponse(json_encode($_POST));
                $parameters['message']="Invalid card please try again";
            }
            $this->mr->persist($transaction);
            $this->mr->flush();
            return $this->render('iveri/index.html.twig',$parameters);
        }
        // $_POST['infoString'] = "3mzsXlDm5DFUnNVXA5Pu8T1d5nNACEsiiUEAo7TteE/x3BGT3Oy3yCcjUHjAVYk3";

        if(isset($_POST['infoString'])){
            $string_to_decrypt = $_POST['infoString'];
            if ($_POST['infoString'] == "") return $this->render('ExceptionHandling.html.twig');
            $decrypted_string = openssl_decrypt($string_to_decrypt, $this->cipher_algorithme, $this->key, 0, $this->iv);
            $suyoolUserInfo = explode("!#!", $decrypted_string);
            $devicetype = stripos($useragent, $suyoolUserInfo[1]);
            if ($this->notificationServices->checkUser($suyoolUserInfo[0], $suyoolUserInfo[2]) && $devicetype) {
                $userdetails=$this->notificationServices->GetuserDetails($suyoolUserInfo[0]);
                $parameters['fname']=$userdetails[0];
                $parameters['lname']=$userdetails[1];
                $transaction = new Transaction();

                $form = $this->createForm(iveriFormType::class, $transaction);
                $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $transaction = $form->getData();
            $parametersToHiddenForm['amount']=$transaction->getAmount();
            $parametersToHiddenForm['currency']=$transaction->getCurrency();
            $parametersToHiddenForm['userid']=$suyoolUserInfo[0];

            return $this->render('iveri/hiddenForm.html.twig',$parametersToHiddenForm);

        }
                $parameters['form']=$form->createView();

                return $this->render('iveri/index.html.twig',$parameters);
            } else return $this->render('ExceptionHandling.html.twig');
           
        } else return $this->render('ExceptionHandling.html.twig');
    }

}
