<?php

namespace App\Controller;

use App\Repository\OrdersRepository;
use App\Utils\Helper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
const CERTIFICATE_PAYMENT_SUYOOL = "GVkCbD9ghQIPzfNrI5HX3GpkAI60HjUaqV2FIPpXN6IB6ZioUbcAeKJVATY6X74s2DNAE5N3T70nCPszxF8gpfUGSU2ity69c2fA";


class ShopifyApiController extends AbstractController
{
    /**
     * @Route("/payQR/", name="app_pay_qr")
     */
    public function paySkashQR(Request $request, OrdersRepository $ordersRepository): Response
    {
        $order_id = $request->request->get('order_id');
        $res = $ordersRepository->findBy(['orderId' => 1]);

        $created_at =  $res[0]->getCreateDate()->getTimestamp();

        $metadata = json_decode($request->request->get('metadata'), true);

        $amount = $metadata['total_price']/100;
        $amount = number_format((float)$amount, 2, '.', '');

        $currency = $metadata['currency'];
//        $timestamp =  $created_at*1000;
        $merchant_id = 23;
        $AdditionalInfo = "";

        $created_at = date('Y-m-d H:i:s');
        $timestamp = strtotime($created_at) * 1000;

        $secure = $order_id . $timestamp . $amount . $currency . $timestamp . CERTIFICATE_PAYMENT_SUYOOL;

        $SecureHash = base64_encode(hash('sha512', $secure, true));

        if ($order_id !== '' && $amount !== '' && $currency !== ''  && $SecureHash !== '' && $timestamp !== '' && $merchant_id !== '') {

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
            print_r($json);
            $params['data'] = json_encode($json);
            $params['url'] = 'SuyoolOnlinePayment/PayQR';

            $result = Helper::send_curl($params);
            $response = json_decode($result, true);
            return $this->render('shopify/pay-qr.html.twig', [
                'pictureURL' => $response['PictureURL'],
                'message' => $response['ReturnText'],
            ]);

        }

    }
    /**
     * @Route("/payMobile/", name="app_pay_mobile")
     */
    public function payMobile(Request $request,OrdersRepository $ordersRepository): Response
    {
        $order_id = $request->query->get('TranID', '');

        $metadata = json_decode($request->request->get('metadata'), true);

        $amount = $metadata['total_price']/100;
        $amount = number_format((float)$amount, 2, '.', '');

        $currency = $metadata['currency'];
        $res = $ordersRepository->findBy(['orderId' => 1]);

        $created_at =  $res[0]->getCreateDate()->getTimestamp();
        $timestamp =  $created_at*1000;

        $merchant_id = $request->query->get('MerchantID', '');
        $AdditionalInfo = "";

        $mobile_secure = $order_id . $merchant_id . $amount . $currency . $timestamp . CERTIFICATE_PAYMENT_SUYOOL;
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
                "currentUrl" =>"asdads",
                "browsertype" => $this->spfw_get_browser_type(),
                "AdditionalInfo" => $AdditionalInfo
            ];
            $json_encoded = json_encode($json);
            $APP_URL = "skashpay://skash.com/skash=?";
            $appUrl = $APP_URL.$json_encoded;

        return $this->render('shopify/pay-mobile.html.twig', [
            'deepLink' => $appUrl,
        ]);

    }

    /**
     * @Route("/update_status/", name="app_update_status")
     */
    public function updateStatus($order_id)
    {
        $data = $this->request->request->all();
        $flag = isset($data['Flag']) ? $data['Flag'] : null;

        if (isset($flag)) {
            if ($flag == '1') {
                $match_secure = $data['Flag'] . $data['ReferenceNo'] . $data['TranID'] . $data['ReturnText'] . CERTIFICATE_PAYMENT_SUYOOL;
                $SecureHash = urldecode(base64_encode(hash('sha512', $match_secure, true)));
                $entityManager = $this->getDoctrine()->getManager();
                $payment = $entityManager->getRepository(Payment::class)->find($order_id);

                if ($SecureHash == $data['SecureHash']) {
                    $url = $domain . 'admin/api/2020-04/orders/' . $data['TranID'] .'transactions.json';
                    $json = array(
                        'transaction' => array(
                            'currency' => $currency,
                            'amount' => $total_price,
                            "source"=> "external",
                            'kind' => 'sale',
                            "status" =>  "success"
                        )
                    );
                    $params['data'] = json_encode($json);
                    $params['url'] = $url;
                    $result = $this->send_curl($params);
                    $response = json_decode($result, true);
                } else {
                    if ($payment) {
                        $payment->setStatus(2);
                        $entityManager->flush();
                    }
                }
            }

            $response = array(
                'success' => true,
            );
            return new JsonResponse($response);
        }
    }

    function spfw_get_browser_type()
    {
        $browser = "";
        if (strrpos(strtolower($_SERVER["HTTP_USER_AGENT"]), strtolower("MSIE"))) {
            $browser = "IE";
        } else if (strrpos(strtolower($_SERVER["HTTP_USER_AGENT"]), strtolower("Presto"))) {
            $browser = "opera";
        } else if (strrpos(strtolower($_SERVER["HTTP_USER_AGENT"]), strtolower("CHROME"))) {
            $browser = "chrome";
        } else if (strrpos(strtolower($_SERVER["HTTP_USER_AGENT"]), strtolower("SAFARI"))) {
            $browser = "safari";
        } else if (strrpos(strtolower($_SERVER["HTTP_USER_AGENT"]), strtolower("FIREFOX"))) {
            $browser = "firefox";
        } else if (strrpos(strtolower($_SERVER["HTTP_USER_AGENT"]), strtolower("Netscape"))) {
            $browser = "netscape";
        }
        return $browser;
    }
}
