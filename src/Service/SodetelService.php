<?php

namespace App\Service;


use App\Utils\Helper;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use function Safe\json_encode;
use Exception;

class SodetelService
{

    private $SODETEL_API_HOST;
    private $customerNumber;
    private $customerKey;
    private $helper;
    private $client;
    private $logger;

    public function __construct(HttpClientInterface $client, ParameterBagInterface $params, Helper $helper, LoggerInterface $logger)
    {
        $this->client = $client;
        $this->helper = $helper;
        $this->logger = $logger;

        if ($_ENV['APP_ENV'] == 'prod') {
            $this->SODETEL_API_HOST = 'https://ws.sodetel.net.lb/';
            $this->customerNumber = "C063814";
            $this->customerKey = "9981373D9AC51";
        } else {
            $this->SODETEL_API_HOST = 'https://ws.sodetel.net.lb/';
            $this->customerNumber = "T00004";
            $this->customerKey = "F6D29C012B90G";
        }
    }

    public function getAvailableCards($service, $identifier)
    {
        $signature = hash('sha256', $service . $this->customerNumber . $this->customerKey . $identifier);

        try {
            $formData = [
                'service' => $service,
                'customer_number' => $this->customerNumber,
                'identifier' => $identifier,
                'signature' => $signature,
            ];


            $response = $this->client->request('POST', $this->SODETEL_API_HOST . "getavailablecards.php", [
                'body' => $formData,
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
            ]);


            $content = $response->getContent();
            $data = json_decode($content, true);
            if (!empty($data) && $data['customerid'] == $identifier) {
                return array(
                    'status'=>true,
                    'data'=>$data
                );
            }

            return array(true, $content);
        } catch (Exception $e) {
            $this->logger->error("Sodetel Error: {$e->getMessage()}");
            return array(false, $e->getMessage(), 255, $e->getMessage());
        }
    }

    public function refill($service, $plan, $identifier, $requestIndex)
    {
        $signature = hash('sha256', $plan . $service . $requestIndex . $this->customerNumber . $this->customerKey . $identifier);

        try {
            $formData = [
                'service' => $service,
                'plan' => $plan,
                'customer_number' => $this->customerNumber,
                'requestindex' => $requestIndex,
                'identifier' => $identifier,
                'signature' => $signature,
            ];
//            dd($formData);

            $response = $this->client->request('POST', $this->SODETEL_API_HOST . "refillapi.php", [
                'body' => $formData,
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
            ]);

            return $response->getContent();

        } catch (Exception $e) {
            $this->logger->error("Sodetel error: {$e->getMessage()}");
            return json_encode(array(array('result'=>false, 'message'=>$e->getMessage()), 255, $e->getMessage()));
        }
    }
}