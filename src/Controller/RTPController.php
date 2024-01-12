<?php

namespace App\Controller;

use App\Service\SuyoolServices;
use App\Entity\Transaction;
use App\Utils\Helper;
use App\Translation\translation;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class RTPController extends AbstractController
{
    private $trans;
    private $session;
    private $hash_algo;
    private $certificate;
    private $cashinput = false;
    private $suyoolServices;
    private $mr;

    public function __construct(translation $trans, SessionInterface $session, $hash_algo, $certificate, SuyoolServices $suyoolServices, ManagerRegistry $mr)
    {
        $this->trans = $trans;
        $this->session = $session;
        $this->hash_algo = $hash_algo;
        $this->certificate = $certificate;
        $this->suyoolServices = $suyoolServices;
        $this->mr = $mr->getManager('default');
    }

    function GetInitials($name)
    {
        $word = explode(" ", $name);
        $initials = "";

        foreach ($word as $w) {
            $initials .= ucwords($w[0]);
        }

        return $initials;
    }

    /**
     * @Route("/rtp/{code}", name="app_request")
     */
    public function index(Request $request, TranslatorInterface $translator, $code): Response
    {
        $this->session->clear();
        setcookie('SenderId', '', -1, '/'); 
        setcookie('ReceiverPhone', '', -1, '/'); 
        setcookie('SenderPhone', '', -1, '/'); 
        setcookie('hostedSessionId', '', -1, '/'); 
        setcookie('orderidhostedsession', '', -1, '/'); 
        setcookie('transactionidhostedsession', '', -1, '/'); 
        setcookie('SenderInitials', '', -1, '/'); 
        setcookie('simulation', '', -1, '/'); 
        setcookie('merchant_name', '', -1, '/'); 


        unset($_COOKIE['SenderId']);
        unset($_COOKIE['ReceiverPhone']);
        unset($_COOKIE['SenderPhone']);
        unset($_COOKIE['hostedSessionId']);
        unset($_COOKIE['orderidhostedsession']);
        unset($_COOKIE['transactionidhostedsession']);
        unset($_COOKIE['SenderInitials']);
        unset($_COOKIE['simulation']);
        unset($_COOKIE['merchant_name']);


        $this->session->remove('requestGenerated');
        $parameters = $this->trans->translation($request, $translator);
        $parameters['currentPage'] = "payment_landingPage";
        $parameters['request_details_response'] = $this->suyoolServices->RequestDetails($code, $parameters['lang']);
        $parameters['currency'] = "LBP";
        // $parameters['request_details_response']['allowCardTopup']=null;
        // dd($parameters['request_details_response']);

        if (strpos($parameters['request_details_response']['amount'], "$") !== false) $parameters['currency'] = "USD";

        $amount = explode(" ", $parameters['request_details_response']['amount']);
        $amount = str_replace(",", "", $amount);


        if ($parameters['request_details_response']['respCode'] == 2 || $parameters['request_details_response']['respCode'] == -1 || $parameters['request_details_response']['transactionID'] == 0) {
            return $this->redirectToRoute("homepage");
        }
        $parameters['amount'] = $amount[1];
        $parameters['currencyInAbb'] = $amount[0];
        $this->session->set("request_details_response", $parameters['request_details_response']);
        $this->session->set('amountwcurrency', $parameters['request_details_response']['amount']);
        $this->session->set('amount', $parameters['amount']);
        $this->session->set('currencyInAbb', $parameters['currencyInAbb']);

        $this->session->set("Code", $code);
        $this->session->set(
            "image",
            isset($parameters['request_details_response']['image'])
                ? $parameters['request_details_response']['image']
                : ''
        );
        $this->session->set(
            "SenderInitials",
            isset($parameters['request_details_response']['senderName'])
                ? $parameters['request_details_response']['senderName']
                : ''
        );
        $this->session->set(
            "SenderId",
            isset($parameters['request_details_response']['senderId'])
                ? $parameters['request_details_response']['senderId']
                : ''
        );
        $this->session->set(
            "TranSimID",
            isset($parameters['request_details_response']['transactionID'])
                ? $parameters['request_details_response']['transactionID']
                : ''
        );

        $this->session->set(
            "IBAN",
            isset($parameters['request_details_response']['iban'])
                ? $parameters['request_details_response']['iban']
                : ''
        );
        $this->session->set(
            "allowCashin",
            isset($parameters['request_details_response']['allowCashin'])
                ? $parameters['request_details_response']['allowCashin']
                : ''
        );
        $this->session->set(
            "allowExternal",
            isset($parameters['request_details_response']['allowExternal'])
                ? $parameters['request_details_response']['allowExternal']
                : ''
        );
        $this->session->set(
            "allowCardTopup",
            isset($parameters['request_details_response']['allowCardTopup'])
                ? $parameters['request_details_response']['allowCardTopup']
                : ''
        );
        if (isset($parameters['request_details_response']['additionalData'])) {
            $additionalData = $parameters['request_details_response']['additionalData'];
            $additionalData = json_decode($additionalData, true);
        }

        $this->session->set(
            "receiverFname",
            isset($additionalData['receiverFname'])
                ? $additionalData['receiverFname']
                : ''
        );
        $this->session->set(
            "receiverLname",
            isset($additionalData['receiverLname'])
                ? $additionalData['receiverLname']
                : ''
        );
        $this->session->set(
            "ReceiverPhone",
            isset($additionalData['ReceiverPhone'])
                ? $additionalData['ReceiverPhone']
                : ''
        );
        $this->session->set(
            "SenderPhone",
            isset($additionalData['Senderphone'])
                ? $additionalData['Senderphone']
                : ''
        );
        if (isset($additionalData['AuthenticationCode']) || $parameters['request_details_response']['respCode'] == 1) {
            $this->session->set(
                "requestGenerated",
                isset($additionalData['AuthenticationCode'])
                    ? $additionalData['AuthenticationCode']
                    : ''
            );
        }

        return $this->render('rtp/index.html.twig', $parameters);
    }

    /**
     * @Route("/rtp/generateCode", name="generateCode")
     */
    public function generateCode(Request $request, TranslatorInterface $translator)
    {
        $code = $this->session->get('requestGenerated');
        if (isset($code)) {
            $parameters['cashin']['data'] = $code;
            return $this->render('rtp/codeGenerated.html.twig', $parameters);
        }
        $submittedToken = $request->request->get('token');
        $parameters = $this->trans->translation($request, $translator);
        $parameters = $this->trans->translation($request, $translator);
        $code = $this->session->get('code');
        $parameters['ReceiverPhone'] = $this->session->get('ReceiverPhone');
        if (isset($_POST['submit'])) {
            if (!empty($_POST['fname']) && !empty($_POST['lname'])) {
                $parameters['cashin'] = $this->suyoolServices->PaymentCashin($this->session->get('TranSimID'), $_POST['fname'], $_POST['lname']);
                if ($parameters['cashin']['globalCode'] == 0 && $parameters['cashin']['flagCode'] != 1) {
                    $parameters['imgsrc'] = "build/images/Loto/error.png";
                    $parameters['title'] = "Unable to create code";
                    $parameters['description'] = "We are unable to create a cashout code.<br>
                    Contact our call center at 01-290900 for further assistance.";
                    $parameters['button'] = "Call Call Center";
                    return $this->render('rtp/generateCode.html.twig', $parameters);
                } else if ($parameters['cashin']['flagCode'] == 1) {
                    $this->session->set(
                        "requestGenerated",
                        isset($parameters['cashin']['data'])
                            ? $parameters['cashin']['data']
                            : ''
                    );
                    return $this->render('rtp/codeGenerated.html.twig', $parameters);
                }
            } else {
                $parameters['cashin']['globalCode'] = 0;
                $parameters['cashin']['flagCode'] = 0;
                $parameters['message'] = 'All input are required';
            }
        }

        return $this->render('rtp/generateCode.html.twig', $parameters);
    }

    /**
     * @Route("/rtp/codeGenerated", name="codeGenerated")
     */
    public function codeGenerated(Request $request, TranslatorInterface $translator): Response
    {
        $parameters = $this->trans->translation($request, $translator);
        $parameters['currency'] = "dollar";
        $parameters['currentPage'] = "GenerateCode2";

        return $this->render('rtp/codeGenerated.html.twig', $parameters);
    }

    /**
     * @Route("/rtp/visaCard", name="visaCard")
     */
    public function visaCard(Request $request, TranslatorInterface $translator): Response
    {
        $parameters = $this->trans->translation($request, $translator);
        $parameters['currency'] = "dollar";
        $parameters['currentPage'] = "visaCard";

        return $this->render('rtp/visaCard.html.twig', $parameters);
    }

    /**
     * @Route("/test/{code}", name="app_request_test")
     */
    public function testHostedSession(Request $request, TranslatorInterface $translator, $code): Response
    {
        setcookie('SenderId', '', -1, '/'); 
        setcookie('ReceiverPhone', '', -1, '/'); 
        setcookie('SenderPhone', '', -1, '/'); 
        setcookie('hostedSessionId', '', -1, '/'); 
        setcookie('orderidhostedsession', '', -1, '/'); 
        setcookie('transactionidhostedsession', '', -1, '/'); 
        setcookie('SenderInitials', '', -1, '/'); 
        unset($_COOKIE['SenderId']);
        unset($_COOKIE['ReceiverPhone']);
        unset($_COOKIE['SenderPhone']);
        unset($_COOKIE['hostedSessionId']);
        unset($_COOKIE['orderidhostedsession']);
        unset($_COOKIE['transactionidhostedsession']);
        unset($_COOKIE['SenderInitials']);
        $this->session->remove('requestGenerated');
        $parameters = $this->trans->translation($request, $translator);
        $parameters['currentPage'] = "payment_landingPage";
        $parameters['request_details_response'] = $this->suyoolServices->RequestDetails($code, $parameters['lang']);
        $parameters['currency'] = "LBP";
        // $parameters['request_details_response']['allowCardTopup']=null;
        // dd($parameters['request_details_response']);

        if (strpos($parameters['request_details_response']['amount'], "$") !== false) $parameters['currency'] = "USD";

        $amount = explode(" ", $parameters['request_details_response']['amount']);
        $amount = str_replace(",", "", $amount);


        if ($parameters['request_details_response']['respCode'] == 2 || $parameters['request_details_response']['respCode'] == -1 || $parameters['request_details_response']['transactionID'] == 0) {
            return $this->redirectToRoute("homepage");
        }
        $parameters['amount'] = $amount[1];
        $parameters['currencyInAbb'] = $amount[0];
        $this->session->set("request_details_response", $parameters['request_details_response']);
        $this->session->set('amountwcurrency', $parameters['request_details_response']['amount']);
        $this->session->set('amount', $parameters['amount']);
        $this->session->set('currencyInAbb', $parameters['currencyInAbb']);

        $this->session->set("Code", $code);
        $this->session->set(
            "image",
            isset($parameters['request_details_response']['image'])
                ? $parameters['request_details_response']['image']
                : ''
        );
        $this->session->set(
            "SenderInitials",
            isset($parameters['request_details_response']['senderName'])
                ? $parameters['request_details_response']['senderName']
                : ''
        );
        $this->session->set(
            "SenderId",
            isset($parameters['request_details_response']['senderId'])
                ? $parameters['request_details_response']['senderId']
                : ''
        );
        $this->session->set(
            "TranSimID",
            isset($parameters['request_details_response']['transactionID'])
                ? $parameters['request_details_response']['transactionID']
                : ''
        );

        $this->session->set(
            "IBAN",
            isset($parameters['request_details_response']['iban'])
                ? $parameters['request_details_response']['iban']
                : ''
        );
        $this->session->set(
            "allowCashin",
            isset($parameters['request_details_response']['allowCashin'])
                ? $parameters['request_details_response']['allowCashin']
                : ''
        );
        $this->session->set(
            "allowExternal",
            isset($parameters['request_details_response']['allowExternal'])
                ? $parameters['request_details_response']['allowExternal']
                : ''
        );
        $this->session->set(
            "allowCardTopup",
            isset($parameters['request_details_response']['allowCardTopup'])
                ? $parameters['request_details_response']['allowCardTopup']
                : ''
        );
        if (isset($parameters['request_details_response']['additionalData'])) {
            $additionalData = $parameters['request_details_response']['additionalData'];
            $additionalData = json_decode($additionalData, true);
        }

        $this->session->set(
            "receiverFname",
            isset($additionalData['receiverFname'])
                ? $additionalData['receiverFname']
                : ''
        );
        $this->session->set(
            "receiverLname",
            isset($additionalData['receiverLname'])
                ? $additionalData['receiverLname']
                : ''
        );
        $this->session->set(
            "ReceiverPhone",
            isset($additionalData['ReceiverPhone'])
                ? $additionalData['ReceiverPhone']
                : ''
        );
        $this->session->set(
            "SenderPhone",
            isset($additionalData['Senderphone'])
                ? $additionalData['Senderphone']
                : ''
        );
        if (isset($additionalData['AuthenticationCode']) || $parameters['request_details_response']['respCode'] == 1) {
            $this->session->set(
                "requestGenerated",
                isset($additionalData['AuthenticationCode'])
                    ? $additionalData['AuthenticationCode']
                    : ''
            );
        }

        return $this->render('rtp/test.html.twig', $parameters);
    }
}
