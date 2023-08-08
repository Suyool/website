<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

use function Safe\json_encode;

class ShopifyServices
{

    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;

        if ($_ENV['APP_ENV'] == 'prod') {
            $this->API_HOST = 'https://externalservices.nicebeach-895ccbf8.francecentral.azurecontainerapps.io/';
        } else {
            $this->API_HOST = 'https://externalservices.nicebeach-895ccbf8.francecentral.azurecontainerapps.io/';
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
    public function updateStatusShopify($data,$accessToken)
    {
        $response = $this->client->request('POST', $data['url'], [
            'body' => $data['data'],
            'headers' => [
                'Content-Type' => 'application/json',
                'X-Shopify-Access-Token: ' . $accessToken
            ]
        ]);

        $content = $response->getContent();

        $response = json_decode($content, true);

        return $response;
    }
    public function getShopifyOrder($order_id,$accessToken,$domain)
    {
        $url = 'https://' . $domain . '/admin/api/2020-04/orders/' . $order_id . '/transactions.json';
        $response = $this->client->request('GET', $url, [
            'headers' => [
                'Content-Type' => 'application/json',
                'X-Shopify-Access-Token: ' . $accessToken
            ]
        ]);

        $content = $response->getContent();

        $response = json_decode($content, true);

        return $response;
    }
}
