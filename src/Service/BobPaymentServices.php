<?php

namespace App\Service;

use App\Utils\Helper;
use Exception;
use PDO;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

use function Safe\json_encode;

class BobPaymentServices
{
    private $BOB_PAYMENT_GATEWAY;
    private $client;

    public function __construct(HttpClientInterface $client, ParameterBagInterface $params, Helper $helper, LoggerInterface $logger)
    {
        $this->client = $client;
        $this->helper = $helper;
        $this->logger = $logger;
        $this->BOB_PAYMENT_GATEWAY="https://test-bobsal.gateway.mastercard.com/api/rest/version/73/merchant/testsuyool/session";
    }

    public function paymentGateWay()
    {
        $body = [
            "apiOperation" => "INITIATE_CHECKOUT",
            "interaction" => [
                "operation" => "PURCHASE",
                "merchant"=>[
                    "name"=>"ARZ MURR"
                ]
            ],
            "order" => [
                "currency" => "USD",
                "id" => "1",
                "amount"=>5,
                "description"=>"ordered goods"
            ]
        ];
        $response = $this->client->request('POST', $this->BOB_PAYMENT_GATEWAY, [
            'body' => json_encode($body),
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'auth_basic' => ['merchant.TESTSUYOOL', '002bcc643011b3cef6967ff40d140d71'],
        ]);

        $content=$response->toArray(false);
        return array(true,$content['session']['id']);
    }
}
