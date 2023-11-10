<?php

namespace App\Controller;

use App\Entity\Iveri\orders;
use App\Service\BobPaymentServices;
use App\Service\BobServices;
use App\Service\IveriServices;
use App\Service\NotificationServices;
use App\Service\SuyoolServices;
use Doctrine\Persistence\ManagerRegistry;
use DOMDocument;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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
        $bobRetrieveResultSession = $bobPaymentServices->RetrievePaymentDetails();

        if ($bobRetrieveResultSession[0] == true) {
            $sessionInterface->remove('order');
            // dd($bobRetrieveResultSession);
            $topUpData = $bobPaymentServices->retrievedataForTopUp($bobRetrieveResultSession[1]['authenticationStatus'],$bobRetrieveResultSession[1]['status'], $request->query->get('resultIndicator'), $bobRetrieveResultSession[1], $sessionInterface->get('transId'), $sessionInterface->get('suyooler'),$bobRetrieveResultSession[1]['sourceOfFunds']['provided']['card']['number']);
            return $this->render('topup/topup.html.twig', $topUpData[1]);
        }

        // $_POST['infoString'] = "fmh1M9oF9lrMsRTdmDc+Om1P0JiMZYj4DuzE6A2MdABCy55LM4VsTfqafInpV8DY!#!2.0!#!USD!#!15791";

        if (isset($_POST['infoString'])) {

            if ($_POST['infoString'] == "")
                return $this->render('ExceptionHandling.html.twig');

            $suyoolUserInfoForTopUp = explode("!#!", $_POST['infoString']);
            $decrypted_string = SuyoolServices::decrypt($suyoolUserInfoForTopUp[0]);
            $suyoolUserInfo = explode("!#!", $decrypted_string);
            $devicetype = stripos($_SERVER['HTTP_USER_AGENT'], $suyoolUserInfo[1]);

            if ($this->notificationServices->checkUser($suyoolUserInfo[0], $suyoolUserInfo[2]) && $devicetype) {
                $parameters = array();
                $bobpayment = $bobPaymentServices->SessionFromBobPayment($suyoolUserInfoForTopUp[1], $suyoolUserInfoForTopUp[2], $suyoolUserInfoForTopUp[3], $suyoolUserInfo[0]);
                $sessionInterface->set('suyooler', $suyoolUserInfo[0]);
                $sessionInterface->set('transId', $suyoolUserInfoForTopUp[3]);
                $parameters = [
                    'topup' => true,
                    'session' => $bobpayment[1]
                ];

                return $this->render('topup/topup.html.twig', $parameters);
            } else {
                return $this->render('ExceptionHandling.html.twig');
            }
        } else {
            return $this->render('ExceptionHandling.html.twig');
        }
    }

    #[Route('/rtp', name: 'app_rtp')]
    public function rtpForTest(Request $request, SessionInterface $sessionInterface, BobPaymentServices $bobPaymentServices)
    {
        $bobRetrieveResultSession = $bobPaymentServices->RetrievePaymentDetails();

        if ($bobRetrieveResultSession[0] == true) {
            $sessionInterface->remove('order');
            // dd($bobRetrieveResultSession);
            $topUpData = $bobPaymentServices->retrievedataForTopUpTest($bobRetrieveResultSession[1]['authenticationStatus'],$bobRetrieveResultSession[1]['status'], $request->query->get('resultIndicator'), $bobRetrieveResultSession[1], $sessionInterface->get('transId'), $sessionInterface->get('suyooler'));
            return $this->render('topup/topupprod.html.twig', $topUpData[1]);
        }

        $_POST['infoString'] = "fmh1M9oF9lrMsRTdmDc+Om1P0JiMZYj4DuzE6A2MdABCy55LM4VsTfqafInpV8DY!#!1.0!#!USD!#!15791";

        if (isset($_POST['infoString'])) {

            if ($_POST['infoString'] == "") return $this->render('ExceptionHandling.html.twig');

            $suyoolUserInfoForTopUp = explode("!#!", $_POST['infoString']);
            $decrypted_string = SuyoolServices::decrypt($suyoolUserInfoForTopUp[0]);
            $suyoolUserInfo = explode("!#!", $decrypted_string);

            $devicetype = stripos($_SERVER['HTTP_USER_AGENT'], $suyoolUserInfo[1]);

            if ($this->notificationServices->checkUser($suyoolUserInfo[0], $suyoolUserInfo[2])) {
                $parameters = array();
                $bobpayment = $bobPaymentServices->paymentGateWayTest($suyoolUserInfoForTopUp[1], $suyoolUserInfoForTopUp[2], $suyoolUserInfoForTopUp[3], $suyoolUserInfo[0]);
                $sessionInterface->set('suyooler', $suyoolUserInfo[0]);
                $sessionInterface->set('transId', $suyoolUserInfoForTopUp[3]);
                $parameters = [
                    // 'topup'=>true,
                    'session' => $bobpayment[1]
                ];

                return $this->render('topup/topupprod.html.twig', $parameters);
            } else {
                return $this->render('ExceptionHandling.html.twig');
            }
        } else {
            return $this->render('ExceptionHandling.html.twig');
        }
    }

    #[Route('/topupRTP', name: 'app_rtptopup')]
    public function rtpTopUp(Request $request, SessionInterface $sessionInterface,BobPaymentServices $bobPaymentServices)
    {
        $bobRetrieveResultSession = $bobPaymentServices->RetrievePaymentDetails();

        if ($bobRetrieveResultSession[0] == true) {
            $sessionInterface->remove('order');
            // dd($bobRetrieveResultSession);
            $topUpData = $bobPaymentServices->retrievedataForTopUpRTP($bobRetrieveResultSession[1]['authenticationStatus'],$bobRetrieveResultSession[1]['status'], $request->query->get('resultIndicator'), $bobRetrieveResultSession[1], $sessionInterface->get('transId'), $sessionInterface->get('suyooler'),$bobRetrieveResultSession[1]['sourceOfFunds']['provided']['card']['number']);
            return $this->render('topup/topuprtp.html.twig', $topUpData[1]);
        }

        $nonSuyooler = $this->suyoolServices->NonSuyoolerTopUpTransaction($sessionInterface->get('TranSimID'));
        // dd($nonSuyooler);
        $data = json_decode($nonSuyooler[1], true);
        $parameters = array();
        $bobpayment = $bobPaymentServices->SessionRTPFromBobPayment($data['TotalAmount'],$data['Currency'],$sessionInterface->get('TranSimID'));
        $parameters = [
            // 'topup'=>true,
            'session' => $bobpayment[1]
        ];

        return $this->render('topup/topuprtp.html.twig', $parameters);
    }
}
