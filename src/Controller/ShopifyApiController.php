<?php

namespace App\Controller;

use App\Repository\OrdersRepository;
use App\Utils\Helper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
const CERTIFICATE_PAYMENT_SUYOOL = "FuawNgIwDKYkPZhuIScrcwMXlmnAlS95bjITnyJufWSyKLL3EokqlBqGaBsMqRBoH8vVEbeNmRe0mpoSpRedbEDE8wMIsQgFxcLq";


class ShopifyApiController extends AbstractController
{
    /**
     * @Route("/payQR/", name="app_pay_qr")
     */
    public function paySkashQR(Request $request, OrdersRepository $ordersRepository): Response
    {
        $order_id = $request->request->get('order_id');
        $res = $ordersRepository->findBy(['orderId' => $order_id]);

        $created_at =  $res[0]->getCreateDate()->getTimestamp();

        $metadata = json_decode($request->request->get('metadata'), true);

        $amount = $metadata['total_price']/100;
        $currency = $metadata['currency'];
        $timestamp =  $created_at*1000;
        $TranTS =  $created_at*1000;
        $merchant_id = $metadata['merchant_id'];
        $AdditionalInfo = "";

        $mobile_secure = $order_id . $merchant_id . $amount . $currency . $timestamp . CERTIFICATE_PAYMENT_SUYOOL;
        $SecureHash = base64_encode(hash('sha512', $mobile_secure, true));

        if ($order_id !== '' && $amount !== '' && $currency !== ''  && $SecureHash !== '' && $timestamp !== '' && $TranTS !== '' && $merchant_id !== '') {
            $amount = number_format((float)$amount, 2, '.', '');

            $json = [
                "TransactionID" => 144,
                "Amount" => "1.00",
                "Currency" => "USD",
                "SecureHash" => "gfq4Tvig01ol4vpl1AV9yanD9inWGgMCLjEXjMu8N1YPMeqEFckgljSjIiNdiNkqBKeKnsspfzNv5QNU9\/KKxg==",
                "TS" => 1686741548000,
                "TranTS" => 1686741548000,
                "MerchantAccountID" => 51,
                "AdditionalInfo" => ""
            ];
//            print_r(json_encode($json));
            $params['data'] = json_encode($json);
            $params['url'] = 'SuyoolOnlinePayment/PayQR';

//            $result = Helper::send_curl($params);
//            $response = json_decode($result, true);
//            print_r($response);die;
//            if ($response['Flag'] == 2) {
//                return $this->json(['pictureURL' => $response['pictureURL']]);
//
//                $displayQRCont = '';
//            } else {
//                $displayQRCont = 'displayNone';
//            }

            return $this->render('shopify/pay-qr.html.twig', [
                'pictureURL' => 'https://suyool.net/SuyoolOnlinePayment/Pictures/1686741548000.png',
            ]);

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

            $json = [
                "TransactionID" => 144,
                "Amount" => "1.00",
                "Currency" => "USD",
                "SecureHash" => "gfq4Tvig01ol4vpl1AV9yanD9inWGgMCLjEXjMu8N1YPMeqEFckgljSjIiNdiNkqBKeKnsspfzNv5QNU9\/KKxg==",
                "TS" => 1686741548000,
                "TranTS" => 1686741548000,
                "MerchantAccountID" => 51,
                "CallBackURL" => "",
                "currentUrl" =>"asdads",
                "browsertype" => $this->spfw_get_browser_type(),
                "AdditionalInfo" => $AdditionalInfo
            ];
            $json_encoded = json_encode($json);
            $APP_URL = "skashpay://skash.com/skash=?";
            $appUrl = $APP_URL.$json_encoded;

            return $this->json(['url' => $appUrl]);


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
