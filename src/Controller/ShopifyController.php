<?php

namespace App\Controller;

use App\Entity\ShopifyOrders;
use App\Repository\CredentialsRepository;
use App\Repository\OrdersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\MerchantCredentials;

class ShopifyController extends AbstractController
{
    /**
     * @Route("/shopify/", name="app_shopify_handle_request")
     */
    public function handleRequest(Request $request, EntityManagerInterface $entityManager, CredentialsRepository $credentialsRepository): Response
    {
        // Extract the parameters from the request
        $orderID = $request->query->get('order_id');
        $totalPrice = $request->query->get('total_price');
        $url = $request->query->get('url');
        $domain = $request->query->get('domain');
        $errorUrl = $request->query->get('error_url');
        $currency = $request->query->get('currency');
        $env = $request->query->get('env');
        if (!isset($url) || $url == '' || !isset($errorUrl) || $errorUrl == '') {
            //insert transaction log error of missing url: to be done later
            return new Response("Your order cannot be proccessed. Either you have not set error url or success url in your request. Please contact support.You will be redirected back to store in few seconds.");
        }

        $parsedUrl = parse_url($domain);

        // Get the hostname from the parsed URL
        $hostname = $parsedUrl['host'];

        // Remove the "www" and any subdomains
        $parts = explode('.', $hostname);
        $partsCount = count($parts);
        if ($partsCount >= 3 && $parts[0] === 'www') {
            $hostname = implode('.', array_slice($parts, 1));
        }


        $credentials = $credentialsRepository->findAll();
        foreach($credentials as $credential){
            if($credential->getShop() == $hostname){
                $metadata = json_encode(array('url' => $url, 'domain' => $domain, 'error_url' => $errorUrl, 'currency' => $currency, 'total_price' => $totalPrice, 'env' => $env));

                $order = new ShopifyOrders();
                $order->setOrderId($orderID);
                $order->setMetaInfo($metadata);

                $entityManager->persist($order);
                $entityManager->flush();

                return $this->render('shopify/index.html.twig', [
                    'order_id' => $orderID,
                    'meta_data' => $metadata,
                ]);
            }
        }



        return new Response("hi");
    }
}
