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
    private $hash_algo;
    private $certificate;
    private $cashinput=false;

    public function __construct(translation $trans ,SessionInterface $session,$hash_algo,$certificate)
    {
        $this->trans=$trans;
        $this->session = $session;
        $this->hash_algo=$hash_algo;
        $this->certificate=$certificate;
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
     * @Route("/request/{code}", name="app_request")
     */
    public function index(Request $request,TranslatorInterface $translator,$code): Response
    {
        $parameters=$this->trans->translation($request,$translator);
        // $parameters['currency'] = "LL";
        $parameters['currentPage'] = "payment_landingPage";

        // $code = $request->query->get('code');
        // $code = "Rgnd3";
        $dateSent = date("ymdHis"); 
        // $Hash = base64_encode(hash('sha512', 'Rgnd3'. date("ymdHis") . 'en'. 'ZL8hKr2Y8emmJjXkSarPW1tR9Qcyk9ue92XYCbsB3yAG90pPmMNuyNyOyVG15HrPL8PkNt6JHEk0ZAo9MMurqrsCOMJFETFHdjMO', true));
        $Hash = base64_encode(hash($this->hash_algo, $code. date("ymdHis") . $parameters['lang']. $this->certificate, true));

        $form_data = [
            'code' => $code,
            "dateSent" => $dateSent,
            'hash' =>  $Hash,
            "lang" => $parameters['lang'],
        ];
        // dd($form_data);
        $params['data']= json_encode($form_data);
        $params['url'] = 'SuyoolGlobalApi/api/Payment/RequestDetails';
        $response = Helper::send_curl($params);
// dd($response);
        $parameters['request_details_response'] = json_decode($response, true);
        // dd($parameters['request_details_response']);
        // $parameters['currency']=$parameters['request_details_response']['currency'];

        // dd($parameters);

        // $parameters['request_details_response']['respTitle'] = str_replace(array("{PayerName}","{Amount}",), array($parameters['request_details_response']['senderName'],$parameters['request_details_response']['amount']), $parameters['request_details_response']['respTitle']);
        // $parameters['request_details_response']['SenderProfilePic']='';
// dd($parameters['request_details_response']['image']);
        $this->session->set("request_details_response", $parameters['request_details_response']);
        $this->session->set("Code", $code);
        $this->session->set( "image",
            isset($parameters['request_details_response']['image']) 
                ? $parameters['request_details_response']['image']
                : '');
                $this->session->set("SenderInitials",
                isset($parameters['request_details_response']['senderName'])
                    ? $parameters['request_details_response']['senderName']
                    : '');
            $this->session->set("TranSimID",
                isset($parameters['request_details_response']['transactionID'])
                    ? $parameters['request_details_response']['transactionID']
                    : '');
                    $this->session->set("allowCashin",
                isset($parameters['request_details_response']['allowCashin'])
                    ? $parameters['request_details_response']['allowCashin']
                    : '');
                    $this->session->set("allowExternal",
                isset($parameters['request_details_response']['allowExternal'])
                    ? $parameters['request_details_response']['allowExternal']
                    : '');
        $this->session->set("IBAN",
            isset($parameters['request_details_response']['iban'])
                ? $parameters['request_details_response']['iban'] 
                : '');

        // $session = $request->getSession();
        // dd($session);
        // dd($parameters);

        return $this->render('request/index.html.twig',$parameters);
    }

     /**
     * @Route("/request/generateCode", name="generateCode")
     */
    public function generateCode(Request $request,TranslatorInterface $translator)
    {
        $submittedToken=$request->request->get('token');
        $parameters=$this->trans->translation($request,$translator);
        $parameters=$this->trans->translation($request,$translator);
        $code = $this->session->get('code');
        if(isset($_POST['submit'])){
            if($this->isCsrfTokenValid('request', $submittedToken) && !empty($_POST['fname']) && !empty($_POST['lname']) && !empty($_POST['mobile'])){

            $Hash = base64_encode(hash($this->hash_algo, $this->session->get('TranSimID'). $_POST['fname'] . $_POST['lname'] . $this->certificate, true));
            // dd($Hash);
            $form_data = [
                'transactionId' => $this->session->get('TranSimID'),
                'receiverFname'=>$_POST['fname'],
                'receiverLname'=>$_POST['lname'],
                'hash' =>  $Hash
            ];
    
            
    
            $params['data']= json_encode($form_data);
           
            // dd($params['data']);
            $params['url'] = 'SuyoolGlobalApi/api/NonSuyooler/NonSuyoolerCashIn';
    
            $response = Helper::send_curl($params);
            $parameters['cashin'] = json_decode($response, true);
            $parameters['cashin']['globalCode']=1;
            $parameters['cashin']['data' ]= "099-112-999";
            if($parameters['cashin']['globalCode'] == 0){
                // dd($params['data']);
                $parameters['message']=$parameters['cashin']['message'];
                return $this->render('request/generateCode.html.twig',$parameters);
            }else{
                return $this->render('request/codeGenerated.html.twig',$parameters);
            }
            // dd($parameters['cashout']);
        }
            else{
                $parameters['cashin']['globalCode']=0;
                $parameters['message']='All input are required';
            }
            
        }
        
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
