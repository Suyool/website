<?php

namespace App\Controller;

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

    public function __construct(translation $trans, SessionInterface $session, $hash_algo, $certificate)
    {
        $this->trans = $trans;
        $this->session = $session;
        $this->hash_algo = $hash_algo;
        $this->certificate = $certificate;
    }

    /**
     * @Route("/payment/{code}", name="app_payment")
     */
    public function index(Request $request, TranslatorInterface $translator, $code): Response
    {
        // dd("ok");
        $this->session->remove('codeGenerated');
        $parameters = $this->trans->translation($request, $translator);


        $parameters['currentPage'] = "payment_landingPage";
        // dd(date("ymdHis"));
        // $code=$request->query->get('code');

        // $params['url'] = 'Incentive/ValidateEmail?Data=' . $code;
        // $params['type'] = 'get';

        // dd(Helper::send_curl($params));
        $Hash = base64_encode(hash($this->hash_algo, $code . date("ymdHis") . $parameters['lang'] . $this->certificate, true));
        // dd($Hash);
        $form_data = [
            'code' => $code,
            "dateSent" => date("ymdHis"),
            'hash' =>  $Hash,
            "lang" => $parameters['lang'],
        ];

        // dd($form_data);

        $params['data'] = json_encode($form_data);
        // dd($params['data']);

        $params['url'] = 'SuyoolGlobalAPIs/api/Payment/PaymentDetails';
        /*** Call the api ***/
        $response = Helper::send_curl($params);
        // dd($response);
        $parameters['payment_details_response'] = json_decode($response, true);
        // dd($parameters['payment_details_response']);

        if ($parameters['payment_details_response'] != null) {
            // $parameters['currency'] = $parameters['payment_details_response']['currency'];
            // dd($parameters['payment_details_response']);
            // $parameters['payment_details_response']['allowExternal']="True";
            // dd
            if($parameters['payment_details_response']['respCode'] == 2){
                // dd();
                return $this->redirectToRoute("homepage");
            }
            $this->session->set("request_details_response", $parameters['payment_details_response']);
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

            $additionalData = $parameters['payment_details_response']['additionalData'];
            $additionalData = json_decode($additionalData, true);
            // dd($additionalData);
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
            if(isset($additionalData['AuthenticationCode']) || $parameters['payment_details_response']['respCode'] == 1){
                $this->session->set(
                    "codeGenerated",
                    isset($additionalData['AuthenticationCode'])
                        ? $additionalData['AuthenticationCode']
                        : ''
                );
            }
           

            // $parameters['']
            // dd(json_decode($additionalData,true));

        }
        return $this->render('payment/index.html.twig', $parameters);
    }

    /**
     * @Route("/payment/generateCode", name="payment_generateCode")
     */
    public function generateCode(Request $request, TranslatorInterface $translator)
    {
        $code=$this->session->get('codeGenerated');
        if(isset($code)){
            $parameters['cashout']['data']=$code;
            return $this->render('payment/codeGenerated.html.twig', $parameters);
        }
        $submittedToken = $request->request->get('token');
        $parameters = $this->trans->translation($request, $translator);
        $parameters = $this->trans->translation($request, $translator);
        $code = $this->session->get('code');
        $parameters['ReceiverPhone']=$this->session->get('ReceiverPhone');
        if (isset($_POST['submit'])) {
            if ($this->isCsrfTokenValid('payment', $submittedToken) && !empty($_POST['receiverfname']) && !empty($_POST['receiverlname'])) {
                $Hash = base64_encode(hash($this->hash_algo, $this->session->get('TranSimID') . $_POST['receiverfname'] . $_POST['receiverlname'] . $this->certificate, true));
                // dd($Hash);
                $form_data = [
                    'transactionId' => $this->session->get('TranSimID'),
                    'receiverFname' => $_POST['receiverfname'],
                    'receiverLname' => $_POST['receiverlname'],
                    'hash' =>  $Hash
                ];



                $params['data'] = json_encode($form_data);

                // dd($params['data']);
                $params['url'] = 'SuyoolGlobalAPIs/api/NonSuyooler/NonSuyoolerCashOut';
                // dd($params['data']);
                $response = Helper::send_curl($params);
                $parameters['cashout'] = json_decode($response, true);
                // dd($parameters['cashout']);
                // $parameters['cashout']['globalCode']=1;
                // $parameters['cashout']['data']=123;
                if ($parameters['cashout']['globalCode'] == 0) {
                    // dd("ok");
                    
                    $parameters['message'] = $parameters['cashout']['message'];
                    
                    return $this->render('payment/generateCode.html.twig', $parameters);
                } else {
                    $this->session->set(
                        "codeGenerated",
                        isset($parameters['cashout']['data'])
                            ? $parameters['cashout']['data']
                            : ''
                    );
                    return $this->render('payment/codeGenerated.html.twig', $parameters);
                }
                // dd($parameters['cashout']);
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
