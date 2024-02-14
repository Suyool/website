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
     * @Route("/merchant/v1/invoices/", name="payment_api_invoice",methods="POST")
     */
    public function api(Request $request, SessionInterface $session, InvoiceServices $invoicesServices)
    {
        // Get IP address of the requester
        $ipAddress = $request->getClientIp();
        $apiKey = $request->headers->get('X-API-Key');
        $merchant = $this->mr->getRepository(MerchantKey::class)->findOneBy(['apiKey' => $apiKey]);
        if ($merchant) {
            $merchantId = $merchant->getMerchant()->getMerchantMid();

            if (!$merchantId) {
                return new Response('Merchant ID not found in request data', 400); // Bad Request
            }

            // Use the rate limiter service
            if (!$this->rateLimiter->limitRequests($ipAddress, $merchantId)) {
                return new Response('Rate limit exceeded', 429); // Too Many Requests
            }

            $data = json_decode($request->getContent(), true);

            $order_id = $data['invoiceId'];
            $amount = $data['amount'];
            $currency = $data['currency'];
            $order_desc = $data['description'];
            $callBackUrl = $data['redirectURl'];

            // Check if $amount, $currency, or $order_id is null
            if ($amount == null || $currency == null || $order_id == null) {
                $array = [
                    "success" => false,
                    "data" => ['payment_url'=> ''],
                    "message" => 'Amount, currency, or order ID cannot be null'
                ];
                return new JsonResponse($array);
            }

            $merchant = $this->mr->getRepository(merchants::class)->findOneBy(['merchantMid' => $merchantId]);
            $referenceNumber = $this->generateRandomString(6);

            if ($_ENV['APP_ENV'] == "test") {
                $path = 'http://suyool.ls';
            }
            else if ($_ENV['APP_ENV'] == "sandbox" || $_ENV['APP_ENV'] == 'dev'){
                $path = 'https://sandbox.suyool.com';
            }
            else {
                $path = 'https://suyool.com';
            }

            $url = $path. "/G" . $referenceNumber;


            $invoicesServices->PostInvoices($merchant, $order_id, $amount, $currency, $order_desc, null, '', $referenceNumber, $callBackUrl);

            $array = [
                "success" => true,
                "data" => ['payment_url'=> $url],
                "message" => 'success'
            ];

            return new JsonResponse($array);

        } else {
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
     * @Route("/{refnumber}", name="payment_gateway_main", requirements={"refnumber"="G[a-zA-Z0-9]+"})
     */
    public function paymentGateway(Request $request, SessionInterface $session, $refnumber)
    {

        //$session->remove('payment_data');

        $firstFPosition = strpos($refnumber, 'G');

        $refnumber = substr($refnumber, $firstFPosition + 1);

        $invoice = $this->mr->getRepository(invoices::class)->createQueryBuilder('i')->select('i', 'm')->leftJoin('i.merchants', 'm')
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
        } else {
            $secure = $orderId . $timestamp . $amount . $currency . $callbackURL . $timestamp . $invoice->getMerchantOrderDesc() .$certificate;
        }
        $secureHash = base64_encode(hash('sha512', $secure, true));


        $qrPath = 'app_pay_suyool_qr';


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
        if (isset($merchantSettings) && $merchantSettings == 1) {
            if ($isMobile) {
                return $this->redirectToRoute('app_pay_suyool_mobile');
            } else {
                return $this->redirectToRoute($qrPath);
            }
        } else {
            return $this->render('Invoices/index.html.twig', [
                'is_mobile' => $isMobile,
            ]);
        }
        return $this->render('Invoices/index.html.twig', [
            'is_mobile' => $isMobile,
        ]);
    }
}