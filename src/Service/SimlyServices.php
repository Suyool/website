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
        if ($status != 200) {
            $this->logger->error("Simly Request : status-> {$status} ; message-> {$message} ; functionName -> {$functionName}");
        }
        return [
            'status' => $status,
            'message' => $message,
            'data' => $data
        ];
    }

    public function IsAuthenticated()
    {
        try {
            if ($_ENV['APP_ENV'] == 'prod') {
                $file = "../var/cache/prod/SimlyToken.txt";
            } else {
                $file = "../var/cache/dev/SimlyToken.txt";
            }

            if (file_exists($file)) {
                $fileModificationTime = filemtime($file);
            } else {
                $fileModificationTime = 0;
            }

            $cacheExpiration = 3600;
            $currentTime = time();

            if ($fileModificationTime + $cacheExpiration > $currentTime && filesize($file) > 0) {
                $operationsjson = file_get_contents($file);
                return json_decode($operationsjson, true);
            } else {
                $body = [
                    "email" => $this->USERNAME,
                    "password" => $this->PASSWORD,
                ];
                $response = $this->helper->clientRequest("POST", $this->SIMLY_API_HOST . 'login', $body);
                $status = $response->getStatusCode();

                if ($status === 500) {
                    return $this->getResponse(500, 'Internal Server Error', null, 'IsAuthenticated');
                }

                $data = json_decode($response->getContent(), true);

                if ($status !== 200 && !isset($data['data']['token'])) {
                    return false;
                } else {
                    $token = $data['data']['token'];
                    $jsonData = json_encode($data['data']['token']);
                    $myfile = fopen($file, "w") or die("Unable to open file!");
                    fwrite($myfile, $jsonData);
                    fclose($myfile);
                    return $token;
                }
            }
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
            return $this->getResponse(500, 'Internal Server Error', null, 'IsAuthenticated');
        }
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
            dd($data);

            return $this->getResponse($status, 'Successfully authenticated the user.', $data, 'Authentication');
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
            $this->getResponse(500, 'Internal Server Error', null, 'Authentication');
        }
    }

    public function GetCountriesPlans()
    {
        try {
            $token = $this->IsAuthenticated();
            // dd($token);
            if (!$token) {
                return $this->getResponse(401, 'Unauthorized', null, 'GetCountriesPlans');
            }
            $response = $this->client->request("GET", $this->SIMLY_API_HOST . 'countries', [
                'headers' => [
                    'x-simly-token' => $token
                ],
            ]);
            $data = json_decode($response->getContent(), true);

            if ($data['code'] == 200) {
                return $data['data'];
            } else {
                return $this->getResponse(500, 'Internal Server Error', null, 'GetCountriesPlans');
            }
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
            return $this->getResponse(500, 'Internal Server Error', null, 'GetCountriesPlans');
        }
    }

    public function GetPlansUsingISOCode($code = 'GLOBAL')
    {
        try {
            $token = $this->IsAuthenticated();
            if (!$token) {
                return $this->getResponse(401, 'Unauthorized', null, 'GetPlansUsingISOCode');
            }
            $response = $this->client->request("GET", $this->SIMLY_API_HOST . 'countries/' . $code . '', [
                'headers' => [
                    'x-simly-token' => $token
                ],
            ]);
            $data = json_decode($response->getContent(), true);

            if ($data['code'] == 200) {
                return $data['data'];
            } else {
                return $this->getResponse(500, 'Internal Server Error', null, 'GetPlansUsingISOCode');
            }
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
            return $this->getResponse(500, 'Internal Server Error', null, 'GetPlansUsingISOCode');
        }
    }

    public function GetAvailableNetworkFromGivenId($planId)
    {
        try {
            $token = $this->IsAuthenticated();
            if (!$token) {
                return $this->getResponse(401, 'Unauthorized', null, 'GetAvailableNetworkFromGivenId');
            }

            $response = $this->client->request("GET", $this->SIMLY_API_HOST . 'networks/' . $planId . '', [
                'headers' => [
                    'x-simly-token' => $token
                ],
            ]);
            $data = json_decode($response->getContent(), true);

            if ($data['code'] == 200) {
                return $data['data'];
            } else {
                return $this->getResponse(500, 'Internal Server Error', null, 'GetAvailableNetworkFromGivenId');
            }
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
            return $this->getResponse(500, 'Internal Server Error', null, 'GetAvailableNetworkFromGivenId');
        }
    }

    public function PurchaseTopup($planId, $esimId = null)
    {
        try {
            $token = $this->IsAuthenticated();
            if (!$token) {
                return $this->getResponse(401, 'Unauthorized', null, 'PurchaseTopup');
            }

            if ($esimId != null) {
                $body = [
                    "planId" => $planId,
                    "esimId" => $esimId
                ];
            } else {
                $body = [
                    "planId" => $planId
                ];
            }

            $response = $this->client->request("POST", $this->SIMLY_API_HOST . 'esims/purchase', [
                'body' => $body,
                'headers' => [
                    'x-simly-token' => $token
                ],
            ]);
            $data = json_decode($response->getContent(), true);

            if ($data['code'] == 200) {
                return $data['data'];
            } else {
                return $this->getResponse(500, 'Internal Server Error', null, 'PurchaseTopup');
            }
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
            return $this->getResponse(500, 'Internal Server Error', null, 'PurchaseTopup');
        }
    }

    public function FetchUsageOfPurchasedESIM($esimId)
    {
        try {
            $token = $this->IsAuthenticated();
            if (!$token) {
                return $this->getResponse(401, 'Unauthorized', null, 'FetchUsageOfPurchasedESIM');
            }

            $response = $this->client->request("GET", $this->SIMLY_API_HOST . 'esims/usage/' . $esimId . '', [
                'headers' => [
                    'x-simly-token' => $token
                ],
            ]);
            $data = json_decode($response->getContent(), true);

            if ($data['code'] == 200) {
                return $data['data'];
            } else {
                return $this->getResponse(500, 'Internal Server Error', null, 'FetchUsageOfPurchasedESIM');
            }
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
            return $this->getResponse(500, 'Internal Server Error', null, 'FetchUsageOfPurchasedESIM');
        }
    }

    public function getPlanById($planId)
    {
        //dummy data
        //{
        //                "duration": 7,
        //                "size": 1,
        //                "price": 7.65,
        //                "planId": "simly_GLOBAL_1GB_7D",
        //                "activationPolicy": "The validity period starts when the SIM connects to any supported networks.",
        //                "topup": true,
        //                "planType": "Data Only",
        //                "isManualAPNRequired": false,
        //                "isKYCRequired": false,
        //                "apn": "globalData"
        //            }

        return [
            "duration" => 7,
            "size" => 1,
            "price" => 7.65,
            "planId" => "simly_GLOBAL_1GB_7D",
            "activationPolicy" => "The validity period starts when the SIM connects to any supported networks.",
            "topup" => true,
            "planType" => "Data Only",
            "isManualAPNRequired" => false,
            "isKYCRequired" => false,
            "apn" => "globalData"
        ];
    }
}
 
