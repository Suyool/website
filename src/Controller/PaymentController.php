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

    public function __construct(translation $trans,SessionInterface $session)
    {
        $this->trans=$trans;
        $this->session=$session;
    }

    /**
     * @Route("/payment", name="app_payment")
     */
    public function index(Request $request,TranslatorInterface $translator): Response
    {
        $parameters=$this->trans->translation($request,$translator);

        
        $parameters['currency'] = "dolar";
        $parameters['currentPage'] = "payment_landingPage";
// dd(date("ymdHis"));
        $code=$request->query->get('code');

        // $params['url'] = 'Incentive/ValidateEmail?Data=' . $code;
        // $params['type'] = 'get';

        // dd(Helper::send_curl($params));
        $Hash = base64_encode(hash('sha512', $code. date("ymdHis") . $parameters['lang']. 'ZL8hKr2Y8emmJjXkSarPW1tR9Qcyk9ue92XYCbsB3yAG90pPmMNuyNyOyVG15HrPL8PkNt6JHEk0ZAo9MMurqrsCOMJFETFHdjMO', true));
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
    public function generateCode(Request $request,TranslatorInterface $translator): Response
    {
        $parameters=$this->trans->translation($request,$translator);

        $parameters['currency'] = "dollar";
        $parameters['currentPage'] = "generate_Code";

        return $this->render('payment/generateCode.html.twig',$parameters);
    }

    /**
     * @Route("/payment/codeGenerated", name="payment_codeGenerated")
     */
    public function codeGenerated(Request $request,TranslatorInterface $translator): Response
    {
        $parameters=$this->trans->translation($request,$translator);


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
