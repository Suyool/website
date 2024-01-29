<?php

namespace App\Service;


use App\Entity\Gift2Games\Logs;
use App\Utils\Helper;
use Doctrine\Persistence\ManagerRegistry;
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
    private $mr;

    public function __construct(HttpClientInterface $client, ParameterBagInterface $params, Helper $helper, LoggerInterface $logger,ManagerRegistry $mr)
    {
        $this->client = $client;
        $this->helper = $helper;
        $this->logger = $logger;
        $this->parentId = 121;
        $this->mr = $mr->getManager('gift2games');

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

            $response = $this->helper->clientRequestWithHeaders('POST', $this->G2G_API_HOST . "categories",
                [
                'body' => $formData,
                ],
                [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'Authorization' => $this->apiKey,
                ]
            );


            $content = $response->getContent();
            $data = json_decode($content, true);
            $logs = new Logs();
            $logs
                ->setidentifier("Get Categories")
                ->seturl($this->G2G_API_HOST . "categories")
                ->setrequest(json_encode($formData))
                ->setresponse(json_encode($data));

            $this->mr->persist($logs);
            $this->mr->flush();

            if ($data['status'] == 1) {
                return array(
                    'status'=>true,
                    'data'=>$content
                );
            }

            return array(true, $content);
        } catch (Exception $e) {
            $this->logger->error("Gift 2 Games categories error: {$e->getMessage()}");
            return array(false, $e->getMessage(), 255, $e->getMessage());
        }
    }

    public function getProducts($categoryId=null) {
        try {
            $formData = [
                "categoryId" => $categoryId,
            ];

            $headers = [
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Authorization' => $this->apiKey,
            ];

            $response = $this->helper->clientRequestWithHeaders('POST', $this->G2G_API_HOST . "products",
                $formData,
                $headers
            );

            $content = $response->getContent();
            $data = json_decode($content, true);
            $logs = new Logs();
            $logs
                ->setidentifier("Get Products")
                ->seturl($this->G2G_API_HOST . "products")
                ->setrequest(json_encode($formData))
                ->setresponse(json_encode($data));

            $this->mr->persist($logs);
            $this->mr->flush();

            if ($data['status'] == 1) {
                return array(
                    'status'=>true,
                    'data'=>$content
                );
            }

        } catch (Exception $e) {
            $this->logger->error("Gift 2 Games products error: {$e->getMessage()}");
            return array(false, $e->getMessage(), 255, $e->getMessage());
        }
    }

    public function createOrder($ProductId, $transID)
    {
        try {
            $formData = [
                "productId" => $ProductId,
                "referenceNumber" =>$transID
            ];
            $headers = [
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Authorization' => $this->apiKey,
            ];

            $response = $this->helper->clientRequestWithHeaders('POST', $this->G2G_API_HOST . "create_order",
                $formData,
                $headers
            );
            $content = $response->getContent();
            $data = json_decode($content, true);
            $logs = new Logs();
            $logs
                ->setidentifier("Create Order")
                ->seturl($this->G2G_API_HOST . "create_order")
                ->setrequest(json_encode($formData))
                ->setresponse(json_encode($data));

            $this->mr->persist($logs);
            $this->mr->flush();

            if ($data['status'] == 1) {
                return array(
                    'status'=>true,
                    'data'=>$content
                );
            }

            return array(true, $content);
        } catch (Exception $e) {
            $this->logger->error("Gift 2 Games categories error: {$e->getMessage()}");
            return array(false, $e->getMessage(), 255, $e->getMessage());
        }
    }
}