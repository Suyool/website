<?php

namespace App\Service;

use App\Entity\Simly\Order;
use App\Entity\topup\orders;
use App\Utils\Helper;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Adapter\MemcachedAdapter;
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
    private $mr;
    private $cache;

    public function __construct(HttpClientInterface $client, ParameterBagInterface $params, Helper $helper, LoggerInterface $logger, ManagerRegistry $mr)
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
        $this->mr = $mr->getManager('simly');

        if (MemcachedAdapter::isSupported()) {
            try {
                $client = MemcachedAdapter::createConnection('memcached://localhost');
                $this->cache = new MemcachedAdapter($client, '', 0);
            } catch (\ErrorException $e) {;
            }
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
            $file = ($_ENV['APP_ENV'] == 'prod') ? "../var/cache/prod/SimlyToken.txt" : (($_ENV['APP_ENV'] == 'test') ? "../var/cache/test/SimlyToken.txt" : (($_ENV['APP_ENV'] == 'sandbox') ? "../var/cache/sandbox/SimlyToken.txt" : "../var/cache/dev/SimlyToken.txt"));

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

    public function GetPlansUsingISOCode($code = 'GLOBAL', $suyoolUserId, $HavingCard)
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
            // dd($data);
            foreach ($data['data']['plans'] as $index1 => $data1) {
                if (!is_null($data1) && $data1['duration'] == 1 && $data1['price'] == 1) {
                    if ($HavingCard) {
                        $data['data']['plans'][$index1]['offre'] = true;
                        $data['data']['plans'][$index1]['duration'] = "24 hrs";
                        $data['data']['plans'][$index1]['initial_price'] = 0;
                        $data['data']['plans'][$index1]['initial_price_free'] = "Free";
                        $data['data']['plans'][$index1]['price'] = 0;
                        $isCompletedPerUser = $this->mr->getRepository(Order::class)->fetchIfUserHasBoughtThisEsim($suyoolUserId, $data['data']['plans'][$index1]['planId']);
                        if (!empty($isCompletedPerUser)) {
                            $data['data']['plans'][$index1]['isbought'] = true;
                        }
                    } else {
                        $data['data']['plans'][$index1] = null;
                    }
                }
            }
            // dd($data);
            if ($data['code'] == 200) {
                return $data['data'];
            } else {
                return $this->getResponse(500, 'Internal Server Error', null, 'GetPlansUsingISOCode');
            }
        } catch (Exception $e) {
            // dd($e->getMessage());
            $this->logger->error($e->getMessage());
            return $this->getResponse(500, 'Internal Server Error', null, 'GetPlansUsingISOCode');
        }
    }

    public function GetOffres($suyoolUserId, $HavingCard)
    {
        try {
            $token = $this->IsAuthenticated();
            if (!$token) {
                return $this->getResponse(401, 'Unauthorized', null, 'GetPlansUsingISOCode');
            }
            $offres = [];
            if ($HavingCard) {
                if (isset($this->cache)) {
                    $item = $this->cache->getItem('getoffres');
                    if (!$item->isHit()) {
                        $response1 =  $this->client->request("GET", $this->SIMLY_API_HOST . 'countries/', [
                            'headers' => [
                                'x-simly-token' => $token
                            ],
                        ]);
                        $offres = [];
                        $content1 = $response1->toArray(false);
                        foreach ($content1['data'] as $index => $value) {
                            foreach ($value as $index2 => $value) {
                                // dd($value);
                                $response2 = $this->client->request("GET", $this->SIMLY_API_HOST . 'countries/' . $value['isoCode'] . '', [
                                    'headers' => [
                                        'x-simly-token' => $token
                                    ],
                                ]);
                                $content2 = $response2->toArray(false);
                                foreach ($content2['data']['plans'] as $plans) {
                                    // dd($plans['planId']);
                                    // dd($isCompletedPerUser);
                                    if (!is_null($plans) && $plans['duration'] == 1 && $plans['price'] == 1) {
                                        $isCompletedPerUser = $this->mr->getRepository(Order::class)->fetchIfUserHasBoughtThisEsim($suyoolUserId, $plans['planId']);
                                        // dd($isCompletedPerUser);
                                        if ($isCompletedPerUser != []) {
                                            // dd("ok");
                                            $plans['bought'] = true;
                                        } else {
                                            $plans['bought'] = false;
                                        }
                                        $plans['country'] = $value['name'];
                                        $plans['image'] = $value['countryImageURL'];
                                        $plans['offre'] = true;
                                        $plans['duration'] = "24 hrs";
                                        $plans['initial_price'] = 0;
                                        $plans['initial_price_free'] = "Free";
                                        $plans['price'] = 0;
                                        $offres[] = $plans;
                                    }
                                }
                            }
                        }
                        $item->set($offres)
                            ->expiresAfter(86400);
                        $this->cache->save($item);
                    } else {
                        $offres = $item->get();
                    }
                } else {
                    if ($_ENV['APP_ENV'] == 'prod') {
                        $file = "../var/cache/prod/offresimly.txt";
                    } else {
                        $file = "../var/cache/test/offresimly.txt";
                    }
                    $clearingTime = time() - (60);
                    if (file_exists($file) && (filemtime($file) > $clearingTime) && (filesize($file) > 0)) {
                        $offres = file_get_contents($file);
                        dd("");
                        return json_decode($offres, true);
                    } else {
                        $response1 =  $this->client->request("GET", $this->SIMLY_API_HOST . 'countries/', [
                            'headers' => [
                                'x-simly-token' => $token
                            ],
                        ]);
                        $offres = [];
                        $content1 = $response1->toArray(false);
                        foreach ($content1['data'] as $index => $value) {
                            foreach ($value as $index2 => $value) {
                                $response2 = $this->client->request("GET", $this->SIMLY_API_HOST . 'countries/' . $value['isoCode'] . '', [
                                    'headers' => [
                                        'x-simly-token' => $token
                                    ],
                                ]);
                                $content2 = $response2->toArray(false);
                                foreach ($content2['data']['plans'] as $plans) {
                                    // dd($plans['planId']);
                                    // dd($isCompletedPerUser);
                                    if (!is_null($plans) && $plans['duration'] == 1 && $plans['price'] == 1) {
                                        $isCompletedPerUser = $this->mr->getRepository(Order::class)->fetchIfUserHasBoughtThisEsim($suyoolUserId, $plans['planId']);
                                        // dd($isCompletedPerUser);
                                        if ($isCompletedPerUser != []) {
                                            // dd("ok");
                                            $plans['bought'] = true;
                                        } else {
                                            $plans['bought'] = false;
                                        }
                                        $plans['country'] = $value['name'];
                                        $plans['image'] = $value['countryImageURL'];
                                        $plans['offre'] = true;
                                        $plans['duration'] = "24 hrs";
                                        $plans['initial_price'] = 0;
                                        $plans['initial_price_free'] = "Free";
                                        $plans['price'] = 0;
                                        $offres[] = $plans;
                                    }
                                }
                            }
                        }
                        $myfile = fopen($file, "w");
                        fwrite($myfile, json_encode($offres));
                        fclose($myfile);
                    }
                    return $offres;
                    // $data = json_decode($response->getContent(), true);
                    // // dd($data);
                    // foreach($data['data']['plans'] as $index1=>$data1){
                    //         if(!is_null($data1) && $data1['duration'] == 1 && $data1['price'] == 1){
                    //             if($HavingCard){
                    //             $data['data']['plans'][$index1]['offre']=true;
                    //             $data['data']['plans'][$index1]['duration']="24 hrs";
                    //             $data['data']['plans'][$index1]['initial_price']=0;
                    //             $data['data']['plans'][$index1]['initial_price_free']="Free";
                    //             $data['data']['plans'][$index1]['price']=0;
                    //             $isCompletedPerUser = $this->mr->getRepository(Order::class)->fetchIfUserHasBoughtThisEsim($suyoolUserId,$data['data']['plans'][$index1]['planId']);
                    //             if(!empty($isCompletedPerUser)){
                    //                 $data['data']['plans'][$index1]['isbought']=true;
                    //             }
                    //             }
                    //             else
                    //             {
                    //                 $data['data']['plans'][$index1] = null;
                    //             }
                    //         }
                    // }
                    // // dd($data);
                    // if ($data['code'] == 200) {
                    //     return $data['data'];
                    // } else {
                    //     return $this->getResponse(500, 'Internal Server Error', null, 'GetPlansUsingISOCode');
                    // }

                }
            }
        } catch (Exception $e) {
            // dd($e->getMessage());
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
                return array($data['data'], json_encode($body), json_encode($data), $this->SIMLY_API_HOST . 'esims/purchase', $response->getStatusCode());
            } else {
                return array(500, 'Internal Server Error', json_encode($data), 'PurchaseTopup', $response->getStatusCode());
            }
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
            return array(500, 'Internal Server Error', $e->getMessage(), 'PurchaseTopup', 500);
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
            $data = $response->toArray(false);

            if ($data['code'] == 200) {
                return $data['data'];
            } else {
                return array($response->getStatusCode(), json_encode($data), null, 'FetchUsageOfPurchasedESIM');
            }
        } catch (Exception $e) {
            // $this->logger->error($e->getMessage());
            return array(500, 'Internal Server Error', null, 'FetchUsageOfPurchasedESIM');
        }
    }

    public function GetAllAvailableCountriesOfContinent($country = null)
    {
        try {
            $token = $this->IsAuthenticated();
            if (!$token) {
                return $this->getResponse(401, 'Unauthorized', null, 'GetAllAvailableCountriesOfContinent');
            }
            if ($country == null) {
                $Url = $this->SIMLY_API_HOST . 'continents';
            } else {
                $Url = $this->SIMLY_API_HOST . 'continents?continent=' . $country . '';
            }
            $response = $this->client->request("GET", $Url, [
                'headers' => [
                    'x-simly-token' => $token
                ],
            ]);
            $data = json_decode($response->getContent(), true);
            if ($data['code'] == 200) {
                foreach ($data['data'] as &$continent) {
                    if (isset($continent['GLOBAL'])) {
                        return $data['data'];
                    } else {
                        $filteredContinent = [];
                        foreach ($continent as $isoCodes => &$countries) {
                            $filteredCountries = [];
                            foreach ($countries as $country) {
                                if ($country['isoCode'] !== "ISR") {
                                    $filteredCountries[] = $country;
                                }
                            }
                            if (!empty($filteredCountries)) {
                                $filteredContinent[$isoCodes] = $filteredCountries;
                            }
                        }
                        if (!empty($filteredContinent)) {
                            $filteredData[] = $filteredContinent;
                        }
                    }
                }
                return $filteredData;
            } else {
                return $this->getResponse(500, 'Internal Server Error', null, 'GetAllAvailableCountriesOfContinent');
            }
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
            return $this->getResponse(500, 'Internal Server Error', null, 'GetAllAvailableCountriesOfContinent');
        }
    }
    public function GetPlanHavingSlug($slug)
    {
        try {
            $token = $this->IsAuthenticated();
            if (!$token) {
                return $this->getResponse(401, 'Unauthorized', null, 'GetPlanHavingSlug');
            }
            $response = $this->client->request("GET", $this->SIMLY_API_HOST . 'plans/' . $slug . '', [
                'headers' => [
                    'x-simly-token' => $token
                ],
            ]);
            // $data = json_decode($response->getContent(), true);
            $data = $response->toArray(false);
            // dd($data);
            if (!is_null($data['data']) && $data['data']['duration'] == 1 && $data['data']['price'] == 1) {
                $data['data']['price'] = 0;
                $data['data']['initial_price'] = 0;
                $data['data']['offre'] = true;
            }
            if ($data['code'] == 200) {
                return array($data['data'], json_encode($data), $this->SIMLY_API_HOST . 'plans/' . $slug . '', $response->getStatusCode());
            } else {
                return array("", json_encode(@$data), $this->SIMLY_API_HOST . 'plans/' . $slug . '', $response->getStatusCode());
            }
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
            return array(500, 'Internal Server Error', null, 'GetPlanHavingSlug');
        }
    }

    public function getPlanById($planId)
    {
        //dummy data
        //{
        //     "duration": 7,
        //     "size": 1,
        //     "price": 7.65,
        //     "planId": "simly_GLOBAL_1GB_7D",
        //     "activationPolicy": "The validity period starts when the SIM connects to any supported networks.",
        //     "topup": true,
        //     "planType": "Data Only",
        //     "isManualAPNRequired": false,
        //     "isKYCRequired": false,
        //     "apn": "globalData"
        // }

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
