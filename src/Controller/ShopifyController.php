<?php

namespace App\Controller;

use App\Entity\Invoices\merchants;
use App\Entity\Shopify\ShopifyInstallation;
use App\Entity\Shopify\ShopifyOrders;
use App\Entity\Shopify\Orders;
use App\Entity\Shopify\OrdersTest;
use App\Service\ShopifyServices;
use App\Utils\Helper;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShopifyController extends AbstractController
{
    private $mr;
    public function __construct(ManagerRegistry $mr)
    {
        $this->mr=$mr->getManager('Shopify');
    }

    #[Route('/shopify', name: 'app_shopify_handle_request')]
    #[Route('/shopify/{cardpayment}', name: 'app_shopify_card_handle_request', requirements: ['cardpayment' => 'cardpayment'])]
    public function handleRequest(Request $request, ShopifyServices $shopifyServices, $cardpayment = null): Response
    {

        $orderID = $request->query->get('order_id');
        $domain = $request->query->get('domain');

        $hostname = Helper::getHost($domain);
        $merchantCredentials = $shopifyServices->getCredentials(Helper::getHost($domain));
        $appKey = $merchantCredentials['appKey'];
        $appPass = $merchantCredentials['appPass'];
        $checkShopifyOrder = $shopifyServices->getShopifyOrder($orderID, $appKey, $appPass, $hostname);
        $totalPrice = $checkShopifyOrder['transactions']['0']['amount'];
        if($cardpayment) {
            $trandID = $request->query->get('TranID');
            $currency = $request->query->get('Currency');
            $merchantID = $request->query->get('MerchantID');
            $callBackURL = $request->query->get('CallBackURL');
            $additionalInfo = $request->query->get('additionalInfo');

            $secureHash = $request->query->get('SecureHash');
            $currentHost = $request->getHost();
            $formattedPrice = number_format($totalPrice, 3);
            $merchant = $this->mr->getRepository(merchants::class)->findOneBy(['merchantMId' => $merchantID]);
            $certificate = $merchant->getCertificate();

            $secure = $trandID . $merchantID . $additionalInfo . $certificate;
            $suyoolSecureHash = base64_encode(hash('sha512', $secure, true));
            if($suyoolSecureHash == $secureHash){
                $secure = $trandID . $merchantID . $formattedPrice .$additionalInfo . $certificate;
                $APISecureHash = base64_encode(hash('sha512', $secure, true));
                $url = 'http://'.$currentHost.'/cardpayment/?Amount='.$formattedPrice.'&TranID='.$trandID.'&Currency='.$currency.'&MerchantID='.$merchantID.'&CallBackURL='.$callBackURL .'&SecureHash=' .$APISecureHash;
                return new RedirectResponse($url);

            }

        }
        $url = $request->query->get('url');
        $errorUrl = $request->query->get('error_url');
        $currency = $request->query->get('currency');
        $env = $request->query->get('env');
        if (!isset($url) || $url == '' || !isset($errorUrl) || $errorUrl == '') {
            //insert transaction log error of missing url: to be done later
            return new Response("Your order cannot be proccessed. Either you have not set error url or success url in your request. Please contact support.You will be redirected back to store in few seconds.");
        }

        $hostname = $domain;
        $credentialsRepository = $this->mr->getRepository(ShopifyInstallation::class);
        $credentials = $credentialsRepository->findAll();
        foreach($credentials as $credential){
            if($credential->getDomain() == $hostname){
                $merchantId = $credential->getMerchantId();
                $metadata = json_encode(array('url' => $url, 'path' => $domain, 'error_url' => $errorUrl, 'currency' => $currency, 'total_price' => $totalPrice, 'env' => $env, 'merchant_id' => $merchantId));

                $order = new Orders();
                $order->setOrderId($orderID);
                $order->setShopName($domain);
                $order->setAmount($totalPrice);
                $order->setCurrency($currency);
                $order->setCallbackUrl($url);
                $order->setErrorUrl($errorUrl);
                $order->setEnv($env);
                $order->setMerchantId($merchantId);
                $order->setStatus(0);
                $order->setFlag(0);

                $existingOrder = $this->mr->getRepository(Orders::class)->findOneBy(['orderId' => $orderID]);

                if ($existingOrder) {
                    // Update the existing order
                    $existingOrder->setAmount($totalPrice);
                    $existingOrder->setCurrency($currency);
                    $existingOrder->setCallbackUrl($url);
                    $existingOrder->setErrorUrl($errorUrl);
                    $existingOrder->setEnv($env);
                    $existingOrder->setStatus(0);
                } else {
                    // Create a new order
                    $this->mr->persist($order);
                }

                $this->mr->flush();
                return $this->render('shopify/index.html.twig', [
                    'order_id' => $orderID,
                    'meta_data' => $metadata,
                ]);
            }
        }

        return new Response("false");
    }
}
