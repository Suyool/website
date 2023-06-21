<?php

namespace App\Controller;

use App\Entity\Shopify\ShopifyOrders;
use App\Repository\CredentialsRepository;
use App\Repository\Shopify\OrdersRepository;
use App\Utils\Helper;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\shopify\MerchantCredentials;

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
    public function handleRequest(Request $request, CredentialsRepository $credentialsRepository): Response
    {
        // Extract the parameters from the request
        dd($this->mr->getRepository(ShopifyOrders::class)->findAll());
        $orderID = $request->query->get('order_id');
        $totalPrice = $request->query->get('Merc_id');
        $totalPrice = base64_decode($totalPrice);
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

        $credentials = $credentialsRepository->findAll();
        foreach($credentials as $credential){
            if($credential->getShop() == $hostname){
                if ($credential->getLiveChecked())
                    $merchantId = $credential->getLiveMerchantId();
                else
                    $merchantId = $credential->getTestMerchantId();

                $metadata = json_encode(array('url' => $url, 'domain' => $domain, 'error_url' => $errorUrl, 'currency' => $currency, 'total_price' => $totalPrice, 'env' => $env, 'merchant_id' => $merchantId));
                $order = new ShopifyOrders();
                $order->setOrderId($orderID);
                $order->setMetaInfo($metadata);
                $order->setStatus(0);

                $orderDb = $this->mr->getRepository(ShopifyOrders::class)->findBy(["orderId"=> $orderID]);
                if (empty($orderDb)){
                    $this->mr->persist($order);
                }else{
                    $orderDb[0]->setOrderId($orderID);
                    $orderDb[0]->setMetaInfo($metadata);
                    $orderDb[0]->setStatus(0);
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
