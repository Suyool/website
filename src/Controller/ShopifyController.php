<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShopifyController extends AbstractController
{
    /**
     * @Route("/shopify/", name="app_shopify_handle_request")
     */
    public function handleRequest(Request $request): Response
    {
        // Extract the parameters from the request
        $orderID = $request->query->get('order_id');
        $totalPrice = $request->query->get('total_price');
        $url = $request->query->get('url');
        $domain = $request->query->get('domain');
        $errorUrl = $request->query->get('error_url');
        $currency = $request->query->get('currency');
        $env = $request->query->get('env');


    }
}
