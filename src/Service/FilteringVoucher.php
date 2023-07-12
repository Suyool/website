<?php

namespace App\Service;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class FilteringVoucher
{
    private $LOTO_API_HOST;

    private $client;

    public function __construct( HttpClientInterface $client)
    {
        $this->client = $client;

        //todo: if prod environment
        if ($_ENV['APP_ENV'] == 'prod') {
            $this->LOTO_API_HOST = 'https://backbone.lebaneseloto.com/Service.asmx/';
        } else {
            $this->LOTO_API_HOST = 'https://backbone.lebaneseloto.com/Service.asmx/';
        }
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
    
}
