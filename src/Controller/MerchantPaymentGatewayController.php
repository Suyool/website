<?php


namespace App\Controller;


use App\Entity\topup\invoices;
use App\Entity\topup\MerchantKey;
use App\Service\InvoiceServices;
use App\Service\RateLimiter;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\topup\merchants;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MerchantPaymentGatewayController extends AbstractController
{

    private $rateLimiter;

    public function __construct(ManagerRegistry $mr, RateLimiter $rateLimiter)
    {
        $this->mr = $mr->getManager('topup');
        $this->rateLimiter = $rateLimiter;
    }


    /**
     * @Route("/merchant/v1/invoices", name="payment_api_invoice")
     */
    public function api(Request $request,SessionInterface $session,InvoiceServices $invoicesServices)
    {
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
        $merchantApiKey = $this->mr->getRepository(MerchantKey::class)->findOneBy(['merchant' => $merchantId]);
        $apiKeydata = $merchantApiKey->getApiKey();
        $referenceNumber = $this->generateRandomString(6);

        if($apiKeydata == $apiKey){
            $invoicesServices->PostInvoices($merchant,$order_id,$amount,$currency,$order_desc,null,'',$referenceNumber);

            $url = "http://suyool.ls/F".$referenceNumber;

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
     * @Route("/{refnumber}", name="payment_gateway_main", requirements={"refnumber"="F[a-zA-Z0-9]+"})
     */
    public function paymentGateway(Request $request,SessionInterface $session,$refnumber)
    {
        $firstFPosition = strpos($refnumber, 'F');

        $refnumber = substr($refnumber, $firstFPosition + 1);

        $order = $this->mr->getRepository(invoices::class)->createQueryBuilder('i')->select('i', 'm')->leftJoin('i.merchants', 'm')
            ->where('i.reference = :refnumber')->setParameter('refnumber', $refnumber)->getQuery()->getOneOrNullResult();

        $merchant = $order->getMerchantsId();
        $merchantId = $merchant->getMerchantMid();
        $certificate = $merchant->getCertificate();
        $orderId = $order->getMerchantOrderId();
        $timestamp = $order->getCreated()->getTimestamp();
        $amount = $order->getAmount();
        $currency = $order->getCurrency();

        $userAgent = $request->headers->get('User-Agent');
        $isMobile = false;
        // Perform some basic checks to determine if it's a mobile user agent
        if (strpos($userAgent, 'Mobile') !== false || strpos($userAgent, 'Android') !== false) {
            $secure = $orderId . $merchantId . $amount . $currency . $timestamp . $certificate;
            $isMobile = true;
        }else {
            $secure = $orderId . $timestamp . $amount . $currency . $timestamp . $certificate;
        }

        $secureHash = base64_encode(hash('sha512', $secure, true));

        // Store data in the session
        $paymentData = [
            'MerchantID' => $merchantId,
            'Amount' => $amount,
            'Currency' => $currency,
            'SecureHash' => $secureHash,
            'TranID' => $orderId,
            'AdditionalInfo' => $order->getMerchantOrderDesc(),
            'CallBackURL' => $order->getCallBackURL(),
            'TS' => $timestamp,
            'TranTS' => $timestamp,
            'refNumber' => $refnumber
        ];

        // Store data in the session
        $session->set('payment_data', $paymentData);


        return $this->render('MerchantPaymentGateway/index.html.twig',[
            'is_mobile' => $isMobile,
        ]);
    }
}