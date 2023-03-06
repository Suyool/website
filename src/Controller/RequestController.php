<?php

namespace App\Controller;

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

    public function __construct(translation $trans ,SessionInterface $session)
    {
        $this->trans=$trans;
        $this->session = $session;
    }

    function GetInitials($name) {
        $word = explode(" ", $name);
        $initials = "";

        foreach ($word as $w) {
            $initials .= ucwords($w[0]);
        }

        return $initials;
    }
    
    /**
     * @Route("/request", name="app_request")
     */
    public function index(Request $request,TranslatorInterface $translator): Response
    {
        $parameters=$this->trans->translation($request,$translator);
        $parameters['currency'] = "dolar";
        $parameters['currentPage'] = "payment_landingPage";


        // $code = $request->query->get('code');
        $code = "Rgnd3";
        $dateSent = date("ymdHis"); 
        $Hash = base64_encode(hash('sha512', 'Rgnd3'. date("ymdHis") . 'en'. 'ZL8hKr2Y8emmJjXkSarPW1tR9Qcyk9ue92XYCbsB3yAG90pPmMNuyNyOyVG15HrPL8PkNt6JHEk0ZAo9MMurqrsCOMJFETFHdjMO', true));
        $form_data = [
            'Code' => $code,
            "DateSent" => $dateSent,
            'Hash' =>  $Hash,
            "lang" => $parameters['lang'],
        ];
        $params['data']= json_encode($form_data);
        $params['url'] = 'Incentive/RequestDetails';
        $response = Helper::send_curl($params);

        $parameters['request_details_response'] = json_decode($response, true);

        $parameters['request_details_response']['RespTitle'] = str_replace(array("{PayerName}","{Amount}",), array($parameters['request_details_response']['SenderName'],$parameters['request_details_response']['Amount']), $parameters['request_details_response']['RespTitle']);
        $parameters['request_details_response']['SenderProfilePic']='';

        $this->session->set("request_details_response", $parameters['request_details_response']);
        $this->session->set("Code", $code);
        $this->session->set( "image",
            isset($parameters['request_details_response']['image']) 
                ? $parameters['request_details_response']['image']
                : '');
        $this->session->set("SenderInitials",
            isset($parameters['request_details_response']['SenderName'])
                ? $this->GetInitials($parameters['request_details_response']['SenderName'])
                : '');
        $this->session->set("IBAN",
            isset($parameters['request_details_response']['IBAN'])
                ? $parameters['request_details_response']['IBAN']
                : '');

        // $session = $request->getSession();
        // dd($session);

        return $this->render('request/index.html.twig',$parameters);
    }

     /**
     * @Route("/request/generateCode", name="generateCode")
     */
    public function generateCode(Request $request,TranslatorInterface $translator): Response
    {
        $parameters=$this->trans->translation($request,$translator);

        $parameters['currency'] = "dollar";
        $parameters['currentPage'] = "generate_Code";

        return $this->render('request/generateCode.html.twig',$parameters);
    }

    /**
     * @Route("/request/codeGenerated", name="codeGenerated")
     */
    public function codeGenerated(Request $request,TranslatorInterface $translator): Response
    {
        $parameters=$this->trans->translation($request,$translator);


        $parameters['currency'] = "dollar";
        $parameters['currentPage'] = "GenerateCode2";

        return $this->render('request/codeGenerated.html.twig',$parameters);
    }

      /**
     * @Route("/request/visaCard", name="visaCard")
     */
    public function visaCard(Request $request,TranslatorInterface $translator): Response
    {
        $parameters=$this->trans->translation($request,$translator);

        
        $parameters['currency'] = "dollar";
        $parameters['currentPage'] = "visaCard";

        return $this->render('request/visaCard.html.twig',$parameters);
    }
}
