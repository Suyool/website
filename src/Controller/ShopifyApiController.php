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
