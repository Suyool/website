<?php

namespace App\Controller;

use App\Entity\Shopify\MerchantCredentials;
use App\Entity\Shopify\RequestedData;
use App\Entity\Shopify\Orders;
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
     *
     * @Route("/customers/data", name="customers", methods={"POST"})
     */
    public function customersData(Request $request): Response
    {
        $requestData = $request->request->all();
        // Extract the necessary data from the request
        $shopId = $requestData['shop_id'];
        $shopDomain = $requestData['shop_domain'];
        $ordersRequestedString = $requestData['orders_requested'];

        $ordersRepository = $this->mr->getRepository(Orders::class);

        $orderIds = explode(',', $ordersRequestedString);

        $orders = [];
        foreach ($orderIds as $orderId) {
            // Fetch the order details from the repository
            $order = $ordersRepository->findOneBy(['orderId' => $orderId]);

            if (!empty($order) && $order->getFlag()==0)
                $orders[] = $order;
        }
        $this->saveRequestedData($requestData,$orders);

        // Return the array of sanitized orders as JSON response
        return $this->json($orders, Response::HTTP_OK, [], ['json_encode_options' => JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES]);
    }

    /**
     * @Route("/customers/redact", name="customersredact", methods={"POST"})
     */
    public function customersRedact(Request $request): Response
    {
        $requestData = $request->request->all();

        // Extract the necessary data from the request
        $ordersRequestedString = $requestData['orders_to_redact'];
        $orderIds = explode(',', $ordersRequestedString);

        $ordersRepository = $this->mr->getRepository(Orders::class);

        foreach ($orderIds as $orderId) {
            // Fetch the order details from the repository
            $order = $ordersRepository->findOneBy(['orderId' => $orderId]);
            if (!empty($order)) {
                // set flag to 1 DELETED
                $order->setFlag(1);
                $this->mr->persist($order);
                $this->mr->flush();

            }
        }
        $response = "Orders deleted successfully";
        $this->saveRequestedData($requestData,$response);
        // Return a success response
        return new Response($response, Response::HTTP_OK);
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
        $response = "Shop deleted successfully";
        $this->saveRequestedData($requestData,$response);

        // Return a success response
        return new Response($response, Response::HTTP_OK);
    }

    private function saveRequestedData(array $request, $response) {
        $result = "";
        if(is_array($response)){
            foreach ($response as $res) {
                $data = "Order_id:". $res->getOrderId() . ", Price: " . $res->getAmount() . ", Status:" . $res->getStatus();
                $result .= $data . " ; ";
            }
        }

        $queryStringRequest= http_build_query($request);
        $queryStringRequest = str_replace('%22', ' , ', $queryStringRequest);

        $requestedData = new RequestedData();
        $requestedData->setShop($request['shop_domain']);
        $requestedData->setData("Request: ".$queryStringRequest . " Response: " . $result);

        $this->mr->persist($requestedData);
        $this->mr->flush();
    }

    private function _sanitizeMetaInfo($metaInfo)
    {
        $sanitizedMetaInfo = json_decode($metaInfo, true);
        // Return the sanitized metaInfo as a JSON-encoded string
        return json_encode($sanitizedMetaInfo, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
