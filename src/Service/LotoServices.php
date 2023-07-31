<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class LotoServices
{
    private $LOTO_API_HOST;

    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;

        if ($_ENV['APP_ENV'] == 'prod') {
            $this->LOTO_API_HOST = 'https://backbone.lebaneseloto.com/Service.asmx/';
        } else {
            $this->LOTO_API_HOST = 'https://backbone.lebaneseloto.com/Service.asmx/';
        }
    }
    public function Login()
    {
        $response = $this->client->request('POST', "{$this->LOTO_API_HOST}LoginUser", [
            'body' => json_encode([
                'Username' => 'suyool',
                'Password' => 'ZvNud5qY3qmM3@h',
            ]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        $content = $response->toArray();
        $token = $content['d']['token'];
        return $token;
    }


    public function VoucherFilter($vcategory)
    {
        $response = $this->client->request('POST', "{$this->LOTO_API_HOST}GetAllVouchersType", [
            'body' => json_encode([
                "Token" => "",
            ]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);

        $content = $response->toArray();

        $filteredVouchers = array_filter($content["d"]["ppavouchertypes"], function ($voucher) use ($vcategory) {
            return $voucher["vouchercategory"] === $vcategory;
        });

        return $filteredVouchers;
    }

    public function BuyPrePaid($Token, $category, $type)
    {
        // dd($this->Login());
        $response = $this->client->request('POST', $this->LOTO_API_HOST . '/PurchaseVoucher', [
            'body' => json_encode([
                "Token" => $this->Login(),
                "category" => $category,
                "type" => $type,
            ]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);

        $content = $response->getContent();
        $content = $response->toArray();
        // dd($content);
        return $content;
    }
}
