<?php

namespace App\Controller;

use App\Service\SuyoolServices;
use App\Translation\translation;
use App\Utils\Helper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class PaymentController extends AbstractController
{
    private $trans;
    private $session;
    private $hash_algo;
    private $certificate;
    private $atm = false;
    private $suyoolServices;

    public function __construct(translation $trans, SessionInterface $session, $hash_algo, $certificate, SuyoolServices $suyoolServices)
    {
        $this->trans = $trans;
        $this->session = $session;
        $this->hash_algo = $hash_algo;
        $this->certificate = $certificate;
        $this->suyoolServices = $suyoolServices;
    }

    /**
     * @Route("/payment/{code}", name="app_payment")
     */
    public function index(Request $request, TranslatorInterface $translator, $code): Response
    {
        $this->session->remove('codeGenerated');
        $parameters = $this->trans->translation($request, $translator);
        $parameters['currentPage'] = "payment_landingPage";
        $parameters['payment_details_response'] = $this->suyoolServices->PaymentDetails($code, $parameters['lang']);
        $parameters['payment_details_response']['allowCashOut']="true";
        if ($parameters['payment_details_response'] != null) {
            if ($parameters['payment_details_response']['respCode'] == 2 || $parameters['payment_details_response']['respCode'] == -1 ||  $parameters['payment_details_response']['transactionID'] == 0) {
                return $this->redirectToRoute("homepage");
            }
            $this->session->set("pequest_details_response", $parameters['payment_details_response']);
            $this->session->set("code", $code);
            $this->session->set(
                "image",
                isset($parameters['payment_details_response']['image'])
                    ? $parameters['payment_details_response']['image']
                    : ''
            );
            $this->session->set(
                "SenderInitials",
                isset($parameters['payment_details_response']['senderName'])
                    ? $parameters['payment_details_response']['senderName']
                    : ''
            );
            $this->session->set(
                "TranSimID",
                isset($parameters['payment_details_response']['transactionID'])
                    ? $parameters['payment_details_response']['transactionID']
                    : ''
            );
            $this->session->set(
                "allowCashOut",
                isset($parameters['payment_details_response']['allowCashOut'])
                    ? $parameters['payment_details_response']['allowCashOut']
                    : ''
            );
            $this->session->set(
                "allowExternal",
                isset($parameters['payment_details_response']['allowExternal'])
                    ? $parameters['payment_details_response']['allowExternal']
                    : ''
            );
            if (isset($parameters['payment_details_response']['additionalData'])) {
                $additionalData = $parameters['payment_details_response']['additionalData'];
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
            if (isset($additionalData['AuthenticationCode']) || $parameters['payment_details_response']['respCode'] == 1) {
                $this->session->set(
                    "codeGenerated",
                    isset($additionalData['AuthenticationCode'])
                        ? $additionalData['AuthenticationCode']
                        : ''
                );
            }
        }
        return $this->render('payment/index.html.twig', $parameters);
    }

    /**
     * @Route("/payment/generateCode", name="payment_generateCode")
     */
    public function generateCode(Request $request, TranslatorInterface $translator)
    {
        $code = $this->session->get('codeGenerated');
        if (isset($code)) {
            $parameters['cashout']['data'] = $code;
            return $this->render('payment/codeGenerated.html.twig', $parameters);
        }
        $submittedToken = $request->request->get('token');
        $parameters = $this->trans->translation($request, $translator);
        $parameters = $this->trans->translation($request, $translator);
        $code = $this->session->get('code');
        $parameters['ReceiverPhone'] = $this->session->get('ReceiverPhone');
        if (isset($_POST['submit'])) {
            if (!empty($_POST['receiverfname']) && !empty($_POST['receiverlname'])) {
                $parameters['cashout'] = $this->suyoolServices->PaymentCashout($this->session->get('TranSimID'), $_POST['receiverfname'], $_POST['receiverlname']);
                if ($parameters['cashout']['globalCode'] == 0 && $parameters['cashin']['flagCode'] != 1 ) {
                    $parameters['imgsrc']="build/images/Loto/error.png";
                    $parameters['title']="Unable to create code";
                    $parameters['description']="We are unable to create a cashout code.<br>
                    Contact our call center at 81-484000 for further assistance.";
                    $parameters['button']="Call Call Center";
                    return $this->render('payment/generateCode.html.twig', $parameters);
                } else if($parameters['cashout']['flagCode'] == 1){
                    $this->session->set(
                        "codeGenerated",
                        isset($parameters['cashout']['data'])
                            ? $parameters['cashout']['data']
                            : ''
                    );
                    return $this->render('payment/codeGenerated.html.twig', $parameters);
                }
            } else {
                $parameters['cashout']['globalCode'] = 0;
                $parameters['message'] = 'All input are required';
            }
        }
        return $this->render('payment/generateCode.html.twig', $parameters);
    }

    /**
     * @Route("/codeGenerated", name="payment_codeGenerated")
     */
    public function codeGenerated(Request $request, TranslatorInterface $translator): Response
    {
        $parameters = $this->trans->translation($request, $translator);
        $parameters['code'] = $request->query->get('codeATM');
        $parameters['currency'] = "dollar";
        $parameters['currentPage'] = "GenerateCode2";

        return $this->render('payment/codeGenerated.html.twig', $parameters);
    }

    /**
     * @Route("/payment/visaCard", name="payment_visaCard")
     */
    public function visaCard(Request $request, TranslatorInterface $translator): Response
    {
        $parameters = $this->trans->translation($request, $translator);
        $parameters['currency'] = "dollar";
        $parameters['currentPage'] = "visaCard";

        return $this->render('payment/visaCard.html.twig', $parameters);
    }
}
