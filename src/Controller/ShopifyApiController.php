<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShopifyApiController extends AbstractController
{
    /**
     * @Route("/payQR/", name="app_pay_qr")
     */
    public function paySkashQR(Request $request): Response
    {
        $order_id = $request->request->get('order_id');
        $metadata = json_decode($request->request->get('metadata'), true);

        $amount = $metadata['total_price']/100;
        $currency = $metadata['currency'];
        $timestamp =  strtotime($created_at)*1000;
        $TranTS =  strtotime($created_at)*1000;
        $merchant_id = $metadata['merchant_id'];
        $AdditionalInfo = "";

        $mobile_secure = $order_id . $merchant_id . $amount . $currency . $timestamp . CERTIFICATE_PAYMENT_SUYOOL;
        $SecureHash = base64_encode(hash('sha512', $mobile_secure, true));

        if ($order_id !== '' && $amount !== '' && $currency !== ''  && $SecureHash !== '' && $timestamp !== '' && $TranTS !== '' && $merchant_id !== '') {

            $json = [
                "TransactionID" => $order_id,
                "Amount" => $amount/ 100,
                "Currency" => $currency,
                "SecureHash" => $SecureHash,
                "TS" => (string)$timestamp,
                "TranTS" => (string)$TranTS,
                "MerchantAccountID" => $merchant_id,
                "AdditionalInfo" => $AdditionalInfo
            ];

            $params['data'] = json_encode($json);
            $params['url'] = 'SuyoolOnlinePayment/PayQR';

            $result = $this->send_curl($params);
            $response = json_decode($result, true);

            if ($response['Flag'] == 2) {
                $displayQRCont = '';
            } else {
                $displayQRCont = 'displayNone';
            }

        }
        return $this->json(['flag' => $response['Flag']]);

    }
    /**
     * @Route("/payMobile/", name="app_pay_mobile")
     */
    public function payMobile(Request $request): Response
    {
        $order_id = $request->query->get('TranID', '');
        $amount = $request->query->get('Amount', '');
        $currency = $request->query->get('Currency', '');

        $timestamp = $request->query->get('TS', '');
        $TranTS = $request->query->get('TranTS', '');
        $merchant_id = $request->query->get('MerchantID', '');
        $AdditionalInfo = $request->query->get('AdditionalInfo', '');

        $mobile_secure = $order_id . $merchant_id . $amount . $currency . $timestamp . CERTIFICATE_PAYMENT_SUYOOL;
        $SecureHash = base64_encode(hash('sha512', $mobile_secure, true));

        if ($order_id !== '' && $amount !== '' && $currency !== ''  && $SecureHash !== '' && $timestamp !== '' && $TranTS !== '' && $merchant_id !== '') {
            $json = [
                "strTranID" => $order_id,
                "MerchantID" => $merchant_id,
                "Amount" => $amount/ 100,
                "Currency" => "USD",
                "CallBackURL" => "",
                "TS" => (string)$timestamp,
                "secureHash" => $SecureHash,
                "currentUrl" =>"asdads",
                "browsertype" => $this->spfw_get_browser_type(),
                "AdditionalInfo" => $AdditionalInfo
            ];
        }
        return $this->json(['url' => 'your_mobile_payment_url']);

    }
}
