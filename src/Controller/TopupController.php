<?php

namespace App\Controller;

use App\Entity\Iveri\orders;
use App\Entity\topup\attempts;
use App\Entity\topup\blackListCards;
use App\Service\BobPaymentServices;
use App\Service\BobServices;
use App\Service\InvoiceServices;
use App\Service\IveriServices;
use App\Service\NotificationServices;
use App\Service\SuyoolServices;
use Doctrine\Persistence\ManagerRegistry;
use DOMDocument;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class TopupController extends AbstractController
{

    private $mr;
    private $suyoolServices;
    private $notificationServices;
    private $logger;
    private $sessionInterface;
    private $hash_algo;

    public function __construct(ManagerRegistry $mr, SuyoolServices $suyoolServices, NotificationServices $notificationServices, LoggerInterface $loggerInterface, SessionInterface $sessionInterface)
    {
        $this->mr = $mr->getManager('topup');
        $this->suyoolServices = $suyoolServices;
        $this->notificationServices = $notificationServices;
        $this->logger = $loggerInterface;
        $this->sessionInterface = $sessionInterface;
        $this->hash_algo = $_ENV['ALGO'];
    }

    // #[Route('/topup', name: 'app_topup')]
    // public function index(Request $request, SessionInterface $sessionInterface, BobPaymentServices $bobPaymentServices)
    // {
    //     try {
    //         // $this->suyoolServices->UpdateCardTopUpTransaction(12764,3,"12764","3990000.00","LBP","8367");
    //         $bobRetrieveResultSession = $bobPaymentServices->RetrievePaymentDetails($sessionInterface->get('suyooler'));
    //         if ($bobRetrieveResultSession[0] == true) {
    //             $sessionInterface->remove('order');
    //             if ($bobRetrieveResultSession[1]['status'] != "CAPTURED") {
    //                 echo '<script type="text/javascript">',
    //                 ' if (navigator.userAgent.match(/Android/i)) {
    //                         window.AndroidInterface.callbackHandler("GoToApp");
    //                       } else {
    //                         window.webkit.messageHandlers.callbackHandler.postMessage("GoToApp");
    //                       }',
    //                 '</script>';
    //             } else {
    //                 $topUpData = $bobPaymentServices->retrievedataForTopUp($bobRetrieveResultSession[1]['authenticationStatus'], $bobRetrieveResultSession[1]['status'], $sessionInterface->get('indicator'), $bobRetrieveResultSession[1], $sessionInterface->get('transId'), $sessionInterface->get('suyooler'), $bobRetrieveResultSession[1]['sourceOfFunds']['provided']['card']['number'],$bobRetrieveResultSession[1]['sourceOfFunds']['provided']['card']['nameOnCard']);
    //                 return $this->render('topup/topup.html.twig', $topUpData[1]);
    //             }
    //         }
    //         // $_POST['infoString'] = "fmh1M9oF9lrMsRTdmDc+Om1P0JiMZYj4DuzE6A2MdABCy55LM4VsTfqafInpV8DY!#!2.0!#!USD!#!15791";
    //         // dd($_POST['infoString']);
    //         if (isset($_POST['infoString'])) {

    //             if ($_POST['infoString'] == "")
    //                 return $this->render('ExceptionHandling.html.twig');

    //             $suyoolUserInfoForTopUp = explode("!#!", $_POST['infoString']);
    //             $decrypted_string = SuyoolServices::decrypt($suyoolUserInfoForTopUp[0]);
    //             $this->logger->debug($_POST['infoString']);
    //             $suyoolUserInfo = explode("!#!", $decrypted_string);
    //             $devicetype = stripos($_SERVER['HTTP_USER_AGENT'], $suyoolUserInfo[1]);
    //             // dd($_SERVER['HTTP_USER_AGENT']);
    //             $suyoolUserInfoForTopUp[1] = number_format($suyoolUserInfoForTopUp[1], 2, '.', '');
    //             if ($this->notificationServices->checkUser($suyoolUserInfo[0], $suyoolUserInfo[2]) && $devicetype) {
    //                 $parameters = array();
    //                 $bobpayment = $bobPaymentServices->SessionFromBobPayment($suyoolUserInfoForTopUp[1], $suyoolUserInfoForTopUp[2], $suyoolUserInfoForTopUp[3], $suyoolUserInfo[0]);
    //                 if ($bobpayment[0] == false) {
    //                     echo '<script type="text/javascript">',
    //                     ' if (navigator.userAgent.match(/Android/i)) {
    //                         window.AndroidInterface.callbackHandler("GoToApp");
    //                     } else {
    //                         window.webkit.messageHandlers.callbackHandler.postMessage("GoToApp");
    //                     }',
    //                     '</script>';
    //                 }
    //                 ($suyoolUserInfoForTopUp[2] == "USD") ? $currency = "$" : $currency = "LL";
    //                 $sessionInterface->set('suyooler', $suyoolUserInfo[0]);
    //                 $sessionInterface->set('transId', $suyoolUserInfoForTopUp[3]);
    //                 if (isset($suyoolUserInfoForTopUp[4])) {
    //                     $parameters = [
    //                         'topup' => true,
    //                         'session' => $bobpayment[1],
    //                         'fee' => $suyoolUserInfoForTopUp[4],
    //                         'beforefee' => $suyoolUserInfoForTopUp[1] - $suyoolUserInfoForTopUp[4],
    //                         'currency' => $currency
    //                     ];
    //                 } else {
    //                     $parameters = [
    //                         'topup' => true,
    //                         'session' => $bobpayment[1],
    //                         'currency' => $currency
    //                     ];
    //                 }
    //                 return $this->render('topup/topup.html.twig', $parameters);
    //             } else {
    //                 return $this->render('ExceptionHandling.html.twig');
    //             }
    //         } else {
    //             $this->logger->error($bobRetrieveResultSession[0]);
    //             return $this->render('ExceptionHandling.html.twig');
    //         }
    //     } catch (Exception $e) {
    //         $this->logger->error($e->getMessage());
    //         // echo '<script type="text/javascript">',
    //         // ' if (navigator.userAgent.match(/Android/i)) {
    //         //     window.AndroidInterface.callbackHandler("GoToApp");
    //         //   } else {
    //         //     window.webkit.messageHandlers.callbackHandler.postMessage("GoToApp");
    //         //   }',
    //         // '</script>';
    //         return $this->redirectToRoute("app_ToTheAPP");
    //     }
    // }

    #[Route('/topup', name: 'app_topup')]
    public function index(Request $request, SessionInterface $sessionInterface, BobPaymentServices $bobPaymentServices)
    {
        try {
            if (isset($_POST['infoString'])) {
                if ($_POST['infoString'] == "")
                    return $this->render('ExceptionHandling.html.twig');
                $suyoolUserInfoForTopUp = explode("!#!", $_POST['infoString']);
                $decrypted_string = SuyoolServices::decrypt($suyoolUserInfoForTopUp[0]);
                $this->logger->debug($_POST['infoString']);
                $suyoolUserInfo = explode("!#!", $decrypted_string);
                $devicetype = stripos($_SERVER['HTTP_USER_AGENT'], $suyoolUserInfo[1]);
                $suyoolUserInfoForTopUp[1] = number_format($suyoolUserInfoForTopUp[1], 2, '.', '');
                if ($this->notificationServices->checkUser($suyoolUserInfo[0], $suyoolUserInfo[2]) && $devicetype) {
                    $parameters = array();
                    $bobpayment = $bobPaymentServices->hostedsessiontopup($suyoolUserInfoForTopUp[1], $suyoolUserInfoForTopUp[2], $suyoolUserInfoForTopUp[3], $suyoolUserInfo[0], null);
                    ($suyoolUserInfoForTopUp[2] == "USD") ? $currency = "$" : $currency = "LL";
                    $sessionInterface->set('SenderId', $suyoolUserInfo[0]);
                    if (isset($suyoolUserInfoForTopUp[4])) {
                        $parameters = [
                            'topup' => true,
                            'session' => $bobpayment[0],
                            'orderId' => $bobpayment[1],
                            'transactionId' => $bobpayment[2],
                            'fees' => $suyoolUserInfoForTopUp[4],
                            'amount' => $suyoolUserInfoForTopUp[1] - $suyoolUserInfoForTopUp[4],
                            'totalAmount' => $suyoolUserInfoForTopUp[1],
                            'currency' => $currency
                        ];
                    } else {
                        $parameters = [
                            'topup' => true,
                            'session' => $bobpayment[0],
                            'orderId' => $bobpayment[1],
                            'transactionId' => $bobpayment[2],
                            'session' => $bobpayment[1],
                            'totalAmount' => $suyoolUserInfoForTopUp[1],
                            'currency' => $currency
                        ];
                    }
                    return $this->render('topup/hostedsessiontopup.html.twig', $parameters);
                } else {
                    return $this->render('ExceptionHandling.html.twig');
                }
            } else {
                return $this->render('ExceptionHandling.html.twig');
            }
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
            return $this->redirectToRoute("app_ToTheAPP");
        }
    }

    #[Route('/topupRTP', name: 'app_rtptopup')]
    public function rtpTopUp(Request $request, SessionInterface $sessionInterface, BobPaymentServices $bobPaymentServices)
    {
        try {
            if ($sessionInterface->get('allowCardTopup') == "true") {
                $bobRetrieveResultSession = $bobPaymentServices->RetrievePaymentDetails($sessionInterface->get('SenderId'), $sessionInterface->get('SenderPhone'), $sessionInterface->get('ReceiverPhone'));
                // dd($bobRetrieveResultSession);
                if ($bobRetrieveResultSession[0] == true) {
                    $sessionInterface->remove('order');
                    if ($bobRetrieveResultSession[1]['status'] != "CAPTURED") {
                        return $this->redirectToRoute("app_rtptopup");
                    } else {
                        $topUpData = $bobPaymentServices->retrievedataForTopUpRTP($bobRetrieveResultSession[1]['authenticationStatus'], $bobRetrieveResultSession[1]['status'], $sessionInterface->get('indicator'), $bobRetrieveResultSession[1], $sessionInterface->get('transId'), $sessionInterface->get('suyooler'), $bobRetrieveResultSession[1]['sourceOfFunds']['provided']['card']['number'], $sessionInterface->get('SenderPhone'), $sessionInterface->get('SenderId'), $bobRetrieveResultSession[1]['sourceOfFunds']['provided']['card']['nameOnCard']);
                        return $this->render('topup/topuprtp.html.twig', $topUpData[1]);
                    }
                }
                $nonSuyooler = $this->suyoolServices->NonSuyoolerTopUpTransaction($sessionInterface->get('TranSimID'));
                $senderName = $sessionInterface->get('SenderInitials');
                $data = json_decode($nonSuyooler[1], true);
                $parameters = array();
                $bobpayment = $bobPaymentServices->SessionRTPFromBobPayment($data['TotalAmount'], $data['Currency'], $sessionInterface->get('TranSimID'), $sessionInterface->get('SenderId'));
                if ($bobpayment[0] == false) {
                    return $this->redirectToRoute("homepage");
                }
                $parameters = [
                    // 'topup'=>true,
                    'session' => $bobpayment[1],
                    'sender' => $senderName,
                    'fees' => $data['TotalAmount'] - $sessionInterface->get('amount')
                ];
                return $this->render('topup/topuprtp.html.twig', $parameters);
            } else {
                if ($request->headers->get('referer') == null) {
                    return $this->redirectToRoute("homepage");
                } else {
                    return new RedirectResponse($request->headers->get('referer'));
                }
            }
        } catch (\Exception $e) {
            // dd($e->getMessage());
            if ($request->headers->get('referer') == null) {
                return $this->redirectToRoute("homepage");
            } else {
                return new RedirectResponse($request->headers->get('referer'));
            }
        }
    }

    #[Route('/ToTheAPP', name: 'app_ToTheAPP')]
    public function ToTheAPP(Request $request)
    {
        $response = new Response();
        echo '<script type="text/javascript">',
        ' if (navigator.userAgent.match(/Android/i)) {
                window.AndroidInterface.callbackHandler("GoToApp");
              } else {
                window.webkit.messageHandlers.callbackHandler.postMessage("GoToApp");
              }',
        '</script>';
        $response->setStatusCode(Response::HTTP_OK);
        return $response;
    }

    #[Route('/cardpayment', name: 'app_paymentGateway')]
    #[Route('/cardpayment/{test}', name: 'app_paymentGateway_test', requirements: ['test' => 'test'])]
    public function payWithBob(Request $request, SessionInterface $sessionInterface, BobPaymentServices $bobPaymentServices,InvoiceServices $invoicesServices,$test = null)
    {
        // Check if the 'test' parameter exists in the URL change the environment to 'dev'
        if ($test === 'test') {
            $sessionInterface->set('simulation', 'true');
        } else {
            $sessionInterface->set('simulation', 'false');
        }
        setcookie('SenderId', '', -1, '/');
        setcookie('ReceiverPhone', '', -1, '/');
        setcookie('SenderPhone', '', -1, '/');
        setcookie('hostedSessionId', '', -1, '/');
        setcookie('orderidhostedsession', '', -1, '/');
        setcookie('transactionidhostedsession', '', -1, '/');
        setcookie('merchant_name', '', -1, '/');
        setcookie('card_payment_url', '', -1, '/');
        setcookie('simulation', '', -1, '/');
        setcookie('orderIdToShopify', '', -1, '/');
        setcookie('shopifyCardPayment', '', -1, '/');
        try {
            if (!empty($sessionInterface->get('payment_data'))) {
                $data = $sessionInterface->get('payment_data');
            } else {
                $data = $request->query->all();
                $additionalInfo = $data['AdditionalInfo'] ?? '';
                $sessionInterface->set('card_payment_url',$_SERVER['REQUEST_URI']);
            }

            $parameters = array();
            $amount = $data['Amount'] ?? '';
            $currency = $data['Currency'] ?? '';
            $mechantOrderId = $data['TranID'] ?? '';
            $merchantId = $data['MerchantID'] ?? '';
            $callBackUrl = $data['CallBackURL'] ?? '';
            $refNumber = $data['refNumber'] ?? '';

            $Hash = $data['SecureHash'] ?? '';
            $merchant = $invoicesServices->findMerchantByMerchId($data['MerchantID']);

            if ($refNumber != null) {
                $refNumber = "G" . $refNumber;
                $Hash = base64_encode(hash($this->hash_algo,   $mechantOrderId .$merchantId . $amount . $currency . $data['AdditionalInfo'] . $merchant->getCertificate(), true));
            } else {
                $refNumber = null;
            }

            $pushCard = $this->suyoolServices->PushCardToMerchantTransaction($mechantOrderId,(float)$amount, $currency, '', $merchantId, $callBackUrl,$Hash);
            $sessionInterface->remove('payment_data');
            //dd($pushCard);
            $transactionDetails = json_decode($pushCard[1]);

            $merchantName = $sessionInterface->set('merchant_name',$merchant->getName());

            $existingInvoice = $invoicesServices->findExistingInvoice($merchant,$mechantOrderId);

            $paymentType = 'Gateway';
            if ($existingInvoice) {
                $invoicesServices->UpdateInvoiceDetails($transactionDetails->TransactionId,'Card',$merchant,$mechantOrderId);
                $paymentType = 'Invoice';
            }
            $finalAmount = number_format($transactionDetails->TransactionAmount, 2, '.', '');

            $bobpayment = $bobPaymentServices->hostedsession($finalAmount, $transactionDetails->Currency, $transactionDetails->TransactionId, null,$refNumber,$paymentType,$merchantId);

            if ($bobpayment[0] == false) {
                return $this->redirectToRoute("homepage");
            }
            ($transactionDetails->Currency == "USD") ? $currency = "$" : $currency = "LL";
            $parameters = [
                'session' => $bobpayment[0],
                'orderId' => $bobpayment[1],
                'transactionId' => $bobpayment[2],
                'fees' => $transactionDetails->FeesAmount,
                'amount' => $finalAmount,
                'currency' => $currency,
                'merchantName' => $merchantName,
                'simulation' => $sessionInterface->get('simulation')
            ];
            $sessionInterface->remove('payment_data');
            return $this->render('topup/topupinvoice.html.twig', $parameters);
        } catch (\Exception $e) {
            dd($e->getMessage());
            if ($request->headers->get('referer') == null) {
                return $this->redirectToRoute("homepage");
            } else {
                return new RedirectResponse($request->headers->get('referer'));
            }
        }
    }

    #[Route('payment/topupRtp', name: 'app_topup_hostedsession')]
    public function hostedsession(BobPaymentServices $bobPaymentServices, SessionInterface $sessionInterface)
    {
        $nonSuyooler = $this->suyoolServices->NonSuyoolerTopUpTransaction($sessionInterface->get('TranSimID'));
        $senderName = $sessionInterface->get('SenderInitials');
        $data = json_decode($nonSuyooler[1], true);
        $parameters = array();
        $bobpayment = $bobPaymentServices->hostedsessionRTP($data['TotalAmount'], $data['Currency'], $sessionInterface->get('TranSimID'), $sessionInterface->get('SenderId'), $sessionInterface->get('Code'), 'rtp',json_encode([
            'SendarInitials'=>$sessionInterface->get('SenderInitials'),
            'SenderPhone'=>$sessionInterface->get('SenderPhone'),
            'ReceiverPhone'=>$sessionInterface->get('ReceiverPhone'),
            'SenderId'=> $sessionInterface->get('SenderId')
        ]));
        $parameters = [
            'session' => $bobpayment[0],
            'orderId' => $bobpayment[1],
            'transactionId' => $bobpayment[2],
            'sender' => $senderName,
            'fees' => $data['TotalAmount'] - $sessionInterface->get('amount'),
            'amount' => $data['TotalAmount']
        ];

        return $this->render('topup/hostedsession.html.twig', $parameters);
    }

    #[Route('/topup2test', name: 'app_topup_hostedsession_TEST')]
    public function hostedsessiontest(BobPaymentServices $bobPaymentServices, SessionInterface $sessionInterface)
    {
        $sessionInterface->remove('APP_ENV_test');
        setcookie('SenderId', '', -1, '/');
        setcookie('ReceiverPhone', '', -1, '/');
        setcookie('SenderPhone', '', -1, '/');
        setcookie('hostedSessionId', '', -1, '/');
        setcookie('orderidhostedsession', '', -1, '/');
        setcookie('transactionidhostedsession', '', -1, '/');
        unset($_COOKIE['SenderId']);
        unset($_COOKIE['ReceiverPhone']);
        unset($_COOKIE['SenderPhone']);
        unset($_COOKIE['hostedSessionId']);
        unset($_COOKIE['orderidhostedsession']);
        unset($_COOKIE['transactionidhostedsession']);
        $senderName = "anthony";
        $sessionInterface->set('amountwcurrency', "$ 1.00");
        $sessionInterface->set('currencyInAbb', "$");
        $parameters = array();
        $bobpayment = $bobPaymentServices->hostedsessionTest(2, "USD", "996", "anthony", "test");

        $parameters = [
            'session' => $bobpayment[0],
            'orderId' => $bobpayment[1],
            'transactionId' => $bobpayment[2],
            'sender' => $senderName,
            'fees' => 2 - 1
        ];

        return $this->render('topup/hostedsession.html.twig', $parameters);
    }

    #[Route('/3dsreceipt', name: 'app_topup_edsecure')]
    public function secure(BobPaymentServices $bobPaymentServices, SessionInterface $sessionInterface, $test = null)
    {
        // setcookie('hostedSessionId', $sessionInterface->get('hostedSessionId'), time() + (60 * 10));
        // setcookie('orderidhostedsession', $sessionInterface->get('orderidhostedsession'), time() + (60 * 10));
        // setcookie('transactionidhostedsession', $sessionInterface->get('transactionidhostedsession'), time() + (60 * 10));
        // setcookie('SenderId', $sessionInterface->get('SenderId'), time() + (60 * 10));
        // setcookie('ReceiverPhone', $sessionInterface->get('ReceiverPhone'), time() + (60 * 10));
        // setcookie('SenderPhone', $sessionInterface->get('SenderPhone'), time() + (60 * 10));
        // setcookie('SenderInitials', $sessionInterface->get('SenderInitials'), time() + (60 * 10));
        setcookie('merchant_name', $sessionInterface->get('merchant_name'), time() + (60 * 10));
        setcookie('card_payment_url', $sessionInterface->get('card_payment_url'), time() + (60 * 10));
        setcookie('simulation', $sessionInterface->get('simulation'), time() + (60 * 10));
        setcookie('shopifyCardPayment', $sessionInterface->get('shopifyCardPayment'), time() + (60 * 10));
        setcookie('orderIdToShopify', $sessionInterface->get('orderIdToShopify'), time() + (60 * 10));

        $parameters = [
            'session' => $sessionInterface->get('hostedSessionId'),
            'orderId' => $sessionInterface->get('orderidhostedsession'),
            'transactionId' => $sessionInterface->get('transactionidhostedsession')
        ];

        return $this->render('topup/3dsecure.html.twig', $parameters);
    }

    #[Route('/3dsreceipt2', name: 'app_topup_edsecure2')]
    public function secure2(BobPaymentServices $bobPaymentServices, SessionInterface $sessionInterface, $test = null)
    {
        // setcookie('hostedSessionId', $sessionInterface->get('hostedSessionId'), time() + (60 * 10));
        // setcookie('orderidhostedsession', $sessionInterface->get('orderidhostedsession'), time() + (60 * 10));
        // setcookie('transactionidhostedsession', $sessionInterface->get('transactionidhostedsession'), time() + (60 * 10));
        // setcookie('SenderId', $sessionInterface->get('SenderId'), time() + (60 * 10));
        // setcookie('ReceiverPhone', $sessionInterface->get('ReceiverPhone'), time() + (60 * 10));
        // setcookie('SenderPhone', $sessionInterface->get('SenderPhone'), time() + (60 * 10));
        // setcookie('SenderInitials', $sessionInterface->get('SenderInitials'), time() + (60 * 10));
        // setcookie('merchant_name', $sessionInterface->get('merchant_name'), time() + (60 * 10));
        // setcookie('card_payment_url', $sessionInterface->get('card_payment_url'), time() + (60 * 10));
        // setcookie('simulation', $sessionInterface->get('simulation'), time() + (60 * 10));
        $parameters = [
            'session' => $sessionInterface->get('hostedSessionId'),
            'orderId' => $sessionInterface->get('orderidhostedsession'),
            'transactionId' => $sessionInterface->get('transactionidhostedsession')
        ];

        return $this->render('topup/3dsecure2.html.twig', $parameters);
    }

    #[Route('/pay', name: 'app_topup_blacklist', methods: ['POST'])]
    public function checkblacklist(Request $request, BobPaymentServices $bobPaymentServices, SessionInterface $sessionInterface)
    {
        $cardnumber = $bobPaymentServices->checkCardNumber();
        if (substr($cardnumber, 0, 6) == 423265 || substr($cardnumber, 0, 6) == 552009 || substr($cardnumber, 0, 6) == 557618) {
            $response = [
                'title' => 'Using Suyooler Card',
                'description' => 'Use your Suyool app for a seamless payment instead of Suyool Visa Card.'
            ];
            return new JsonResponse([
                'status' => false,
                'response' => $response
            ]);
        }
        $checkIfTheCardInTheBlackList = $this->mr->getRepository(blackListCards::class)->findOneBy(['card' => $cardnumber]);
        if (is_null($checkIfTheCardInTheBlackList)) {
            $status = true;
            $response = "Go to Receipt3d";
        } else {
            $emailMessageBlacklistedCard = "Dear,<br><br>Our automated system has detected a potential fraudulent transaction requiring your attention:<br><br>";
            $emailMessageBlacklistedCard .= "We have identified that the card with the number {$_POST['card']} has been blacklisted. <br><br>";
            $emailMessageBlacklistedCard .= "</ul><br><br>Please initiate the necessary protocol for further investigation and action.<br><a href='https://suyool.com'>Suyool.com</a>";
            $this->suyoolServices->sendDotNetEmail('[Alert] Suspected Fraudulent RTP Transaction', 'anthony.saliba@elbarid.com', $emailMessageBlacklistedCard, "", "", "suyool@noreply.com", "Suyool", 1, 0);
            $status = false;
            $response = [
                'title' => 'Fraudulent Card',
                'description' => 'The card you are using is flagged as fraudulent.
                Kindly contact your issuer bank.'
            ];
        }
        return new JsonResponse([
            'status' => $status,
            'response' => $response
        ]);
    }

    #[Route('/payment/responseRtp/{orderid}/{transid}', name: 'app_topup_blacklist2_rtp')]
    public function checkblacklist2(Request $request, BobPaymentServices $bobPaymentServices, SessionInterface $sessionInterface,$orderid,$transid)
    {
        if(isset($_COOKIE['merchant_name'])){
            $data = $bobPaymentServices->updatedTransactionInHostedSessionToPay($orderid,$transid,null, null, null, null, $_COOKIE['merchant_name'], $_COOKIE['simulation']);
        }else{
            $data = $bobPaymentServices->updatedTransactionInHostedSessionToPay($orderid,$transid,@$_COOKIE['SenderId'], @$_COOKIE['ReceiverPhone'], @$_COOKIE['SenderPhone'], @$_COOKIE['SenderInitials']);
        }
        return $this->render('topup/popup.html.twig', $data);
    }

    #[Route('payment/response/{orderid}/{transid}', name: 'app_topup_blacklist2')]
    public function checkblacklist2topup(Request $request, BobPaymentServices $bobPaymentServices, SessionInterface $sessionInterface,$orderid,$transid)
    {
        sleep(1);
        $this->logger->info("user entered pay2topup");
            $data = $bobPaymentServices->updatedTransactionInHostedSessionToPayTopup($orderid,$transid);
            $status = true;
            $response = $data;
        return $this->render('topup/popup.html.twig', $response);
    }

    #[Route('/pay2test', name: 'app_topup_blacklist2_test')]
    public function checkblacklist2test(Request $request, BobPaymentServices $bobPaymentServices, SessionInterface $sessionInterface)
    {
        // dd($_COOKIE);
        $checkIfTheCardInTheBlackList = NULL;
        if (is_null($checkIfTheCardInTheBlackList)) {
            $data = $bobPaymentServices->updatedTransactionInHostedSessionToPayTest($_COOKIE['SenderId'], $_COOKIE['ReceiverPhone'], $_COOKIE['SenderPhone']);
            $status = true;
            $response = $data;
        } else {
            $emailMessageBlacklistedCard = "Dear,<br><br>Our automated system has detected a potential fraudulent transaction requiring your attention:<br><br>";

            $emailMessageBlacklistedCard .= "We have identified that the card with the number {$_POST['card']} has been blacklisted. <br><br>";

            $emailMessageBlacklistedCard .= "</ul><br><br>Please initiate the necessary protocol for further investigation and action.<br><a href='https://suyool.com'>Suyool.com</a>";
            // $this->suyoolServices->sendDotNetEmail('[Alert] Suspected Fraudulent RTP Transaction', 'web@suyool.com,it@suyool.com,arz@elbarid.com', $emailMessageBlacklistedCard, "", "", "suyool@noreply.com", "Suyool", 1, 0);
            $this->suyoolServices->sendDotNetEmail('[Alert] Suspected Fraudulent RTP Transaction', 'anthony.saliba@elbarid.com', $emailMessageBlacklistedCard, "", "", "suyool@noreply.com", "Suyool", 1, 0);
            $status = false;
            $response = 'The Card Number is blacklisted';
        }

        return $this->render('topup/popup.html.twig', $response);
    }

    /**
     * @Route("/callbackURL", name="callbackUrl")
     */
    public function generateJSON(Request $request): JsonResponse
    {
        $urlParams = $request->query->all();


        if (!empty($urlParams)) {

            return $this->json($urlParams);
        } else {

            return $this->json(['message' => 'No parameters received']);
        }
    }
}
