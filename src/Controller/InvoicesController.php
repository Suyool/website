<?php


namespace App\Controller;


use App\Entity\Invoices\invoices;
use App\Entity\Invoices\MerchantKey;
use App\Entity\Invoices\merchants;
use App\Entity\Invoices\test_invoices;
use App\Service\InvoiceServices;
use App\Service\RateLimiter;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class InvoicesController extends AbstractController
{

    private $rateLimiter;
    private $certificate;

    public function __construct(ManagerRegistry $mr, RateLimiter $rateLimiter, RequestStack $requestStack)
    {
        $this->mr = $mr->getManager('invoices');
        $this->rateLimiter = $rateLimiter;
        $this->certificate = $_ENV['CERTIFICATE'];
        $this->requestStack = $requestStack;
    }


    /**
     * @Route("/merchant/v1/invoices/{test?}", name="payment_api_invoice_test", requirements={"test"="test"},methods="POST")
     * @Route("/merchant/v1/invoices/", name="payment_api_invoice",methods="POST")
     */
    public function api(Request $request,SessionInterface $session,InvoiceServices $invoicesServices, $test=null)
    {
        if ($test === 'test') {
            $_ENV['APP_ENV'] = 'preProd';
            $session->set('APP_ENV_test','preProd');
        }
        // Get IP address of the requester
        $ipAddress = $request->getClientIp();

        $data = json_decode($request->getContent(),true);
        $merchantId = $data['MerchantAccountID'];

        if (!$merchantId) {
            return new Response('Merchant ID not found in request data', 400); // Bad Request
        }

        // Use the rate limiter service
        if (!$this->rateLimiter->limitRequests($ipAddress, $merchantId)) {
            return new Response('Rate limit exceeded', 429); // Too Many Requests
        }


        $apiKey = $request->headers->get('X-API-Key');

        $order_id = $data['TransactionID'];
        $amount = $data['Amount'];
        $currency = $data['Currency'];
        $order_desc = $data['AdditionalInfo'];
        $callBackUrl = $data['callBackUrl'];

        $merchant = $this->mr->getRepository(merchants::class)->findOneBy(['merchantMid' => $merchantId]);
        $merchantApiKey = $this->mr->getRepository(MerchantKey::class)->findOneBy(['merchant' => $merchant->getId()]);
        $apiKeydata = $merchantApiKey->getApiKey();
        $referenceNumber = $this->generateRandomString(6);

        $request = $this->requestStack->getCurrentRequest();

        // Get the base URL
        $baseUrl = $request->getSchemeAndHttpHost();
        if($apiKeydata == $apiKey){
            $invoicesServices->PostInvoices($merchant,$order_id,$amount,$currency,$order_desc,null,'',$referenceNumber,$callBackUrl);
            if ($test === 'test') {
                $url = $baseUrl."/test/G".$referenceNumber;
            }else {
                $url = $baseUrl."/G".$referenceNumber;
            }

            $array = [
                "url" => $url,
                "success" => true
            ];

            return new JsonResponse($array);

        }else {
            return new Response("your are not eligible to continue");

        }
    }
    private function generateRandomString($length = 6)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charLength - 1)];
        }

        return $randomString;
    }

    /**
     * @Route("/test/{refnumber}", name="payment_gateway_test", requirements={"refnumber"="G[a-zA-Z0-9]+"})
     * @Route("/{refnumber}", name="payment_gateway_main", requirements={"refnumber"="G[a-zA-Z0-9]+"})
     */
    public function paymentGateway(Request $request,SessionInterface $session,$refnumber)
    {

        $session->remove('payment_data');
        $firstFPosition = strpos($refnumber, 'G');

        $refnumber = substr($refnumber, $firstFPosition + 1);

        $isTestRequest = strpos($request->getPathInfo(), '/test/') === 0;

        $invoiceClass = invoices::class;
        if ($isTestRequest){
            $invoiceClass = test_invoices::class;
            $session->set('APP_ENV_test','preProd');
        }

        $invoice = $this->mr->getRepository($invoiceClass)->createQueryBuilder('i')->select('i', 'm')->leftJoin('i.merchants', 'm')
            ->where('i.reference = :refnumber')->setParameter('refnumber', $refnumber)->getQuery()->getOneOrNullResult();

        $merchant = $invoice->getMerchantsId();
        $merchantId = $merchant->getMerchantMid();
        $certificate = $merchant->getCertificate();
        $orderId = $invoice->getMerchantOrderId();
        $timestamp = $invoice->getCreated()->getTimestamp();
        $amount = $invoice->getAmount();
        $currency = $invoice->getCurrency();
        $callbackURL = $invoice->getCallBackURL();
        $userAgent = $request->headers->get('User-Agent');
        $isMobile = false;
        // Perform some basic checks to determine if it's a mobile user agent
        if (strpos($userAgent, 'Mobile') !== false || strpos($userAgent, 'Android') !== false) {
            $secure = $orderId . $merchantId . $amount . $currency . $callbackURL . $timestamp . $certificate;
            $isMobile = true;
        }else {
            $secure = $orderId . $timestamp . $amount . $currency . $callbackURL . $timestamp . $certificate;
        }

        $secureHash = base64_encode(hash('sha512', $secure, true));

        $merchant = $this->mr->getRepository(merchants::class)->findOneBy(['merchantMid' => $merchantId]);
        $merchantSettings = $merchant->getSettings();
        $amount = number_format($amount, 3, '.', '');
        // Store data in the session
        $paymentData = [
            'MerchantID' => $merchantId,
            'Amount' => $amount,
            'Currency' => $currency,
            'SecureHash' => $secureHash,
            'TranID' => $orderId,
            'AdditionalInfo' => $invoice->getMerchantOrderDesc(),
            'CallBackURL' => $callbackURL,
            'TS' => $timestamp,
            'TranTS' => $timestamp,
            'refNumber' => $refnumber
        ];

        // Store data in the session
        $session->set('payment_data', $paymentData);
        if(isset($merchantSettings) && $merchantSettings == 1 ) {
            if($isMobile) {
                return $this->redirectToRoute('app_pay_suyool_mobile');
            }
            else{
                return $this->redirectToRoute('app_pay_suyool_qr');
            }
        }else {
            return $this->render('Invoices/index.html.twig',[
                'is_mobile' => $isMobile,
            ]);
        }
        return $this->render('Invoices/index.html.twig',[
            'is_mobile' => $isMobile,
        ]);
    }
}