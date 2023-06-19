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
        $order_id = $request->request->get('order_id');
        $res = $ordersRepository->findBy(['orderId' => $order_id]);

        $created_at = $res[0]->getCreateDate()->getTimestamp();

        $metadata = json_decode($request->request->get('metadata'), true);

        $amount = $metadata['total_price'] / 100;
        $amount = number_format((float)$amount, 2, '.', '');

        $currency = $metadata['currency'];
        $timestamp = $created_at * 1000;
        $merchant_id = 23;
        $AdditionalInfo = "";
//        $created_at = date('Y-m-d H:i:s');
//        $timestamp = strtotime($created_at) * 1000;
        $domain = $metadata['domain'];

        $secure = $order_id . $timestamp . $amount . $currency . $timestamp . $this->getCertificate(Helper::getHost($domain));

        $SecureHash = base64_encode(hash('sha512', $secure, true));

        if ($order_id !== '' && $amount !== '' && $currency !== '' && $SecureHash !== '' && $timestamp !== '' && $merchant_id !== '') {

            $json = [
                "TransactionID" => $order_id,
                "Amount" => $amount,
                "Currency" => $currency,
                "SecureHash" => $SecureHash,
                "TS" => $timestamp,
                "TranTS" => $timestamp,
                "MerchantAccountID" => $merchant_id,
                "AdditionalInfo" => $AdditionalInfo,
            ];
            $params['data'] = json_encode($json);
            $params['url'] = 'SuyoolOnlinePayment/PayQR';

            $result = Helper::send_curl($params);
            $response = json_decode($result, true);
            return $this->render('shopify/pay-qr.html.twig', [
                'pictureURL' => $response['PictureURL'],
                'message' => $response['ReturnText'],
                'order_id' => $order_id,
            ]);

        }

    }

    /**
     * @Route("/payMobile/", name="app_pay_mobile")
     */
    public function payMobile(Request $request, OrdersRepository $ordersRepository): Response
    {
        $order_id = $request->query->get('TranID', '');

        $metadata = json_decode($request->request->get('metadata'), true);

        $amount = $metadata['total_price'] / 100;
        $amount = number_format((float)$amount, 2, '.', '');

        $currency = $metadata['currency'];
        $res = $ordersRepository->findBy(['orderId' => 1]);

        $created_at = $res[0]->getCreateDate()->getTimestamp();
        $timestamp = $created_at * 1000;

        $merchant_id = $request->query->get('MerchantID', '');
        $AdditionalInfo = "";
        $domain = $metadata['domain'];

        $mobile_secure = $order_id . $merchant_id . $amount . $currency . $timestamp . $this->getCertificate(Helper::getHost($domain));
        $SecureHash = base64_encode(hash('sha512', $mobile_secure, true));

        $json = [
            "TransactionID" => $order_id,
            "Amount" => $amount,
            "Currency" => $currency,
            "SecureHash" => $SecureHash,
            "TS" => $timestamp,
            "TranTS" => $timestamp,
            "MerchantAccountID" => $merchant_id,
            "CallBackURL" => "",
            "currentUrl" => "asdads",
            "browsertype" => Helper::getHost(),
            "AdditionalInfo" => $AdditionalInfo
        ];
        $json_encoded = json_encode($json);
        $APP_URL = "skashpay://skash.com/skash=?";
        $appUrl = $APP_URL . $json_encoded;

        return $this->render('shopify/pay-mobile.html.twig', [
            'deepLink' => $appUrl,
            'order_id' => $order_id,
        ]);

    }

    /**
     * @Route("/update_status/{order_id}", name="app_update_status")
     */
    public function updateStatus(Request $request, $order_id, OrdersRepository $ordersRepository, CredentialsRepository $credentialsRepository)
    {

        $data = $request->request->all();

        $flag = isset($data['Flag']) ? $data['Flag'] : null;

        if (isset($flag)) {
            $entityManager = $this->getDoctrine()->getManager();
            $orders = $ordersRepository->findBy(['orderId' => $order_id]);
            $order = $orders[0];

            if ($flag == '1') {

                $metaInfo = json_decode($order->getMetaInfo(), true);
                $currency = $metaInfo['currency'];
                $domain = Helper::getHost($metaInfo['domain']);
                $totalPrice = $metaInfo['total_price'];
                $url = $domain . '/admin/api/2020-04/orders/' . $data['TranID'] . '/transactions.json';

                $match_secure = $data['Flag'] . $data['ReferenceNo'] . $data['TranID'] . $data['ReturnText'] . $this->getCertificate(Helper::getHost($domain));
                $SecureHash = urldecode(base64_encode(hash('sha512', $match_secure, true)));

                $credential = $credentialsRepository->findBy(['shop' => $domain]);

                $accessToken = $credential[0]->getAccessToken();


                if ($SecureHash == $data['SecureHash']) {
                    if ($order) {
                        $order->setStatus(1);
                    }
                    $json = array(
                        'transaction' => array(
                            'currency' => $currency,
                            'amount' => $totalPrice,
                            "source" => "external",
                            'kind' => 'sale',
                            "status" => "success"
                        )
                    );

                } else {
                    if ($order) {
                        $order->setStatus(2);
                    }
                    $json = array(
                        'transaction' => array(
                            'currency' => $currency,
                            'amount' => $totalPrice,
                            "source" => "external",
                            'kind' => 'sale',
                            "status" => "failed"
                        )
                    );
                }
                $params['data'] = json_encode($json);
                $params['url'] = $url;
                $result = Helper::send_curl($params, $accessToken);
                $response = json_decode($result, true);
                return new JsonResponse($response);

            }

            $entityManager->persist($order);
            $entityManager->flush();
            $response = array(
                'status' => 'failed',
            );
            return new JsonResponse($response);
        }
    }

    /**
     * @Route("/check_status/{orderId}", name="app_check_status")
     */
    public function checkOrderStatus($orderId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $order = $entityManager->getRepository(ShopifyOrders::class)->find($orderId);

        if ($order) {
            $status = $order->getStatus();
            $metaInfo = json_decode($order->getMetaInfo(), true);

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

    private function getCertificate($domain)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $credentials = $entityManager->getRepository(MerchantCredentials::class)->findBy(['shop' => $domain]);
        $credential = $credentials[0];

        if ($credential->getTestChecked()) {
            return $credential->getTestCertificateKey();
        } else {
            return $credential->getLiveCertificateKey();
        }
    }
}
