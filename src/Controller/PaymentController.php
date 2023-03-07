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
    private $atm=false;

    public function __construct(translation $trans,SessionInterface $session,$hash_algo,$certificate)
    {
        $this->trans=$trans;
        $this->session=$session;
        $this->hash_algo=$hash_algo;
        $this->certificate=$certificate;
    }

    /**
     * @Route("/payment/{code}", name="app_payment")
     */
    public function index(Request $request,TranslatorInterface $translator,$code): Response
    {
        $parameters=$this->trans->translation($request,$translator);

        
        $parameters['currency'] = "dolar";
        $parameters['currentPage'] = "payment_landingPage";
// dd(date("ymdHis"));
        // $code=$request->query->get('code');

        // $params['url'] = 'Incentive/ValidateEmail?Data=' . $code;
        // $params['type'] = 'get';

        // dd(Helper::send_curl($params));
        $Hash = base64_encode(hash($this->hash_algo, $code. date("ymdHis") . $parameters['lang']. $this->certificate, true));
        // dd($Hash);
        $form_data = [
            'Code' => $code,
            "DateSent" => date("ymdHis"),
            'Hash' =>  $Hash,
            "lang" => $parameters['lang'],
        ];
        $params['data']= json_encode($form_data);
        $params['url'] = 'Incentive/PaymentDetails';
        /*** Call the api ***/
        $response = Helper::send_curl($params);
        $parameters['payment_details_response'] = json_decode($response, true);
            // dd($parameters);
            $parameters['payment_details_response']['AllowExternal']="True";
        $this->session->set("request_details_response", $parameters['payment_details_response']);
        $this->session->set("code", $code);
        $this->session->set( "image",
            isset($parameters['payment_details_response']['image'])
                ? $parameters['payment_details_response']['image']
                : '');
        $this->session->set("SenderInitials",
            isset($parameters['payment_details_response']['SenderName'])
                ? $parameters['payment_details_response']['SenderName']
                : '');
        $this->session->set("TranSimID",
            isset($parameters['payment_details_response']['TranSimID'])
                ? $parameters['payment_details_response']['TranSimID']
                : '');
                $this->session->set("AllowATM",
            isset($parameters['payment_details_response']['AllowATM'])
                ? $parameters['payment_details_response']['AllowATM']
                : '');
                $this->session->set("AllowExternal",
            isset($parameters['payment_details_response']['AllowExternal'])
                ? $parameters['payment_details_response']['AllowExternal']
                : '');
                $this->session->set("AllowBenName",
            isset($parameters['payment_details_response']['AllowBenName'])
                ? $parameters['payment_details_response']['AllowBenName']
                : '');

        return $this->render('payment/index.html.twig',$parameters);
    }

     /**
     * @Route("/payment/generateCode", name="payment_generateCode")
     */
    public function generateCode(Request $request,TranslatorInterface $translator)
    {
        $parameters=$this->trans->translation($request,$translator);
        $parameters['currency'] = "dollar";
        $parameters['currentPage'] = "generate_Code";
        $code=$request->query->get('code');
        $type=$request->query->get('type');
        if($request->query->get('code')&&$request->query->get('type')){
            
            if($code == $this->session->get('code')){
                // dd("ok");
                /*** Check the payment type if is equal to ATM_KEY or EXTERNAL_KEY ***/
                ($type == 'atm') ? $this->atm = true : '';
                /*** Return result ***/
                
            }else{
                return $this->redirectToRoute('homepage');
            }
            }
            $parameters['atm'] = $this->atm;
            $params_valid = true;

        if($this->atm === true){
            // dd($this->session->get('TranSimID'));
            $timetolive =24;
            if($timetolive == ''){
                $params_valid = false;
                $result['error_message'] = "Please select a time to live";
            }else{
                $payment_type = '1'; //1 for Cashout, 2 for External Transfer
                $Hash = base64_encode(hash($this->hash_algo, $this->session->get('TranSimID') . $payment_type . $timetolive .$parameters['lang']. $this->certificate, true));
                $form_data = [
                    "TranSimID" => $this->session->get('TranSimID'),
                    "PaymentType" => $payment_type,  //1 for Cashout
                    "TimeToLive" => $timetolive,  // 1 for 1 Hour, 2 for 2 Hours.... till 24 Hours
                    'Hash' => $Hash,
                    "lang" => $parameters['lang'],
                ];
                // dd($form_data);
            }
            $parameters['timetolive'] = $timetolive;

            if($params_valid){

                $params['data'] = json_encode($form_data);
                $params['url'] = 'Incentive/SimulatePayment';
                $response = Helper::send_curl($params);
                $parameters['simulate_payment_response'] = json_decode($response, true);
                // $parameters['simulate_payment_response']['ATMCode']="5d3efr";
                // $parameters['simulate_payment_response']['RespDesc']=null;
            }
        }
        
        $parameters['atm'] = $this->atm;
        // dd($parameters);
        return $this->render('payment/generateCode.html.twig',$parameters);
    }

    /**
     * @Route("/codeGenerated", name="payment_codeGenerated")
     */
    public function codeGenerated(Request $request,TranslatorInterface $translator): Response
    {
        $parameters=$this->trans->translation($request,$translator);
        $parameters['code']=$request->query->get('codeATM');

        $parameters['currency'] = "dollar";
        $parameters['currentPage'] = "GenerateCode2";
        

        return $this->render('payment/codeGenerated.html.twig',$parameters);
    }

      /**
     * @Route("/payment/visaCard", name="payment_visaCard")
     */
    public function visaCard(Request $request,TranslatorInterface $translator): Response
    {
        $parameters=$this->trans->translation($request,$translator);

        
        $parameters['currency'] = "dollar";
        $parameters['currentPage'] = "visaCard";

        return $this->render('payment/visaCard.html.twig',$parameters);
    }
}
