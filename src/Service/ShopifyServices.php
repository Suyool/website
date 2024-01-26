<?php

namespace App\Service;

use App\Entity\Shopify\ShopifyInstallation;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Contracts\HttpClient\HttpClientInterface;

use function Safe\json_encode;

class ShopifyServices
{

    private $client;
    private $mr;

    public function __construct(HttpClientInterface $client,ManagerRegistry $mr)
    {
        $this->client = $client;
        $this->mr = $mr->getManager('Shopify');

        if ($_ENV['APP_ENV'] == "test") {
            $this->SUYOOL_API_HOST = 'http://10.20.80.62/api/OnlinePayment';
        }
        else if ($_ENV['APP_ENV'] == "sandbox" || $_ENV['APP_ENV'] == 'dev' ){
            $this->SUYOOL_API_HOST = 'https://externalservices.suyool.money/api/OnlinePayment';
        }
        else {
            $this->SUYOOL_API_HOST = 'https://externalservices.nicebeach-895ccbf8.francecentral.azurecontainerapps.io/api/OnlinePayment';
        }
    }

    public function getQr($data)
    {
        $response = $this->client->request('POST', $this->API_HOST . $data['url'], [
            'body' => $data['data'],
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);

        $content = $response->getContent();

        $response = json_decode($content, true);

        return $response;
    }
    public function updateStatusShopify($data)
    {
        $response = $this->client->request('POST',$this->SUYOOL_API_HOST . $data['url'], [
            'body' => $data['data'],
            'headers' => [
                'Content-Type' => 'application/json',
//                'X-Shopify-Access-Token: ' . $accessToken
            ]
        ]);

        $content = $response->getContent();

        $response = json_decode($content, true);

        return $response;
    }
//    public function getShopifyOrder($order_id,$accessToken,$domain)
//    {
//        $url = 'https://' . $domain . '/admin/api/2020-04/orders/' . $order_id . '/transactions.json';
//        $response = $this->client->request('GET', $url, [
//            'headers' => [
//                'Content-Type' => 'application/json',
//                'X-Shopify-Access-Token: ' . $accessToken
//            ]
//        ]);
//
//        $content = $response->getContent();
//
//        $response = json_decode($content, true);
//
//        return $response;
//    }

    public function getShopifyOrder($order_id,$appKey, $appPass,$domain)
    {
        //$url = 'https://' . $domain . '/admin/api/2020-04/orders/' . $order_id . '/transactions.json';
        $url = 'https://'.$appKey.':'.$appPass.'@'.$domain.'/admin/api/2020-04/orders/'.$order_id.'/transactions.json';

        $response = $this->client->request('GET', $url, [
            'headers' => [
                'Content-Type' => 'application/json',
//                'X-Shopify-Access-Token: ' . $accessToken
            ]
        ]);

        $content = $response->getContent();

        $response = json_decode($content, true);

        return $response;
    }
    public function getCredentials($domain)
    {
        $credentialsRepository = $this->mr->getRepository(ShopifyInstallation::class);
        $credential = $credentialsRepository->findOneBy(['domain' => $domain]);

        $response = [];
        $response['certificate'] = $credential->getCertificateKey();
        $response['merchantId'] = $credential->getMerchantId();
        $response['appKey'] = $credential->getAppKey();
        $response['appSecret'] = $credential->getAppSecret();
        $response['appPass'] = $credential->getAppPass();
        $response['integrationType'] = $credential->getIntegrationType();

        return $response;
    }
}
