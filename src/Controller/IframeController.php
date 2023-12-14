<?php


namespace App\Controller;


use App\Entity\topup\invoices;
use App\Entity\topup\merchants;
use App\Service\ShopifyServices;
use App\Utils\Helper;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class IframeController extends AbstractController
{
    private $client;
    private $mr;

    public function __construct(HttpClientInterface $client,ManagerRegistry $mr)
    {
        $this->client = $client;
        $this->mr = $mr->getManager('topup');

    }

    /**
     * @Route("/paysuyoolqr/", name="app_pay_suyool_qr")
     */
    public function paySuyoolQR(Request $request,SessionInterface $session): Response
    {

        if(!empty($session->get('payment_data'))){
            $data = $session->get('payment_data');
        }else {
            $data = $request->query->all();
        }
        return $this->processPayment($data, 'live');

    }

    /**
     * @Route("/paysuyoolqrtest/", name="app_pay_suyool_qr_test")
     */
    public function paySuyoolQRTest(Request $request): Response
    {
        return $this->processPayment($request, 'test');
    }

    private function processPayment($data, string $env): Response
    {
        if(!empty($data)){
            $response = $this->windowProcess($data, $env);
            $TranID = $data['TranID'] ?? '';
            if ($env == 'live') {
                $pictureUrl = $response['pictureURL'];
                $returnText = $response['returnText'];
            } else {
                $pictureUrl = $response['PictureURL'];
                $returnText = $response['ReturnText'];
            }

            $showQR = $pictureUrl ? 'displayBlock' : '';
            $main_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
            return $this->render('iframe/pay-qr.html.twig', [
                'pictureURL' => $pictureUrl,
                'order_id' => $TranID,
                'ReturnText' => $returnText,
                'displayBlock' => $showQR,
                'merchantId' => $data['MerchantID'],
                'CallBackURL' => $data['CallBackURL'],
                'env' => $env,
                'main_url' => $main_url,

            ]);
        }else {
            return new Response('No Data received');
        }
    }

    public function windowProcess($data, $env)
    {
        $TranID = $data['TranID'] ?? '';
        $amount = $data['Amount'] ?? '';
        $currency = $data['Currency'] ?? '';
        $CallBackURL = rawurldecode($data['CallBackURL'] ?? '');
        $secureHash = rawurldecode($data['SecureHash'] ?? '');
        $TS = $data['TS'] ?? '';
        $TranTS = $data['TranTS'] ?? '';
        $merchantId = $data['MerchantID'] ?? '';
        $additionalInfo = $data['AdditionalInfo'] ?? '';

        $merchant = $this->mr->getRepository(merchants::class)->findOneBy(['merchantMid' => $merchantId]);
        $existingInvoice = $this->mr->getRepository(invoices::class)->findOneBy([
            'merchants' => $merchant,
            'merchantOrderId' => $TranID
        ]);
        if ($existingInvoice) {
            $existingInvoice->setPaymentMethod('QR Payment Gateway');
            // Update other fields as needed
            $this->mr->persist($existingInvoice);
            $this->mr->flush();

        } else {
        $invoice = new invoices();
        $invoice->setMerchantsId($merchant);
        $invoice->setMerchantOrderId($TranID);
        $invoice->setAmount($amount);
        $invoice->setCurrency($currency);
        $invoice->setMerchantOrderDesc($additionalInfo);
        $invoice->setPaymentMethod('QR Payment Gateway');
        $invoice->setStatus('Pending');

        $this->mr->persist($invoice);
        $this->mr->flush();
            return 'New invoice created';
        }
        if ($TranID !== '' && $amount !== '' && $currency !== '' && $secureHash !== '' && $TS !== '' && $merchantId !== '') {
            $transactionData = [
                'TransactionID' => $TranID,
                'Amount' => $amount,
                'Currency' => $currency,
                'SecureHash' => $secureHash,
                'TS' => $TS,
                'callBackURL' => $CallBackURL,
                'TranTS' => $TranTS,
                'MerchantAccountID' => $merchantId,
                'AdditionalInfo' => $additionalInfo,
            ];
            if ($env == 'live')
                $url = "api/OnlinePayment/PayQR";
            else
                $url = "PayQR";


            $params = [
                'data' => json_encode($transactionData),
                'url' => $url,
                'env' => $env,
            ];

            return $this->getQr($params);
        }
    }

    public function getQr($data)
    {
        if ($data['env'] == 'live') {
            $apiHost = 'https://externalservices.nicebeach-895ccbf8.francecentral.azurecontainerapps.io/';
        } else {
            $apiHost = 'https://online.suyool.money/';
        }
        $response = $this->client->request('POST', $apiHost . $data['url'], [
            'body' => $data['data'],
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);

        $content = $response->getContent();
        $response = json_decode($content, true);

        return $response;
    }

    /**
     * @Route("/paysuyoolmobile/", name="app_pay_suyool_mobile")
     */
    public function paySuyoolMobile(Request $request,SessionInterface $session): Response
    {
        if(!empty($session->get('payment_data'))){
            $data = $session->get('payment_data');
        }else {
            $data = $request->query->all();
        }
        $env = "live";
        $TranID = isset($data['TranID']) ? $data['TranID'] : "";
        $amount = isset($data['Amount']) ? $data['Amount'] : "";
        $currency = isset($data['Currency']) ? $data['Currency'] : "";
        $CallBackURL = isset($data['CallBackURL']) ? rawurldecode($data['CallBackURL']) : "";
        $secureHash = isset($data['SecureHash']) ? rawurldecode($data['SecureHash']) : "";
        $TS = isset($data['TS']) ? $data['TS'] : "";
        $TranTS = isset($data['TranTS']) ? $data['TranTS'] : "";
        $merchantId = isset($data['MerchantID']) ? $data['MerchantID'] : "";
        $CurrentUrlClient = isset($data['currentUrl']) ? rawurldecode($data['currentUrl']) : "";
        $Browsertype = isset($data['browsertype']) ? $data['browsertype'] : Helper::getBrowserType();
        $additionalInfo = isset($data['AdditionalInfo']) ? $data['AdditionalInfo'] : "";
        $main_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";

        $merchant = $this->mr->getRepository(merchants::class)->findOneBy(['merchantMid' => $merchantId]);
        $existingInvoice = $this->mr->getRepository(invoices::class)->findOneBy([
            'merchants' => $merchant,
            'merchantOrderId' => $TranID
        ]);
        if ($existingInvoice) {
            $existingInvoice->setPaymentMethod('QR Payment Gateway');
            // Update other fields as needed
            $this->mr->persist($existingInvoice);
            $this->mr->flush();

        } else {
            $invoice = new invoices();
            $invoice->setMerchantsId($merchant);
            $invoice->setMerchantOrderId($TranID);
            $invoice->setAmount($amount);
            $invoice->setCurrency($currency);
            $invoice->setMerchantOrderDesc($additionalInfo);
            $invoice->setPaymentMethod('Mobile Payment Gateway');
            $invoice->setStatus('Pending');

            $this->mr->persist($invoice);
            $this->mr->flush();
        }
        if ($TranID !== '' && $amount !== '' && $currency !== '' && $secureHash !== '' && $TS !== '' && $merchantId !== '') {
            $transactionData = [
                'TransactionID' => "$TranID",
                'Amount' => $amount,
                'Currency' => $currency,
                'SecureHash' => $secureHash,
                'TS' => "$TS",
                'TranTS' => "$TranTS",
                "CallBackURL" => $CallBackURL,
                'MerchantAccountID' => $merchantId,
                'currentUrl' => "$CurrentUrlClient",
                'browsertype' => $Browsertype,
                'AdditionalInfo' => $additionalInfo,
            ];

            $jsonEncoded = json_encode($transactionData);
            $appUrl = "suyoolpay://suyool.com/suyool=?" . $jsonEncoded;

            return $this->render('iframe/pay-mobile.html.twig', [
                'deepLink' => $appUrl,
                'order_id' => $TranID,
                'env' => $env,
                'main_url' => $main_url,
                'merchantID'=>$merchantId,
                'callBackURL' =>$CallBackURL
            ]);
        }
    }

    /**
     * @Route("/iframe_check_status/", name="app_Iframe_check_status")
     */
    public function checkIFrameStatus(Request $request)
    {
        $data = $request->request->all();
        $transactionId = $data['transaction_id'];
        $merchantId = $data['merchant_id'];
        $callBackURL = $data['callBack_URL'];
        $env = $data['env'];
        if ($transactionId != '' && $merchantId != '') {
            $timestamp = date("ymdHis"); //Format: 180907071749 = 07/09/2018 7:17:49am - UTC time
            $merchant = $this->mr->getRepository(merchants::class)->findOneBy(['merchantMid' => $merchantId]);
            $certificate = $merchant->getCertificate();
            $secure = $timestamp . $transactionId . $certificate;
            $secureHash = base64_encode(hash('sha512', $secure, true));
            $json = [
                "transactionID" => $transactionId,
                "ts" => $timestamp,
                "merchantAccountID" => $merchantId,
                "secureHash" => $secureHash
            ];
            $params['data'] = json_encode($json);
            if ($env == 'live') {
                $apiHost = 'https://externalservices.nicebeach-895ccbf8.francecentral.azurecontainerapps.io/';
                $params['url'] = 'api/OnlinePayment/CheckQRPaymentStatus';

            } else {
                $apiHost = 'https://online.suyool.money/';
                $params['url'] = 'CheckQRPaymentStatus';
            }
            $response = $this->client->request('POST', $apiHost . $params['url'], [
                'body' => $params['data'],
                'headers' => [
                    'Content-Type' => 'application/json'
                ]
            ]);
            $result = json_decode($response->getContent(), true);
            $responseContent = json_encode([
                'Flag' => $result['flag'],
                'ReferenceNo' => $result['referenceNo'],
                'TranID' => $result['tranID'],
                'ReturnText' => $result['returnText'],
                'SecureHash' => $result['secureHash'],
                'AdditionalInfo' => $result['additionalInfo'],
            ]);
            $merchant = $this->mr->getRepository(merchants::class)->findOneBy(['merchantMid' => $merchantId]);

            $invoice = $this->mr->getRepository(invoices::class)->findOneBy(['merchants' => $merchant->getId(),'merchantOrderId'=> $transactionId]);

            if($invoice) {
                if ($result['flag'] == 1){
                    $invoice->setStatus('completed');
                }elseif ($result['flag'] == 3){
                    $invoice->setStatus('canceled');
                }
                $this->mr->persist($invoice);
                $this->mr->flush();
            }

            $http_origin = $_SERVER['HTTP_ORIGIN'];

            $response = new Response($responseContent);
            $response->headers->set('Content-Type', 'application/json');
            $response->headers->set('Access-Control-Allow-Credentials', 'true');
            $response->headers->set('Access-Control-Allow-Origin', $http_origin);

            return $response;
        }
        return new Response('Invalid request', Response::HTTP_BAD_REQUEST);
    }
}