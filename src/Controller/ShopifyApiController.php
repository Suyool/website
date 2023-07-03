<?php

namespace App\Controller;

use App\Entity\Shopify\ShopifyOrders;
use App\Entity\Shopify\MerchantCredentials;
use App\Utils\Helper;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

//const CERTIFICATE_PAYMENT_SUYOOL = "GVkCbD9ghQIPzfNrI5HX3GpkAI60HjUaqV2FIPpXN6IB6ZioUbcAeKJVATY6X74s2DNAE5N3T70nCPszxF8gpfUGSU2ity69c2fA";


class ShopifyApiController extends AbstractController
{

    private $mr;
    public function __construct(ManagerRegistry $mr)
    {
        $this->mr=$mr->getManager('Shopify');
    }

    /**
     * @Route("/payQR/", name="app_pay_qr")
     */
    public function paySkashQR(Request $request): Response
    {
        $ordersRepository = $this->mr->getRepository(ShopifyOrders::class);

        $orderId = $request->request->get('order_id');
        $order = $ordersRepository->findOneBy(['orderId' => $orderId]);

        if (!$order) {
            throw $this->createNotFoundException('Order not found');
        }

        $createdAt = $order->getCreateDate()->getTimestamp();

        $metadata = json_decode($request->request->get('metadata'), true);
        $totalPrice = trim($metadata['total_price']) / 100;
        $amount = number_format($totalPrice, 2, '.', '');
        $currency = $metadata['currency'];
        $timestamp = $createdAt * 1000;
        $additionalInfo = '';
        $domain = $metadata['domain'];
        $merchantCredentials = $this->getCredentials(Helper::getHost($domain));
        $merchantId = $merchantCredentials['merchantId'];
        $certificate = $merchantCredentials['certificate'];

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

            $result = Helper::send_curl($params);
            $response = json_decode($result, true);
            
            if($response['pictureURL'] != null)
                $showQR = 'displayBlock';
            else
                $showQR = '';

            return $this->render('shopify/pay-qr.html.twig', [
                'pictureURL' => $response['pictureURL'],
                'order_id' => $orderId,
                'ReturnText' => $response['returnText'],
                'displayBlock' =>$showQR,
            ]);
        }
    }

    /**
     * @Route("/payMobile/", name="app_pay_mobile")
     */
    public function payMobile(Request $request): Response
    {
        if(!empty($request->request)) {
            $orderId = $request->request->get('order_id');
            $metadata = json_decode($request->request->get('metadata'), true);
            $totalPrice = trim($metadata['total_price']) / 100;
            $amount = number_format($totalPrice, 2, '.', '');
            $currency = $metadata['currency'];

            $ordersRepository = $this->mr->getRepository(ShopifyOrders::class);
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
            $current_page = (empty($_SERVER['HTTPS']) ? 'http' : 'https') . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
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
        }else {
            return $this->render('shopify/pay-mobile.html.twig');
        }

    }


    /**
     * @Route("/update_status/{order_id}", name="app_update_status")
     */
    public function updateStatus(Request $request, $order_id)
    {
        $data = $request->request->all();
        $flag = isset($data['Flag']) ? $data['Flag'] : null;

        if ($flag !== null) {
            $ordersRepository = $this->mr->getRepository(ShopifyOrders::class);
            $orders = $ordersRepository->findBy(['orderId' => $order_id]);
            $order = $orders[0];

            if ($flag == '1') {
                $metaInfo = json_decode($order->getMetaInfo(), true);
                $currency = $metaInfo['currency'];
                $domain = Helper::getHost($metaInfo['domain']);

                $merchantCredentials = $this->getCredentials($domain);
                $merchantId = $merchantCredentials['merchantId'];
                $certificate = $merchantCredentials['certificate'];

                $totalPrice = $metaInfo['total_price'];
                $url = 'https://' . $domain . '/admin/api/2020-04/orders/' . $order_id . '/transactions.json';

                $matchSecure = $data['Flag'] . $data['ReferenceNo'] . $order_id . $data['ReturnText'] . $certificate;
                $secureHash = urldecode(base64_encode(hash('sha512', $matchSecure, true)));

                $credentialsRepository = $this->mr->getRepository(MerchantCredentials::class);
                $credential = $credentialsRepository->findOneBy(['shop' => $domain]);
                $accessToken = $credential->getAccessToken();

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

                $result = Helper::send_curl($params, $accessToken);
                $response = json_decode($result, true);
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
        $ordersRepository = $this->mr->getRepository(ShopifyOrders::class);
        $order = $ordersRepository->findBy(["orderId"=> $orderId]);
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
}
