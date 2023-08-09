<?php

namespace App\Controller;

use App\Entity\Shopify\Orders;
use App\Entity\Shopify\Logs;
use App\Entity\Shopify\MerchantCredentials;
use App\Entity\Shopify\Session;
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
        $ordersRepository = $this->mr->getRepository(Orders::class);

        $orderId = $request->request->get('order_id');
        $order = $ordersRepository->findOneBy(['orderId' => $orderId]);
        if (!$order) {
            throw $this->createNotFoundException('Order not found');
        }

        $createdAt = $order->getCreated()->getTimestamp();

        $metadata = json_decode($request->request->get('metadata'), true);
        $totalPrice = trim($metadata['total_price']) / 100;
        $amount = number_format($totalPrice, 2, '.', '');
        $currency = $metadata['currency'];
        $timestamp = $createdAt * 1000;
        $additionalInfo = '';
        $domain = $metadata['domain'];
        $hostname = Helper::getHost($domain);
        $merchantCredentials = $this->getCredentials(Helper::getHost($domain));
        $merchantId = $merchantCredentials['merchantId'];
        $certificate = $merchantCredentials['certificate'];
        $sessionRepository = $this->mr->getRepository(Session::class);
        $sessionInfo = $sessionRepository->findOneBy(['shop' => $hostname]);
        $checkAmount = $shopifyServices->getShopifyOrder($orderId, $sessionInfo->getAccessToken(), $hostname);

        $shopifyAmount = $checkAmount['transactions']['0']['amount'];
        $resAmount = bccomp($shopifyAmount, $totalPrice, 2);
        if ($resAmount == 0) {
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
                $params = [
                    'data' => json_encode($transactionData),
                    'url' => 'api/OnlinePayment/PayQR',
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
        }else {
            return new Response("404");
        }
    }

    /**
     * @Route("/payMobile/", name="app_pay_mobile")
     */
    public function payMobile(Request $request): Response
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

            $createdAt = $order->getCreateDate()->getTimestamp();
            $timestamp = $createdAt * 1000;
            $domain = $metadata['domain'];
            $merchantCredentials = $this->getCredentials(Helper::getHost($domain));
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
     * @Route("/update_status/{order_id}", name="app_update_status")
     */
    public function updateStatus(Request $request, $order_id, ShopifyServices $shopifyServices)
    {
        $data = $request->request->all();
        $flag = isset($data['Flag']) ? $data['Flag'] : null;

        if ($flag !== null) {
            $ordersRepository = $this->mr->getRepository(Orders::class);
            $orders = $ordersRepository->findBy(['orderId' => $order_id]);
            $order = $orders[0];

            if ($flag == '1') {
                $currency = $order->getCurrency();
                $domain = Helper::getHost($order->getShopName());

                $merchantCredentials = $this->getCredentials($domain);
                $certificate = $merchantCredentials['certificate'];

                $totalPrice = $order->getAmount();
                $url = 'https://' . $domain . '/admin/api/2020-04/orders/' . $order_id . '/transactions.json';

                $matchSecure = $data['Flag'] . $data['ReferenceNo'] . $order_id . $data['ReturnText'] . $certificate;
                $secureHash = urldecode(base64_encode(hash('sha512', $matchSecure, true)));

                $sessionRepository = $this->mr->getRepository(Session::class);
                $sessionInfo = $sessionRepository->findOneBy(['shop' => $domain]);
                $accessToken = $sessionInfo->getAccessToken();

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
                $response = $shopifyServices->updateStatusShopify($params, $accessToken);

                $logs = array('orderId' => $order_id, 'request' => $params, 'response' => $response, 'env' => "");

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
    }


    /**
     * @Route("/check_status/{orderId}", name="app_check_status")
     */
    public function checkOrderStatus($orderId)
    {
        $ordersRepository = $this->mr->getRepository(Orders::class);
        $order = $ordersRepository->findBy(["orderId" => $orderId]);
//        dd($order);
        if ($order) {
            $status = $order[0]->getStatus();
            $metaInfo = json_decode($order[0]->getMetaInfo(), true);

            if ($status == 1) {
                // The order status is 1
                // Return the status and URL as JSON response
                $response = [
                    'status' => 1,
                    'url' => $metaInfo['url']
                ];
                return $this->json($response);
            } elseif ($status == 2) {
                // The order status is 2
                // Return the status and error URL as JSON response
                $response = [
                    'status' => 2,
                    'url' => $metaInfo['error_url']
                ];
                return $this->json($response);
            }
        }

        // Return a default response if the order is not found or the status is not handled
        $response = [
            'status' => 0,
            'url' => ''
        ];
        return $this->json($response);
    }

    private function getCredentials($domain)
    {
        $credentialsRepository = $this->mr->getRepository(MerchantCredentials::class);
        $credentials = $credentialsRepository->findBy(['shop' => $domain]);
        $credential = $credentials[0];

        $response = [];
        if ($credential->getTestChecked()) {
            $certificate = $credential->getTestCertificateKey();
            $merchantId = $credential->getTestMerchantId();
        } else {
            $certificate = $credential->getLiveCertificateKey();
            $merchantId = $credential->getLiveMerchantId();
        }
        $response['certificate'] = $certificate;
        $response['merchantId'] = $merchantId;

        return $response;
    }

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
