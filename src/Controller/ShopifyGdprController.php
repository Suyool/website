<?php

namespace App\Controller;

use App\Entity\Shopify\MerchantCredentials;
use App\Entity\Shopify\ShopifyOrders;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShopifyGdprController extends AbstractController
{
    private $mr;

    public function __construct(ManagerRegistry $mr)
    {
        $this->mr = $mr->getManager('Shopify');
    }

    /**
     * @Route("/customers/data", name="customers", methods={"POST"})
     */
    public function customersData(Request $request): Response
    {
        $requestData = $request->request->all();

        // Extract the necessary data from the request
        $shopId = $requestData['shop_id'];
        $shopDomain = $requestData['shop_domain'];
        $ordersRequestedString = $requestData['orders_requested'];

        $ordersRepository = $this->mr->getRepository(ShopifyOrders::class);

        $orderIds = explode(',', $ordersRequestedString);

        $orders = [];
        foreach ($orderIds as $orderId) {
            // Fetch the order details from the repository
            $order = $ordersRepository->findOneBy(['orderId' => $orderId]);

            if ($order) {
                // Sanitize the metaInfo property
                $order->setMetaInfo($this->sanitizeMetaInfo($order->getMetaInfo()));

                // Add the order to the orders array
                $orders[] = $order;
            }
        }

        // Return the array of sanitized orders as JSON response
        return $this->json($orders, Response::HTTP_OK, [], ['json_encode_options' => JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES]);
    }

    private function sanitizeMetaInfo($metaInfo)
    {
        // Perform your sanitization logic on the metaInfo property
        // For example, you can use the PHP `json_decode()` and `json_encode()` functions

        $sanitizedMetaInfo = json_decode($metaInfo, true);
        // Perform any sanitization operations on the $sanitizedMetaInfo array

        // Return the sanitized metaInfo as a JSON-encoded string
        return json_encode($sanitizedMetaInfo, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
    /**
     * @Route("/customers/redact", name="customers_redact", methods={"POST"})
     */
    public function customersRedact(Request $request): Response
    {
        $requestData = $request->request->all();

        // Extract the necessary data from the request
        $ordersRequestedString = $requestData['orders_to_redact'];
        $orderIds = explode(',', $ordersRequestedString);

        $ordersRepository = $this->mr->getRepository(ShopifyOrders::class);

        foreach ($orderIds as $orderId) {
            // Fetch the order details from the repository
            $order = $ordersRepository->findOneBy(['orderId' => $orderId]);

            if ($order) {
                // Delete the order
                $this->mr->remove($order);
                $this->mr->flush();
            }
        }

        // Return a success response
        return new Response('Orders deleted successfully', Response::HTTP_OK);
    }
    /**
     * @Route("/shop/redact", name="customers_redact", methods={"POST"})
     */
    public function shopRedact(Request $request): Response
    {
        $requestData = $request->request->all();

        // Extract the necessary data from the request
        $shop_domain = $requestData['shop_domain'];

        $MerchantRepository = $this->mr->getRepository(MerchantCredentials::class);

            // Fetch the order details from the repository
            $order = $MerchantRepository->findOneBy(['shop' => $shop_domain]);

            if ($order) {
                // Delete the order
                $this->mr->remove($order);
                $this->mr->flush();
            }

        // Return a success response
        return new Response('Shop deleted successfully', Response::HTTP_OK);
    }
}




