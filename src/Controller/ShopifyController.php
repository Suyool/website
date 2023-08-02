<?php

namespace App\Controller;

use App\Entity\Shopify\Orders;
use App\Entity\Shopify\OrdersTest;
use App\Entity\Shopify\MerchantCredentials;
use App\Utils\Helper;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    /**
     * @Route("/shopify/", name="app_shopify_handle_request")
     */
    public function handleRequest(Request $request): Response
    {
        $orderID = $request->query->get('order_id');
        $totalPrice = $request->query->get('Merc_id');
        $totalPrice = base64_decode($totalPrice)/ 100;
        $url = $request->query->get('url');
        $domain = $request->query->get('domain');
        $errorUrl = $request->query->get('error_url');
        $currency = $request->query->get('currency');
        $env = $request->query->get('env');
        if (!isset($url) || $url == '' || !isset($errorUrl) || $errorUrl == '') {
            //insert transaction log error of missing url: to be done later
            return new Response("Your order cannot be proccessed. Either you have not set error url or success url in your request. Please contact support.You will be redirected back to store in few seconds.");
        }

        $hostname = Helper::getHost($domain);
        $credentialsRepository = $this->mr->getRepository(MerchantCredentials::class);
        $credentials = $credentialsRepository->findAll();

        foreach($credentials as $credential){
            if($credential->getShop() == $hostname){
                if ($credential->getLiveChecked())
                    $merchantId = $credential->getLiveMerchantId();
                else
                    $merchantId = $credential->getTestMerchantId();

                $metadata = json_encode(array('url' => $url, 'domain' => $domain, 'error_url' => $errorUrl, 'currency' => $currency, 'total_price' => $totalPrice, 'env' => $env, 'merchant_id' => $merchantId));
                $orderClass = ($env == "test") ? OrdersTest::class : Orders::class;
                $order = new $orderClass();

                $order->setOrderId($orderID);
                $order->setShopName($domain);
                $order->setAmount($totalPrice);
                $order->setCurrency($currency);
                $order->setCallbackUrl($url);
                $order->setErrorUrl($errorUrl);
                $order->setEnv($env);
                $order->setMerchantId($merchantId);
                $order->setStatus(0);

                // Check if the order already exists in the database
                $existingOrder = $this->mr->getRepository($orderClass)->findOneBy(['orderId' => $orderID]);
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
