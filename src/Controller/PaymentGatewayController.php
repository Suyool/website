<?php


namespace App\Controller;


use App\Entity\topup\invoices;
use App\Entity\topup\merchants;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class PaymentGatewayController extends AbstractController
{
    private $mr;
    public function __construct(ManagerRegistry $mr)
    {
        $this->mr = $mr->getManager('topup');
    }

    /**
     * @Route("/payment_gateway", name="payment_gateway_main")
     */
    public function index(Request $request,SessionInterface $session)
    {
        $data = json_decode($request->getContent(),true);

        $userAgent = $request->headers->get('User-Agent');

        $isMobile = false;

        // Perform some basic checks to determine if it's a mobile user agent
        if (strpos($userAgent, 'Mobile') !== false || strpos($userAgent, 'Android') !== false) {
            $isMobile = true;
        }

//        $merchantId = $data['MerchantAccountID'];
//        $order_id = $data['TransactionID'];
//        $amount = $data['Amount'];
//        $currency = $data['Currency'];
//        $order_desc = $data['AdditionalInfo'];
//        $secureHash = $data['SecureHash'];
//        $callbackURL = $data['callBackURL'];
//        $TS = $data['TS'];
//        $TranTS = $data['TranTS'];
        $merchantId = "90";
        $order_id ="258" ;
        $amount = "1.00";
        $currency = "USD";
        $order_desc = "";
        $secureHash = "yGIj47xM67ZAp9RA8zf8lukHqbmMbooTQ7pcBLazb4ahURgbjh7wTKn+5Oe3J33duHjQa9pkyrHe6c3MwVIMRA==";
        $callbackURL = "https://suyool.com";
        $TS = "1701166428000";
        $TranTS = "1701166428000" ;

        // Store data in the session
        $session->set('payment_data', [
            'MerchantID' => $merchantId,
            'Amount' => $amount,
            'Currency' => $currency,
            'SecureHash' => $secureHash,
            'TranID' => $order_id,
            'AdditionalInfo' => $order_desc,
            'CallBackURL'=> $callbackURL,
            'TS' => $TS,
            'TranTS' => $TranTS
        ]);

        return $this->render('paymentGateway/index.html.twig',[
            'is_mobile' => $isMobile,
        ]);
    }

    /**
     * @Route("/payment_gateway_QR", name="payment_gateway_qr")
     */
    public function payWithQR()
    {
        return $this->render('paymentGateway/qr.html.twig');
    }

    /**
     * @Route("/merchant/v1/invoices", name="payment_api_invoice")
     */
    public function api(Request $request,SessionInterface $session)
    {
        $apiKey = $request->headers->get('X-API-Key');

        $apiKeydata = "2323dsad12e242142142521521sad12221";

        $data = json_decode($request->getContent(),true);


        $merchantId = $data['MerchantAccountID'];
        $order_id = $data['TransactionID'];
        $amount = $data['Amount'];
        $currency = $data['Currency'];
        $order_desc = $data['AdditionalInfo'];
        $merchant = $this->mr->getRepository(merchants::class)->findOneBy(['merchantMid' => $merchantId]);

        if($apiKeydata == $apiKey){

            $invoice = new invoices();
            $invoice->setMerchantsId($merchant);
            $invoice->setMerchantOrderId($order_id);
            $invoice->setAmount($amount);
            $invoice->setCurrency($currency);
            $invoice->setMerchantOrderDesc($order_desc);
            $invoice->setStatus('Pending');



//            $apiHost = 'https://externalservices.nicebeach-895ccbf8.francecentral.azurecontainerapps.io/';
//
//            $response = $this->client->request('POST', $apiHost . 'api/OnlinePayment/PayQR', [
//                'body' => $data,
//                'headers' => [
//                    'Content-Type' => 'application/json'
//                ]
//            ]);
//
//            $content = $response->getContent();
//            $response = json_decode($content, true);

            $response = array('reference'=>'ref-123');
            if(!empty($response)) {
                $invoice->setReference($response['reference']);
            }
            $this->mr->persist($invoice);
            $this->mr->flush();

            $url = "http://suyool.ls/payment-gateway";
            $array = [
               "url" => $url,
                "success" => true
            ];
            $res = json_encode($array,true);

            return new Response($res);

        }else {
            return new Response("your are not eligible to continue");

        }
    }
}