<?php

namespace App\Controller;

use App\Entity\Iveri\orders;
use App\Entity\topup\attempts;
use App\Service\BobPaymentServices;
use App\Service\BobServices;
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

    public function __construct(ManagerRegistry $mr, SuyoolServices $suyoolServices, NotificationServices $notificationServices, LoggerInterface $loggerInterface, SessionInterface $sessionInterface)
    {
        $this->mr = $mr->getManager('topup');
        $this->suyoolServices = $suyoolServices;
        $this->notificationServices = $notificationServices;
        $this->logger = $loggerInterface;
        $this->sessionInterface = $sessionInterface;
    }

    #[Route('/topup', name: 'app_topup')]
    public function index(Request $request, SessionInterface $sessionInterface, BobPaymentServices $bobPaymentServices)
    {
        try {
            // $this->suyoolServices->UpdateCardTopUpTransaction(12764,3,"12764","3990000.00","LBP","8367");
            $bobRetrieveResultSession = $bobPaymentServices->RetrievePaymentDetails($sessionInterface->get('suyooler'));
            if ($bobRetrieveResultSession[0] == true) {
                $sessionInterface->remove('order');
                if ($bobRetrieveResultSession[1]['status'] != "CAPTURED") {
                    echo '<script type="text/javascript">',
                    ' if (navigator.userAgent.match(/Android/i)) {
                            window.AndroidInterface.callbackHandler("GoToApp");
                          } else {
                            window.webkit.messageHandlers.callbackHandler.postMessage("GoToApp");
                          }',
                    '</script>';
                } else {
                    $topUpData = $bobPaymentServices->retrievedataForTopUp($bobRetrieveResultSession[1]['authenticationStatus'], $bobRetrieveResultSession[1]['status'], $sessionInterface->get('indicator'), $bobRetrieveResultSession[1], $sessionInterface->get('transId'), $sessionInterface->get('suyooler'), $bobRetrieveResultSession[1]['sourceOfFunds']['provided']['card']['number']);
                    return $this->render('topup/topup.html.twig', $topUpData[1]);
                }
            }
            // $_POST['infoString'] = "fmh1M9oF9lrMsRTdmDc+Om1P0JiMZYj4DuzE6A2MdABCy55LM4VsTfqafInpV8DY!#!2.0!#!USD!#!15791";
            // dd($_POST['infoString']);
            if (isset($_POST['infoString'])) {

                if ($_POST['infoString'] == "")
                    return $this->render('ExceptionHandling.html.twig');

                $suyoolUserInfoForTopUp = explode("!#!", $_POST['infoString']);
                $decrypted_string = SuyoolServices::decrypt($suyoolUserInfoForTopUp[0]);
                $this->logger->debug($_POST['infoString']);
                $suyoolUserInfo = explode("!#!", $decrypted_string);
                $devicetype = stripos($_SERVER['HTTP_USER_AGENT'], $suyoolUserInfo[1]);
                // dd($_SERVER['HTTP_USER_AGENT']);

                if ($this->notificationServices->checkUser($suyoolUserInfo[0], $suyoolUserInfo[2]) && $devicetype) {
                    $parameters = array();
                    $bobpayment = $bobPaymentServices->SessionFromBobPayment($suyoolUserInfoForTopUp[1], $suyoolUserInfoForTopUp[2], $suyoolUserInfoForTopUp[3], $suyoolUserInfo[0]);
                    if ($bobpayment[0] == false) {
                        echo '<script type="text/javascript">',
                        ' if (navigator.userAgent.match(/Android/i)) {
                            window.AndroidInterface.callbackHandler("GoToApp");
                        } else {
                            window.webkit.messageHandlers.callbackHandler.postMessage("GoToApp");
                        }',
                        '</script>';
                    }
                    ($suyoolUserInfoForTopUp[2] == "USD") ? $currency = "$" : $currency = "LL";
                    $sessionInterface->set('suyooler', $suyoolUserInfo[0]);
                    $sessionInterface->set('transId', $suyoolUserInfoForTopUp[3]);
                    if (isset($suyoolUserInfoForTopUp[4])) {
                        $parameters = [
                            'topup' => true,
                            'session' => $bobpayment[1],
                            'fee' => $suyoolUserInfoForTopUp[4],
                            'beforefee' => $suyoolUserInfoForTopUp[1] - $suyoolUserInfoForTopUp[4],
                            'currency' => $currency
                        ];
                    } else {
                        $parameters = [
                            'topup' => true,
                            'session' => $bobpayment[1],
                            'currency' => $currency
                        ];
                    }


                    return $this->render('topup/topup.html.twig', $parameters);
                } else {
                    return $this->render('ExceptionHandling.html.twig');
                }
            } else {
                return $this->render('ExceptionHandling.html.twig');
            }
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
            // echo '<script type="text/javascript">',
            // ' if (navigator.userAgent.match(/Android/i)) {
            //     window.AndroidInterface.callbackHandler("GoToApp");
            //   } else {
            //     window.webkit.messageHandlers.callbackHandler.postMessage("GoToApp");
            //   }',
            // '</script>';
            return $this->redirectToRoute("app_ToTheAPP");
        }
    }

    #[Route('/topupRTP', name: 'app_rtptopup')]
    public function rtpTopUp(Request $request, SessionInterface $sessionInterface, BobPaymentServices $bobPaymentServices)
    {
        // $attemptsPerCard = $this->mr->getRepository(attempts::class)->GetTransactionsPerCard("512345xxxxxx0008");
        // dd($attemptsPerCard);
        // dd($sessionInterface->get('allowCardTopup'));
        try {
            if ($sessionInterface->get('allowCardTopup')) {
                $bobRetrieveResultSession = $bobPaymentServices->RetrievePaymentDetails($sessionInterface->get('SenderId'), $sessionInterface->get('SenderPhone'), $sessionInterface->get('ReceiverPhone'));
                // dd($bobRetrieveResultSession);
                if ($bobRetrieveResultSession[0] == true) {
                    $sessionInterface->remove('order');
                    if ($bobRetrieveResultSession[1]['status'] != "CAPTURED") {
                        return $this->redirectToRoute("app_rtptopup");
                    } else {
                        $topUpData = $bobPaymentServices->retrievedataForTopUpRTP($bobRetrieveResultSession[1]['authenticationStatus'], $bobRetrieveResultSession[1]['status'], $sessionInterface->get('indicator'), $bobRetrieveResultSession[1], $sessionInterface->get('transId'), $sessionInterface->get('suyooler'), $bobRetrieveResultSession[1]['sourceOfFunds']['provided']['card']['number'], $sessionInterface->get('SenderPhone'), $sessionInterface->get('SenderId'));
                        return $this->render('topup/topuprtp.html.twig', $topUpData[1]);
                    }
                }

                $nonSuyooler = $this->suyoolServices->NonSuyoolerTopUpTransaction($sessionInterface->get('TranSimID'));
                $senderName = $sessionInterface->get('SenderInitials');
                $data = json_decode($nonSuyooler[1], true);
                $parameters = array();
                $bobpayment = $bobPaymentServices->SessionRTPFromBobPayment($data['TotalAmount'], $data['Currency'], $sessionInterface->get('TranSimID'));
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

    // #[Route('/updateTopupMoney', name: 'app_rtptopupupdate')]
    // public function updateTopupMoney(Request $request, SessionInterface $sessionInterface, BobPaymentServices $bobPaymentServices)
    // {
    //     try{
    //         $bobRetrieveResultSession = $bobPaymentServices->RetrievePaymentDetailsUpdate();
    //         if ($bobRetrieveResultSession[0] == true) {
    //             $sessionInterface->remove('order');
    //             if ($bobRetrieveResultSession[1]['status'] != "CAPTURED") {
    //                 return $this->redirectToRoute("app_rtptopup");
    //             } else {
    //                 $topUpData = $bobPaymentServices->retrievedataForTopUpRTPUpdate($bobRetrieveResultSession[1]['authenticationStatus'], $bobRetrieveResultSession[1]['status'], "ae2b7cc62ad4455c", $bobRetrieveResultSession[1]);
    //                 // dd($topUpData);
    //                 return $this->render('topup/topuprtp.html.twig', $topUpData[1]);
    //             }
    //         }
    //     }catch(Exception $e){
    //         dd($e->getMessage());
    //     }

    // }
}
