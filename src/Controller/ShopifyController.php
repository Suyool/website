<?php

namespace App\Controller;

use App\Entity\Invoices\merchants;
use App\Entity\Shopify\ShopifyInstallation;
use App\Entity\Shopify\ShopifyOrders;
use App\Entity\Shopify\Orders;
use App\Entity\Shopify\OrdersTest;
use App\Service\InvoiceServices;
use App\Service\ShopifyServices;
use App\Utils\Helper;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
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
    public function handleRequest(Request $request, ShopifyServices $shopifyServices, InvoiceServices $invoicesServices, SessionInterface $session, $cardpayment = null): Response
    {

        $orderID = $request->query->get('order_id');
        $domain = $request->query->get('domain');

        $hostname = Helper::getHost($domain);
        $merchantCredentials = $shopifyServices->getCredentials(Helper::getHost($domain));
        if (!$merchantCredentials || !isset($merchantCredentials['appKey']) || !isset($merchantCredentials['appPass'])) {
            return $this->jsonErrorResponse("Merchant credentials are missing or incomplete.");
        }
        $appKey = $merchantCredentials['appKey'];
        $appPass = $merchantCredentials['appPass'];
        $checkShopifyOrder = $shopifyServices->getShopifyOrder($orderID, $appKey, $appPass, $hostname);

        if (!$checkShopifyOrder || !isset($checkShopifyOrder['transactions'][0]['amount'])) {
            return $this->jsonErrorResponse("Unable to retrieve Shopify order information");
        }

        $totalPrice = $checkShopifyOrder['transactions']['0']['amount'];
        $url = $request->query->get('url') ?? null;
        $errorUrl = $request->query->get('error_url') ?? null;
        $currency = $request->query->get('currency') ?? null;
        $env = $request->query->get('env') ?? null;

        if($cardpayment) {
            $session->set('shopifyCardPayment', true);
            $session->set('orderIdToShopify',$orderID);

            $currency = $request->query->get('currency');
            $merchantID = $request->query->get('merchantID');
            $callBackURL = $request->query->get('callBackURL');
            $additionalInfo = $request->query->get('additionalInfo');
            $secureHash = $request->query->get('secureHash');
            $currentHost = $request->getHost();

            $formattedPrice = number_format($totalPrice, 3);
            $merchant = $invoicesServices->findMerchantByMerchId($merchantID);
            if (!$merchant) {
                return new Response("Merchant not found. Please contact support.");
            }
            $certificate = $merchant->getCertificate();
            if (!$certificate) {
                return new Response("Certificate not found for the merchant. Please contact support.");
            }
            $secure = $orderID . $merchantID . $currency . $additionalInfo . $certificate;
            $suyoolSecureHash = base64_encode(hash('sha512', $secure, true));
            if($suyoolSecureHash == $secureHash){
                $order = new Orders();
                $order->setOrderId($orderID);
                $order->setShopName($domain);
                $order->setAmount($formattedPrice);
                $order->setCurrency($currency);
                $order->setCallbackUrl($url);
                $order->setErrorUrl($errorUrl);
                $order->setEnv($env);
                $order->setMerchantId($merchantID);
                $order->setStatus(0);
                $order->setFlag(0);

                $this->mr->persist($order);
                $this->mr->flush();

                $apiSecure = $orderID . $merchantID .$formattedPrice .$currency.$additionalInfo . $certificate;
                $APISecureHash = base64_encode(hash('sha512', $apiSecure, true));
                $url = 'http://'.$currentHost.'/cardpayment/?Amount='.$formattedPrice.'&TranID='.$orderID.'&Currency='.$currency.'&MerchantID='.$merchantID.'&CallBackURL='.urlencode($callBackURL) .'&SecureHash=' .urlencode($APISecureHash);
                return new RedirectResponse($url);

            }else {
                return $this->jsonErrorResponse("Your order cannot be processed. Please contact support.");
            }
        }

        if ($url === null || $errorUrl === null || $currency === null ) {
            // Handle the error, log it, and return an appropriate response
            return $this->jsonErrorResponse("Your order cannot be processed.Either you have not set error URL or success URL or currency in your request. Please contact support.");

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
    private function jsonErrorResponse(string $message, int $statusCode = Response::HTTP_BAD_REQUEST): JsonResponse
    {
        return new JsonResponse(['error' => $message], $statusCode);
    }
}
