<?php


namespace App\Controller;


use App\Entity\Gift2Games\IframeLog;
use App\Entity\Invoices\invoices;
use App\Entity\Invoices\merchants;
use App\Entity\Invoices\test_invoices;
use App\Utils\Helper;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class IframeController extends AbstractController
{
    private $client;
    private $session;
    private $mr;
    private $SUYOOL_API_HOST;

    public function __construct(HttpClientInterface $client,ManagerRegistry $mr,SessionInterface $session)
    {
        $this->client = $client;
        $this->mr = $mr->getManager('invoices');
        $this->defaultDB=$mr->getManager('default');

        $this->session = $session;
        if ($session!=null && $session->has('simulation')) {
            $simulation = $session->get('simulation');
        }

        if ($_ENV['APP_ENV'] == "test") {
            $this->SUYOOL_API_HOST = 'http://10.20.80.62/api/OnlinePayment';
        }
        else if ($_ENV['APP_ENV'] == "sandbox" || $_ENV['APP_ENV'] == 'dev' || (isset($simulation) && $simulation == "true") || (isset($_COOKIE['simulation']) && $_COOKIE['simulation']=="true")){
            $this->SUYOOL_API_HOST = 'https://externalservices.suyool.money/api/OnlinePayment';
        }
        else {
            $this->SUYOOL_API_HOST = 'https://externalservices.nicebeach-895ccbf8.francecentral.azurecontainerapps.io/api/OnlinePayment';
        }
    }

    /**
     * @Route("/paysuyoolqr/", name="app_pay_suyool_qr")
     */
    public function paySuyoolQR(Request $request): Response
    {

        if (!empty($this->session->get('payment_data'))) {
            $data = $this->session->get('payment_data');
        } else {
            $data = $request->query->all();
            $data['SecureHash'] = str_replace(' ', '+', $data['SecureHash']);
        }

        return $this->processPayment($data, 'live');
    }

    /**
     * @Route("/paysuyoolqrtest/", name="app_pay_suyool_qr_test")
     */
    public function paySuyoolQRTest(Request $request): Response
    {
        if (!empty($this->session->get('payment_data'))) {
            $data = $this->session->get('payment_data');
        } else {
            $data = $request->query->all();
        }
        return $this->processPayment($data, 'test');
    }

    private function processPayment(array $data, string $env): Response
    {
        $data['SecureHash'] = str_replace(' ', '+', $data['SecureHash']);

        if (!empty($data)) {
            $response = $this->windowProcess($data, $env);

            $TranID = $data['TranID'] ?? '';
            $callbackUrl = isset($data['CallBackURL']) ? rawurldecode($data['CallBackURL'] ?? '') : (isset($data['CallbackURL']) ? rawurldecode($data['CallbackURL'] ?? '') : null);
            $merchantID = isset($data['MerchantID']) ? $data['MerchantID'] : (isset($data['MerchantAccountID']) ? $data['MerchantAccountID'] : null);
            $pictureUrl = $response['pictureURL'];
            $returnText = $response['returnText'];

            $showQR = $pictureUrl ? 'displayBlock' : '';
            $main_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
            return $this->render('iframe/pay-qr.html.twig', [
                'pictureURL' => $pictureUrl,
                'order_id' => $TranID,
                'ReturnText' => $returnText,
                'displayBlock' => $showQR,
                'merchantId' => $merchantID,
                'Amount' => $data['Amount'],
                'Currency' => $data['Currency'],
                'CallBackURL' => $callbackUrl,
                'env' => $env,
                'main_url' => $main_url,

            ]);
        } else {
            return new Response('No Data received');
        }
    }

    public function windowProcess($data, $env)
    {
        $TranID = $data['TranID'] ?? '';
        $amount = $data['Amount'] ?? '';
        $currency = $data['Currency'] ?? '';
        $CallBackURL = isset($data['CallBackURL']) ? rawurldecode($data['CallBackURL'] ?? '') : (isset($data['CallbackURL']) ? rawurldecode($data['CallbackURL'] ?? '') : null);

        $secureHash = rawurldecode($data['SecureHash'] ?? '');
        $TS = $data['TS'] ?? '';
        $TranTS = $data['TranTS'] ?? '';
        $merchantId = isset($data['MerchantID']) ? $data['MerchantID'] : (isset($data['MerchantAccountID']) ? $data['MerchantAccountID'] : null);
        $additionalInfo = isset($data['AdditionalInfo']) ? $data['AdditionalInfo'] : (isset($data['description']) ? $data['description'] : '');

        $merchant = $this->mr->getRepository(merchants::class)->findOneBy(['merchantMid' => $merchantId]);

        //For G gateway only(ex: Ihjoz)
        $existingInvoice = $this->mr->getRepository(invoices::class)->findOneBy([
            'merchants' => $merchant,
            'merchantOrderId' => $TranID
        ]);
        if ($existingInvoice) {
            $existingInvoice->setPaymentMethod('QRPaymentGateway');
            // Update other fields as needed
            $this->mr->persist($existingInvoice);
            $this->mr->flush();
        }

        if ($TranID !== '' && $amount !== '' && $currency !== '' && $secureHash !== '' && $TS !== '' && $merchantId !== '') {
            $transactionData = [
                'TransactionID' => $TranID,
                'Amount' => $amount,
                'Currency' => $currency,
                'SecureHash' => $secureHash,
                'TS' => "$TS",
                'TranTS' => "$TranTS",
                'MerchantAccountID' => $merchantId,
                'AdditionalInfo' => $additionalInfo,
                'CallBackUrl' => $CallBackURL
            ];
            $url = "/PayQR";

            $params = [
                'data' => json_encode($transactionData),
                'url' => $url,
            ];

            return $this->getQr($params);
        }
    }

    public function getQr($data)
    {
        $response = $this->client->request('POST', $this->SUYOOL_API_HOST . $data['url'], [
            'body' => $data['data'],
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);

        $content = $response->getContent();
        $response = json_decode($content, true);

        $logs = new IframeLog();
        $logs->setidentifier("QR Payment");
        $logs->seturl($this->SUYOOL_API_HOST . $data['url']);
        $logs->setrequest($data['data']);
        $logs->setresponse($content);
        $logs->seterror(json_encode($response['returnText']));

        $this->defaultDB->persist($logs);
        $this->defaultDB->flush();



        $this->session->remove('payment_data');

        return $response;
    }

    /**
     * @Route("/paysuyoolmobile/", name="app_pay_suyool_mobile")
     */
    public function paySuyoolMobile(Request $request): Response
    {
        if (!empty($this->session->get('payment_data'))) {
            $data = $this->session->get('payment_data');
        } else {
            $data = $request->query->all();
        }
        $data['SecureHash'] = str_replace(' ', '+', $data['SecureHash']);
        $env = "live";
        $TranID = isset($data['TranID']) ? $data['TranID'] : "";
        $amount = isset($data['Amount']) ? $data['Amount'] : "";
        $currency = isset($data['Currency']) ? $data['Currency'] : "";
        $CallBackURL = isset($data['CallBackURL']) ? rawurldecode($data['CallBackURL'] ?? '') : (isset($data['CallbackURL']) ? rawurldecode($data['CallbackURL'] ?? '') : null);
        $secureHash = isset($data['SecureHash']) ? rawurldecode($data['SecureHash']) : "";
        $TS = isset($data['TS']) ? $data['TS'] : "";
        $TranTS = isset($data['TranTS']) ? $data['TranTS'] : "";
        $merchantId = isset($data['MerchantID']) ? $data['MerchantID'] : (isset($data['MerchantAccountID']) ? $data['MerchantAccountID'] : null);
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
        }

        if ($TranID !== '' && $amount !== '' && $currency !== '' && $secureHash !== '' && $TS !== '' && $merchantId !== '') {
            $transactionData = [
                'strTranID' => "$TranID",
                'MerchantID' => $merchantId,
                'Amount' => $amount,
                'Currency' => $currency,
                "CallBackURL" => $CallBackURL,
                'TS' => "$TS",
                'SecureHash' => $secureHash,
                'currentUrl' => "$CurrentUrlClient",
                'browsertype' => $Browsertype,
                'AdditionalInfo' => $additionalInfo,
            ];

            $jsonEncoded = json_encode($transactionData);
            $appUrl = "suyoolpay://suyool.com/suyool=?" . urlencode($jsonEncoded);

            $logs = new IframeLog();
            $logs->setidentifier("Mobile Payment");
            $logs->seturl("suyoolpay://suyool.com/suyool=?");
            $logs->setrequest($jsonEncoded);
            $logs->setresponse($appUrl);
            $logs->seterror('');

            $this->defaultDB->persist($logs);
            $this->defaultDB->flush();

            return $this->render('iframe/pay-mobile.html.twig', [
                'deepLink' => $appUrl,
                'order_id' => $TranID,
                'env' => $env,
                'main_url' => $main_url,
                'merchantId' => $merchantId,
                'CallBackURL' => $CallBackURL,

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

        if ($transactionId != '' && $merchantId != '') {
            $timestamp = date("ymdHis"); //Format: 180907071749 = 07/09/2018 7:17:49am - UTC time
            $certificate = "6eEimt2ffGTy2Jps3T7XS9aKzl1Rjwut0vk8q3byk1ERUAosAppdzaLorUVEfmMP0ip33aoiWpwKX9iSsFTfX19FqT9WiYPou1tX4KkaZYIJBzdaIPhD49NRsm1JXW8ZJMmTYKsqw7zeYeUjgA9JDc";

            $secure = $timestamp . $transactionId . $certificate;
            $secureHash = base64_encode(hash('sha512', $secure, true));
            $json = [
                "transactionID" => $transactionId,
                "ts" => $timestamp,
                "merchantAccountID" => $merchantId,
                "secureHash" => $secureHash
            ];
            $params['data'] = json_encode($json);
            $params['url'] = '/CheckQRPaymentStatus';


            $response = $this->client->request('POST', $this->SUYOOL_API_HOST . $params['url'], [
                'body' => $params['data'],
                'headers' => [
                    'Content-Type' => 'application/json'
                ]
            ]);



            $result = json_decode($response->getContent(), true);

            $logs = new IframeLog();
            $logs->setidentifier("Check Payment STATUS");
            $logs->seturl($this->SUYOOL_API_HOST . $params['url']);
            $logs->setrequest($params['data']);
            $logs->setresponse($response->getContent());
            $logs->seterror(json_encode($result['returnText']));

            $this->defaultDB->persist($logs);
            $this->defaultDB->flush();

            $flag = isset($result['Flag']) ? $result['Flag'] : (isset($result['flag']) ? $result['flag'] : null);
            $referenceNo = isset($result['ReferenceNo']) ? $result['ReferenceNo'] : (isset($result['referenceNo']) ? $result['referenceNo'] : null);
            $tranID = isset($result['TranID']) ? $result['TranID'] : (isset($result['tranid']) ? $result['tranid'] : null);
            $returnText = isset($result['ReturnText']) ? $result['ReturnText'] : (isset($result['returnText']) ? $result['returnText'] : null);
            $SecureHash = isset($result['SecureHash']) ? $result['SecureHash'] : (isset($result['secureHash']) ? $result['secureHash'] : null);
            $additionalInfo = isset($result['AdditionalInfo']) ? $result['AdditionalInfo'] : (isset($result['additionalinfo']) ? $result['additionalinfo'] : null);

            $merchant = $this->mr->getRepository(merchants::class)->findOneBy(['merchantMid' => $merchantId]);

            $webhook = $merchant->getWebhook() ?? '';
            if(isset($webhook) && $webhook == 0) {
                $callBackURL = $callBackURL ."?Flag=".$flag . "&ReturnText=".$returnText . "&ReferenceNo=".$referenceNo . "&TranID=". $transactionId . "&SecureHash=" . rawurlencode($SecureHash);
                $callBackURL = str_replace("&amp;","&",$callBackURL);
            }

            $responseContent = json_encode([
                'Flag' => $flag,
                'ReferenceNo' => $referenceNo,
                'TranID' => $tranID,
                'ReturnText' => $returnText,
                'AdditionalInfo' => $additionalInfo,
                'CallBackURL' => $callBackURL
            ]);

            $invoice = $this->mr->getRepository(invoices::class)->findOneBy(['merchants' => $merchant->getId(),'merchantOrderId'=> $transactionId]);

            if($invoice) {
                if ($result['flag'] == 1){
                    $invoice->setStatus('completed');
                }elseif ($result['flag'] == 3){
                    $invoice->setStatus('TimedOut');
                }elseif ($result['flag'] == 7){
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
