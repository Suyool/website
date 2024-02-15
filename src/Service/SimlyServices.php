<?php

namespace App\Service;

use App\Utils\Helper;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

use function Safe\json_encode;

class SimlyServices
{
    private $SIMLY_API_HOST;
    private $USERNAME;
    private $PASSWORD;
    private $helper;
    private $client;
    private $METHOD_GET;
    private $METHOD_POST;
    private $logger;

    public function __construct(HttpClientInterface $client, ParameterBagInterface $params, Helper $helper, LoggerInterface $logger)
    {
        $this->client = $client;
        $this->METHOD_POST = $params->get('METHOD_POST');
        $this->METHOD_GET = $params->get('METHOD_GET');
        $this->helper = $helper;
        $this->logger = $logger;
        if ($_ENV['APP_ENV'] == 'prod') {
            $this->SIMLY_API_HOST = 'https://api.simly.io/partner/';
            $this->USERNAME = "it@suyool.com";
            $this->PASSWORD = "nu7sq7F2CYcWPTIO";
        } else {
            $this->SIMLY_API_HOST = 'https://api.simly.io/partner/';
            $this->USERNAME = "it@suyool.com";
            $this->PASSWORD = "nu7sq7F2CYcWPTIO";
        }
    }

    private function getResponse($status, $message, $data, $functionName)
    {
        // if ($status == 200) {
        // dd($this->logger);
        $this->logger->error("Simly Request : status-> {$status} ; message-> {$message} ; functionName -> {$functionName}");
        // }
        return [
            'status' => $status,
            'message' => $message,
            'data' => $data
        ];
    }

    public function Authentication(): array
    {
        try {
            $body = [
                "email" => $this->USERNAME,
                "password" => $this->PASSWORD,
            ];
            $response = $this->helper->clientRequest($this->METHOD_POST, $this->SIMLY_API_HOST . 'login',  $body);
            $status = $response->getStatusCode();

            if ($status == 500) {
                $this->getResponse(500, 'Internal Server Error', null, 'Authentication');
            }

            $data = json_decode($response->getContent(), true);

            return $this->getResponse($status, 'Successfully authenticated the user.', $data, 'Authentication');
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
            $this->getResponse(500, 'Internal Server Error', null, 'Authentication');
        }
    }
}
