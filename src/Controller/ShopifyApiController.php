<?php

namespace App\Controller;

use App\Entity\Shopify\Orders;
use App\Entity\Shopify\Logs;
use App\Entity\Shopify\MerchantCredentials;
use App\Entity\Shopify\OrdersTest;
use App\Entity\Shopify\Session;
use App\Entity\Shopify\ShopifyInstallation;
use App\Utils\Helper;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\ShopifyServices;
use ReCaptcha\ReCaptcha;

//const CERTIFICATE_PAYMENT_SUYOOL = "GVkCbD9ghQIPzfNrI5HX3GpkAI60HjUaqV2FIPpXN6IB6ZioUbcAeKJVATY6X74s2DNAE5N3T70nCPszxF8gpfUGSU2ity69c2fA";

class ShopifyApiController extends AbstractController
{
    private $mr;

    public function __construct(ManagerRegistry $mr)
    {
        $this->mr = $mr->getManager('Shopify');
    }

    /**
     * @Route("/payQR/", name="app_pay_qr")
     */
    public function paySkashQR(Request $request, ShopifyServices $shopifyServices): Response
    {
        $metadata = json_decode($request->request->get('metadata'), true);
        $env = $metadata['env'];
        $orderClass = ($env == "test") ? OrdersTest::class : Orders::class;

        $ordersRepository = $this->mr->getRepository($orderClass);

        $orderId = $request->request->get('order_id');
        $order = $ordersRepository->findOneBy(['orderId' => $orderId]);
        if (!$order) {
            throw $this->createNotFoundException('Order not found');
        }

        $createdAt = $order->getCreated()->getTimestamp();


        $amount = $metadata['total_price'];
        $currency = $metadata['currency'];
        $timestamp = $createdAt * 1000;
        $additionalInfo = '';
        $domain = $metadata['path'];
       // $hostname = Helper::getHost($domain);
        $merchantCredentials = $shopifyServices->getCredentials(Helper::getHost($domain));
        $merchantId = $merchantCredentials['merchantId'];
        $certificate = $merchantCredentials['certificate'];
//        $sessionRepository = $this->mr->getRepository(Session::class);
//        $sessionInfo = $sessionRepository->findOneBy(['shop' => $hostname]);
       // $appKey = $merchantCredentials['appKey'];
        //$appPass = $merchantCredentials['appPass'];
        //$checkAmount = $shopifyServices->getShopifyOrder($orderId, $sessionInfo->getAccessToken(), $hostname);

//        $checkAmount = $shopifyServices->getShopifyOrder($orderId, $appKey, $appPass, $hostname);

//        $shopifyAmount = $checkAmount['transactions']['0']['amount'];
//        $resAmount = bccomp($shopifyAmount, $totalPrice, 2);

        $secure = $orderId . $timestamp . $amount . $currency . $timestamp . $certificate;
        $secureHash = base64_encode(hash('sha512', $secure, true));
        if ($orderId !== '' && $amount !== '' && $currency !== '' && $secureHash !== '' && $timestamp !== '' && $merchantId !== '') {
            $transactionData = [
                'TransactionID' => "$orderId",
                'Amount' => $amount,
                'Currency' => $currency,
                'SecureHash' => $secureHash,
                'TS' => "$timestamp",
                'TranTS' => "$timestamp",
                'MerchantAccountID' => $merchantId,
                'AdditionalInfo' => $additionalInfo,
            ];
            if ($env == 'test') {
                $url = 'SuyoolOnlinePayment/PayQR';
            } else {
                $url = 'api/OnlinePayment/PayQR';

            }
            $params = [
                'data' => json_encode($transactionData),
                'url' => $url,
            ];
            $response = $shopifyServices->getQr($params);

            $logs = array('orderId' => $orderId, 'request' => $params, 'response' => $response, 'env' => $metadata['env']);
            $this->saveLog($logs);

            if ($response['pictureURL'] != null)
                $showQR = 'displayBlock';
            else
                $showQR = '';

            return $this->render('shopify/pay-qr.html.twig', [
                'pictureURL' => $response['pictureURL'],
                'order_id' => $orderId,
                'ReturnText' => $response['returnText'],
                'displayBlock' => $showQR,
            ]);
        }

    }

    /**
     * @Route("/payMobile/", name="app_pay_mobile")
     */
    public function payMobile(Request $request,ShopifyServices $shopifyServices): Response
    {
        if ($request->request->get('order_id') != null) {

            $orderId = $request->request->get('order_id');
            $metadata = json_decode($request->request->get('metadata'), true);
            $totalPrice = trim($metadata['total_price']) / 100;
            $amount = number_format($totalPrice, 2, '.', '');
            $currency = $metadata['currency'];
            $ordersRepository = $this->mr->getRepository(Orders::class);
            $order = $ordersRepository->findOneBy(['orderId' => $orderId]);

            if (!$order) {
                throw $this->createNotFoundException('Order not found');
            }

            $createdAt = $order->getCreated()->getTimestamp();
            $timestamp = $createdAt * 1000;
            $domain = $metadata['path'];
            $merchantCredentials = $shopifyServices->getCredentials(Helper::getHost($domain));
            $merchantId = $merchantCredentials['merchantId'];
            $certificate = $merchantCredentials['certificate'];
            $additionalInfo = '';
            $mobileSecure = $orderId . $merchantId . $amount . $currency . $timestamp . $certificate;
            $secureHash = base64_encode(hash('sha512', $mobileSecure, true));
            $current_page = (empty($_SERVER['HTTPS']) ? 'http' : 'https') . "://$_SERVER[HTTP_HOST]" . "/result-page/" . $orderId;

            $json = [
                'TransactionID' => $orderId,
                'Amount' => $amount,
                'Currency' => $currency,
                'SecureHash' => $secureHash,
                'TS' => $timestamp,
                'TranTS' => $timestamp,
                'MerchantAccountID' => $merchantId,
                'CallBackURL' => '',
                'currentUrl' => $current_page,
                'browsertype' => Helper::getBrowserType(),
                'AdditionalInfo' => $additionalInfo
            ];

            $jsonEncoded = json_encode($json);
            $appUrl = "suyoolpay://suyool.com/suyool=?" . $jsonEncoded;

            return $this->render('shopify/pay-mobile.html.twig', [
                'deepLink' => $appUrl,
                'order_id' => $orderId,
            ]);
        } else {
            return $this->render('shopify/pay-mobile.html.twig');
        }
    }

    /**
     * @Route("/update_status/", name="app_update_status",methods={"POST"})
     */
    public function updateStatus(Request $request, ShopifyServices $shopifyServices)
    {
        $data = json_decode($request->getContent(), true);
        $flag = isset($data['Flag']) ? $data['Flag'] : null;

        if ($flag !== null) {

            $ordersRepository = $this->mr->getRepository(Orders::class);
            $order = $ordersRepository->findOneBy(['orderId' => $data['TransactionID'], 'merchantId' => $data['MerchantAccountID']]);

            if ($flag == '1') {
                $currency = $order->getCurrency();
                $domain = Helper::getHost($order->getShopName());
                $merchantCredentials = $shopifyServices->getCredentials($domain);
                $certificate = $merchantCredentials['certificate'];
                $totalPrice = $order->getAmount();
                $appKey = $merchantCredentials['appKey'];
                $appPass = $merchantCredentials['appPass'];
                $env = $merchantCredentials['integrationType'];
                $url = 'https://' . $appKey . ':' . $appPass . '@' . $domain . '/admin/api/2020-04/orders/' . $data['TransactionID'] . '/transactions.json';

                $matchSecure = $data['Flag'] . $data['ReferenceNo'] . $data['TransactionID'] . $data['ReturnText'] . $certificate;
                $secureHash = urldecode(base64_encode(hash('sha512', $matchSecure, true)));
                $secureHash = str_replace(' ', '+', $secureHash);

                if ($secureHash == $data['SecureHash']) {
                    if ($order) {
                        $order->setStatus(1);
                    }
                    $json = [
                        'transaction' => [
                            'currency' => $currency,
                            'amount' => $totalPrice,
                            'source' => 'external',
                            'kind' => 'sale',
                            'status' => 'success'
                        ]
                    ];
                } else {
                    if ($order) {
                        $order->setStatus(2);
                    }
                    $json = [
                        'transaction' => [
                            'currency' => $currency,
                            'amount' => $totalPrice,
                            'source' => 'external',
                            'kind' => 'sale',
                            'status' => 'failed'
                        ]
                    ];
                }

                $params = [
                    'data' => json_encode($json),
                    'url' => $url
                ];
                $logs = array('orderId' => $data['TransactionID'], 'request' => $data, 'response' => $params, 'env' => $env);

                $this->saveLog($logs);

                $response = $shopifyServices->updateStatusShopify($params);

                $logs = array('orderId' => $data['TransactionID'], 'request' => $params, 'response' => $response, 'env' => $env);

                $this->saveLog($logs);

                return new JsonResponse($response);
            }

            $this->mr->persist($order);
            $this->mr->flush();
            $response = [
                'status' => 'failed'
            ];
            return new JsonResponse($response);
        }
        return new JsonResponse("no data sent");
    }

    /**
     * @Route("/check_status/{orderId}", name="app_check_status")
     */
    public function checkOrderStatus($orderId)
    {
        $ordersRepository = $this->mr->getRepository(Orders::class);
        $order = $ordersRepository->findOneBy(["orderId" => $orderId]);
        if ($order) {
            $status = $order->getStatus();

            if ($status == 1) {
                $response = [
                    'status' => 1,
                    'url' => $order->getCallbackUrl()
                ];
                return $this->json($response);
            } elseif ($status == 2) {
                $response = [
                    'status' => 2,
                    'url' => $order->getErrorUrl()
                ];
                return $this->json($response);
            }
        }

        $response = [
            'status' => 0,
            'url' => ''
        ];
        return $this->json($response);
    }

//    private function getCredentials($domain)
//    {
//        $credentialsRepository = $this->mr->getRepository(MerchantCredentials::class);
//        $credentials = $credentialsRepository->findBy(['shop' => $domain]);
//        $credential = $credentials[0];
//
//        $response = [];
//        if ($credential->getTestChecked()) {
//            $certificate = $credential->getTestCertificateKey();
//            $merchantId = $credential->getTestMerchantId();
//        } else {
//            $certificate = $credential->getLiveCertificateKey();
//            $merchantId = $credential->getLiveMerchantId();
//        }
//        $response['certificate'] = $certificate;
//        $response['merchantId'] = $merchantId;
//
//        return $response;
//    }


    /**
     * @Route("/result-page/{orderId}", name="app_result_page")
     */
    public function resultPage($orderId): Response
    {
        return $this->render('shopify/result-page.html.twig', [
            'order_id' => $orderId,
        ]);
    }

    private function saveLog(array $data)
    {
        $log = new Logs();
        $log->setOrderId($data['orderId']);
        $log->setRequest(json_encode($data['request']));
        $log->setResponse(json_encode($data['response']));
        $log->setEnv($data['env']);

        $this->mr->persist($log);
        $this->mr->flush();
    }
}
