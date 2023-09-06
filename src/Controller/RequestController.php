<?php

namespace App\Controller;

use App\Service\SuyoolServices;
use App\Utils\Helper;
use App\Translation\translation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class RequestController extends AbstractController
{
    private $trans;
    private $session;
    private $hash_algo;
    private $certificate;
    private $cashinput = false;
    private $suyoolServices;

    public function __construct(translation $trans, SessionInterface $session, $hash_algo, $certificate, SuyoolServices $suyoolServices)
    {
        $this->trans = $trans;
        $this->session = $session;
        $this->hash_algo = $hash_algo;
        $this->certificate = $certificate;
        $this->suyoolServices = $suyoolServices;
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
     * @Route("/request/{code}", name="app_request")
     */
    public function index(Request $request, TranslatorInterface $translator, $code): Response
    {
        $this->session->remove('requestGenerated');
        $parameters = $this->trans->translation($request, $translator);
        $parameters['currentPage'] = "payment_landingPage";
        $parameters['request_details_response'] = $this->suyoolServices->RequestDetails($code, $parameters['lang']);

        if ($parameters['request_details_response']['respCode'] == 2 || $parameters['request_details_response']['respCode'] == -1 || $parameters['request_details_response']['transactionID'] == 0) {
            return $this->redirectToRoute("homepage");
        }
        $this->session->set("request_details_response", $parameters['request_details_response']);
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
            "SenderInitials",
            isset($parameters['request_details_response']['senderName'])
                ? $parameters['request_details_response']['senderName']
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
            "IBAN",
            isset($parameters['request_details_response']['iban'])
                ? $parameters['request_details_response']['iban']
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
        if (isset($additionalData['AuthenticationCode']) || $parameters['request_details_response']['respCode'] == 1) {
            $this->session->set(
                "requestGenerated",
                isset($additionalData['AuthenticationCode'])
                    ? $additionalData['AuthenticationCode']
                    : ''
            );
        }

        return $this->render('request/index.html.twig', $parameters);
    }

    /**
     * @Route("/request/generateCode", name="generateCode")
     */
    public function generateCode(Request $request, TranslatorInterface $translator)
    {
        $code = $this->session->get('requestGenerated');
        if (isset($code)) {
            $parameters['cashin']['data'] = $code;
            return $this->render('request/codeGenerated.html.twig', $parameters);
        }
        $submittedToken = $request->request->get('token');
        $parameters = $this->trans->translation($request, $translator);
        $parameters = $this->trans->translation($request, $translator);
        $code = $this->session->get('code');
        $parameters['ReceiverPhone'] = $this->session->get('ReceiverPhone');
        if (isset($_POST['submit'])) {
            if (!empty($_POST['fname']) && !empty($_POST['lname'])) {
                $parameters['cashin'] = $this->suyoolServices->PaymentCashin($this->session->get('TranSimID'), $_POST['fname'], $_POST['lname']);

                if ($parameters['cashin']['globalCode'] == 0) {
                    $parameters['message'] = $parameters['cashin']['message'];
                    return $this->render('request/generateCode.html.twig', $parameters);
                } else {
                    $this->session->set(
                        "requestGenerated",
                        isset($parameters['cashin']['data'])
                            ? $parameters['cashin']['data']
                            : ''
                    );
                    return $this->render('request/codeGenerated.html.twig', $parameters);
                }
            } else {
                $parameters['cashin']['globalCode'] = 0;
                $parameters['message'] = 'All input are required';
            }
        }

        return $this->render('request/generateCode.html.twig', $parameters);
    }

    /**
     * @Route("/request/codeGenerated", name="codeGenerated")
     */
    public function codeGenerated(Request $request, TranslatorInterface $translator): Response
    {
        $parameters = $this->trans->translation($request, $translator);
        $parameters['currency'] = "dollar";
        $parameters['currentPage'] = "GenerateCode2";

        return $this->render('request/codeGenerated.html.twig', $parameters);
    }

    /**
     * @Route("/request/visaCard", name="visaCard")
     */
    public function visaCard(Request $request, TranslatorInterface $translator): Response
    {
        $parameters = $this->trans->translation($request, $translator);
        $parameters['currency'] = "dollar";
        $parameters['currentPage'] = "visaCard";

        return $this->render('request/visaCard.html.twig', $parameters);
    }
}
