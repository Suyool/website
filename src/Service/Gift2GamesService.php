<?php

namespace App\Service;


use App\Utils\Helper;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use function Safe\json_encode;

class Gift2GamesService
{

    private $G2G_API_HOST;
    private $email;
    private $apiKey;
    private $helper;
    private $client;
    private $parentId;
    private $logger;

    public function __construct(HttpClientInterface $client, ParameterBagInterface $params, Helper $helper, LoggerInterface $logger)
    {
        $this->client = $client;
        $this->helper = $helper;
        $this->logger = $logger;
        $this->parentId = 121;

        if ($_ENV['APP_ENV'] == 'prod') {
            $this->G2G_API_HOST = '';
            $this->email = "";
            $this->apiKey = "";
        } else {
            $this->G2G_API_HOST = 'https://gift2games.net/api/';
            $this->email = "testsuyool@suyool.com";
            $this->apiKey = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJlbWFpbCI6IlRlc3RzdXlvb2xAc3V5b29sLmNvbSIsInRva2VuX3RpbWUiOjE2OTgwNTY2OTF9.1K8nmwRLCJD66Imoq_y0hxv0HIgOpd6hjvUbYhhF1Z8";
        }
    }

    public function getCategories()
    {
        try {
            $formData = [
                "parentId" => $this->parentId,
            ];
//            $response = $this->helper->clientRequest($this->METHOD_POST, $this->BOB_API_HOST . 'RetrieveChannelResults',  $body);
//            $status = $response->getStatusCode(); // Get the status code
//            if ($status == 500) {
//                return array(false, "error 500 timeout", 255, "error 500 timeout");
//            }

            $response = $this->helper->clientRequestWithHeaders('POST', $this->G2G_API_HOST . "categories",
                [
                'body' => $formData,
                ],
                [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'Authorization' => $this->apiKey,
                ]
            );

//            dd($response);
            dd($response->getContent());

            $content = $response->getContent();
            $reponse = json_encode($content);
            $ApiResponse = json_decode($content, true);
            $res = $ApiResponse['Response'];
            if ($res == "") {
                return array(false, $ApiResponse['ErrorDescription'], $ApiResponse['ErrorCode'], $reponse);
            }
            $decodedString = $this->_decodeGzipString(base64_decode($res));

            return array(true, $decodedString, $ApiResponse['ErrorDescription'], $ApiResponse['ErrorCode'], $reponse);
        } catch (Exception $e) {
            $this->logger->error("Alfa postpaid error retrieve: {$e->getMessage()}");
            return array(false, $e->getMessage(), 255, $e->getMessage());
        }
    }


}