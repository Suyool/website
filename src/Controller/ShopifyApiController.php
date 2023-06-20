<?php

namespace App\Controller;

use App\Entity\MerchantCredentials;
use App\Entity\ShopifyOrders;
use App\Repository\CredentialsRepository;
use App\Repository\OrdersRepository;
use App\Utils\Helper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

//const CERTIFICATE_PAYMENT_SUYOOL = "GVkCbD9ghQIPzfNrI5HX3GpkAI60HjUaqV2FIPpXN6IB6ZioUbcAeKJVATY6X74s2DNAE5N3T70nCPszxF8gpfUGSU2ity69c2fA";


class ShopifyApiController extends AbstractController
{
    /**
     * @Route("/payQR/", name="app_pay_qr")
     */
    public function paySkashQR(Request $request, OrdersRepository $ordersRepository): Response
    {
        $orderId = $request->request->get('order_id');
        $order = $ordersRepository->findOneBy(['orderId' => $orderId]);

        if (!$order) {
            throw $this->createNotFoundException('Order not found');
        }

        $createdAt = $order->getCreateDate()->getTimestamp();

        $metadata = json_decode($request->request->get('metadata'), true);
        $totalPrice = $metadata['total_price'] / 100;
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
                'TransactionID' => $orderId,
                'Amount' => $amount,
                'Currency' => $currency,
                'SecureHash' => $secureHash,
                'TS' => $timestamp,
                'TranTS' => $timestamp,
                'MerchantAccountID' => $merchantId,
                'AdditionalInfo' => $additionalInfo,
            ];
            $params = [
                'data' => json_encode($transactionData),
                'url' => 'SuyoolOnlinePayment/PayQR',
            ];

            $result = Helper::send_curl($params);
            $response = json_decode($result, true);
            
            if($response['PictureURL'] != null)
                $showQR = 'displayBlock';
            else
                $showQR = '';

            return $this->render('shopify/pay-qr.html.twig', [
                'pictureURL' => $response['PictureURL'],
                'message' => $response['ReturnText'],
                'order_id' => $orderId,
                'ReturnText' => $response['ReturnText'],
                'displayBlock' =>$showQR,
            ]);
        }
    }

    /**
     * @Route("/payMobile/", name="app_pay_mobile")
     */
    public function payMobile(Request $request, OrdersRepository $ordersRepository): Response
    {
        $orderId = $request->request->get('order_id');
        $metadata = json_decode($request->request->get('metadata'), true);
        $totalPrice = $metadata['total_price'] / 100;
        $amount = number_format($totalPrice, 2, '.', '');
        $currency = $metadata['currency'];

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
        $current_page = $_SERVER['REQUEST_URI'];

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
        $appUrl = "skashpay://skash.com/skash=?" . $jsonEncoded;

        return $this->render('shopify/pay-mobile.html.twig', [
            'deepLink' => $appUrl,
            'order_id' => $orderId,
        ]);
    }


    /**
     * @Route("/update_status/{order_id}", name="app_update_status")
     */
    public function updateStatus(Request $request, $order_id, OrdersRepository $ordersRepository, CredentialsRepository $credentialsRepository)
    {
        $data = $request->request->all();
        $flag = isset($data['Flag']) ? $data['Flag'] : null;

        if ($flag !== null) {
            $entityManager = $this->getDoctrine()->getManager();
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
                $url = $domain . '/admin/api/2020-04/orders/' . $data['TranID'] . '/transactions.json';

                $matchSecure = $data['Flag'] . $data['ReferenceNo'] . $order_id . $data['ReturnText'] . $certificate;
                $secureHash = urldecode(base64_encode(hash('sha512', $matchSecure, true)));

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

            $entityManager->persist($order);
            $entityManager->flush();
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
        $entityManager = $this->getDoctrine()->getManager();
        $order = $entityManager->getRepository(ShopifyOrders::class)->findBy(["orderId"=> $orderId]);
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
        $entityManager = $this->getDoctrine()->getManager();
        $credentials = $entityManager->getRepository(MerchantCredentials::class)->findBy(['shop' => $domain]);
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
