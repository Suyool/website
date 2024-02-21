<?php

namespace App\Controller;


use App\Entity\Simly\Esim;
use App\Entity\Simly\Logs;
use App\Entity\Simly\Order;
use App\Service\Memcached;
use App\Service\NotificationServices;
use App\Service\SimlyServices;
use App\Service\SuyoolServices;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class SimlyController extends AbstractController
{
    private $mr;
    private $params;
    public $cipher_algorithme = "AES128";
    public $key = "SY1X24elh9eG3fpOaHcWlQ9h2bHaqimdIDoyoOaFoi0rukAj3Z";
    public $iv = "fgu26y9e43wc8dj2";
    private $session;
    private $Memcached;

    public function __construct(ManagerRegistry $mr, ParameterBagInterface $params, SessionInterface $sessionInterface, Memcached $memcached)
    {
        $this->mr = $mr->getManager('simly');
        $this->params = $params;
        $this->session = $sessionInterface;
        $this->Memcached = $memcached;
    }

    /**
     * @Route("/alfa", name="simly")
     */
    public function index(NotificationServices $notificationServices)
    {
        $useragent = $_SERVER['HTTP_USER_AGENT'];
        // $_POST['infoString']="Mwx9v3bq3GNGIWBYFJ1f1PcdL3j8SjmsS6y+Hc76TEtMxwGjwZQJHlGv0+EaTI7c";

        if (isset($_POST['infoString'])) {
            // dd($_POST['infoString']);   
            $decrypted_string = SuyoolServices::decrypt($_POST['infoString']);
            // dd($decrypted_string);
            $suyoolUserInfo = explode("!#!", $decrypted_string);
            $devicetype = stripos($useragent, $suyoolUserInfo[1]);

            if ($notificationServices->checkUser($suyoolUserInfo[0], $suyoolUserInfo[2]) && !$devicetype) {
                $SuyoolUserId = $suyoolUserInfo[0];
                $this->session->set('suyoolUserId', $SuyoolUserId);
                // $this->session->set('suyoolUserId', 155);

                $parameters['deviceType'] = $suyoolUserInfo[1];

                return $this->render('simly/index.html.twig', [
                    'parameters' => $parameters
                ]);
            } else {
                return $this->render('ExceptionHandling.html.twig');
            }
        } else {
            return $this->render('ExceptionHandling.html.twig');
        }
    }

    /**
     * @Route("/simly/api", name="app_simly_api")
     */
    public function API(SimlyServices $simlyServices)
    {
        // $res = $simlyServices->IsAuthenticated();
        // dd($res);

        // $res = $simlyServices->Authentication();
        // dd($res);

        // $res = $simlyServices->GetCountriesPlans();
        // dd($res);

        // $res = $simlyServices->GetPlansUsingISOCode('ME');
        // dd($res);

        // $res = $simlyServices->GetAvailableNetworkFromGivenId('simly_FRA_1GB_7D');
        // dd($res);

        //$res = $simlyServices->GetAvailableNetworkFromGivenId('simly_FRA_1GB_7D');

        //$res = $simlyServices->PurchaseTopup('simly_FRA_1GB_7D');
        //$res = $simlyServices->PurchaseTopup('simly_FRA_1GB_7D', "65cf183ab08a52056b17017b");
        // dd($res);

        $res = $simlyServices->GetPlanHavingSlug("simly_FRA_1GB_7D");
        // dd($res);

        //$res = $simlyServices->FetchUsageOfPurchasedESIM("65cf183ab08a52056b17017b");
        //dd($res);

        //$res = $simlyServices->GetAllAvailableCountriesOfContinent("ME");
        //$res = $simlyServices->GetAllAvailableCountriesOfContinent();
        dd($res);
    }


    /**
     * @Route("/simly/getAllAvailableCountries", name="app_simly_getAllAvailableCountries")
     */
    public function GetAllAvailableCountries(SimlyServices $simlyServices, Memcached $Memcached)
    {
        $filter = $Memcached->getAllCountriesBySimly($simlyServices);
        // $filter = $Memcached->getAllCountriesBySimlyFromSimly($simlyServices);

        return new JsonResponse([
            'status' => true,
            'message' => $filter
        ], 200);
    }
    /**
     * @Route("/simly/getLocalAvailableCountries", name="app_simly_getLocalAvailableCountries")
     */
    public function GetLocalAvailableCountries(SimlyServices $simlyServices, Memcached $Memcached)
    {
        $filter = $Memcached->getAllCountriesBySimlyFromSimly($simlyServices);
        return new JsonResponse([
            'status' => true,
            'message' => $filter
        ], 200);
    }

    /**
     * @Route("/simly/getContientAvailableByCountry", name="app_simly_getContientAvailableByCountry")
     */
    public function GetContientAvailableByCountry(SimlyServices $simlyServices, Request $request)
    {
        $country = $request->get('country');
        if (!$country) return new JsonResponse([
            'status' => false,
            'message' => 'Country code is required'
        ]);
        $filter = $simlyServices->GetAllAvailableCountriesOfContinent($country);
        return new JsonResponse([
            'status' => true,
            'message' => $filter
        ], 200);
    }


    /**
     * @Route("/simly/getPlansUsingISOCode", name="app_simly_getPlansUsingISOCode")
     */
    public function GetPlansUsingISOCode(Request $request, SimlyServices $simlyServices)
    {
        $code = $request->get('code');
        if (!$code) return new JsonResponse([
            'status' => false,
            'message' => 'Country code is required'
        ], 400);

        $code = strtoupper($code);

        $res = $simlyServices->GetPlansUsingISOCode($code);
        return new JsonResponse([
            'status' => true,
            'message' => $res
        ], 200);
    }

    /**
     * @Route("/simly/getNetworksById", name="app_simly_getNetworksById")
     */
    public function GetNetworksById(Request $request, SimlyServices $simlyServices)
    {
        $planId = $request->get('planId');
        if (!$planId) return new JsonResponse([
            'status' => false,
            'message' => 'planId is required'
        ], 400);

        $res = $simlyServices->GetAvailableNetworkFromGivenId($planId);
        return new JsonResponse([
            'status' => true,
            'message' => $res
        ], 200);
    }

    /**
     * @Route("/simly/purchaseTopup", name="app_simly_purchaseTopup", methods="POST")
     */
    public function PurchaseTopup(Request $request, SimlyServices $simlyServices, SuyoolServices $suyoolServices, NotificationServices $notificationServices)
    {
        $SuyoolUserId = $this->session->get('suyoolUserId');

        $data = json_decode($request->getContent(), true);
        if (isset($data['parentPlanType'])) {
            $parentPlanType = $data['parentPlanType'];
        } elseif (isset($data['esimId']) && $data['esimId'] != null) {
            $esimRepository = $this->mr->getRepository(Esim::class);
            $esim = $esimRepository->findOneBy(['esimId' => $data['esimId']]);
            $parentPlanType = $esim ? $esim->getParentPlanType() : '';
        } else {
            $parentPlanType = '';
        }

        if (!isset($data['planId'])) {
            $logs = new Logs;
            $logs
                ->setidentifier("simly_purchaseTopup")
                ->seturl("simly/purchaseTopup")
                ->setrequest(json_encode($data))
                ->setresponse("planId and esimid are required")
                ->seterror("planId and esimid are required");
            $this->mr->persist($logs);
            $this->mr->flush();


            return new JsonResponse([
                'status' => false,
                'message' => 'planId and esimid are required'
            ], 400);
        }

        $simlyMerchId = $this->params->get('SIMLY_MERCHANT_ID');
        $simlyPlan = $simlyServices->GetPlanHavingSlug($data['planId']);
        $fees = $simlyPlan['initial_price'] - $simlyPlan['price'];

        $order = new Order();
        $order
            ->setStatus(Order::$statusOrder['PENDING'])
            ->setAmount($simlyPlan['initial_price'])
            ->setFees($fees)
            ->setCurrency('USD');

        if (isset($data['esimId'])) {
            $order->setType('topup');
        } else {
            $order->setType('esim');
        }


        $this->mr->persist($order);
        $this->mr->flush();
        $order_id = $simlyMerchId . "-" . $order->getId();

        $utilityResponse = $suyoolServices->PushUtilities($SuyoolUserId, $order_id, $order->getAmount(), $order->getCurrency(), $order->getFees(), $simlyMerchId);
        if (!$utilityResponse[0]) {

            $order->setStatus(Order::$statusOrder['CANCELED'])
                ->setError(json_encode($utilityResponse));

            $this->mr->persist($order);
            $this->mr->flush();

            $logs = new Logs;
            $logs
                ->setidentifier("simly_purchaseTopup")
                ->seturl("PushUtilities")
                ->setrequest(json_encode(array(
                    'SuyoolUserId' => $SuyoolUserId,
                    'order_id' => $order_id,
                    'amount' => $order->getAmount(),
                    'currency' => $order->getCurrency(),
                    'fees' => $order->getFees(),
                    'simlyMerchId' => $simlyMerchId
                )))
                ->seterror(json_encode($utilityResponse));

            $this->mr->persist($logs);
            $this->mr->flush();

            return new JsonResponse([
                'status' => false,
                'message' => @json_decode($utilityResponse[1], true),
                'flagCode' => @$utilityResponse[2]
            ]);
        }

        $transId = $utilityResponse[1];

        $order
            ->setStatus(Order::$statusOrder['PURCHASED'])
            ->setTransId($transId);

        $this->mr->persist($order);
        $this->mr->flush();

        if ($order->getType() == 'esim') {
            $simlyResponse = $simlyServices->PurchaseTopup($data['planId']);
        } else {
            $simlyResponse = $simlyServices->PurchaseTopup($data['planId'], $data['esimId']);
        }

        if (!isset($simlyResponse['id'])) {
            $logs = new Logs;
            $logs
                ->setidentifier("simly_purchaseTopup")
                ->seturl("simly/purchaseTopup")
                ->setrequest(json_encode($data))
                ->setresponse(json_encode($simlyResponse))
                ->seterror(json_encode($simlyResponse));
            $this->mr->persist($logs);
            $this->mr->flush();

            //return the money to the user
            $order->setStatus(Order::$statusOrder['CANCELED'])
                ->setError($simlyResponse['message']);

            $this->mr->persist($order);
            $this->mr->flush();

            $responseUpdateUtilities = $suyoolServices->UpdateUtilities(0, "", $transId);
            if ($responseUpdateUtilities[0]) {
                $order
                    ->setstatus(Order::$statusOrder['CANCELED'])
                    ->seterror("reversed " . $responseUpdateUtilities[1]);
                $this->mr->persist($order);
                $this->mr->flush();
                $message = "Simly Purchase failed and the money was returned to the user";
            } else {
                $order
                    ->setstatus(Order::$statusOrder['CANCELED'])
                    ->seterror($responseUpdateUtilities[1]);
                $this->mr->persist($order);
                $this->mr->flush();
                $message = "Simly Purchase failed and the money was not returned to the user";
            }

            $this->mr->persist($order);
            $this->mr->flush();

            try {
                $logs = new Logs;
                $logs
                    ->setidentifier("simly_purchaseTopup")
                    ->seturl("UpdateUtilities")
                    ->setrequest(json_encode(array(
                        'amount' => 0,
                        'additionalData' => "",
                        'transId' => $transId
                    )))
                    ->setresponse(json_encode($responseUpdateUtilities))
                    ->seterror($message);

                $this->mr->persist($logs);
                $this->mr->flush();
            } catch (\Exception $e) {
            }


            return new JsonResponse([
                'status' => false,
                'message' => $message
            ]);
        }

        if ($order->getType() == 'esim') {
            if (isset($data['country'])) $country = $data['country'];
            else $country = "";
            $esim = new Esim();
            $esim
                ->setEsimId($simlyResponse['id'])
                ->setSuyoolUserId($SuyoolUserId)
                ->setStatus('active')
                ->setSmdp($simlyResponse['smdp'])
                ->setMatchingId($simlyResponse['matchingID'])
                ->setQrCodeImageUrl($simlyResponse['qrCodeImageUrl'])
                ->setQrCodeString($simlyResponse['qrCodeString'])
                ->setTopups(json_encode($simlyResponse['topups']))
                ->setTransaction(json_encode($simlyResponse['transaction']))
                ->setPlan($simlyResponse['plan'])
                ->setInitialPrice($simlyPlan['initial_price'])
                ->setPrice($simlyPlan['price'])
                ->setParentPlanType($parentPlanType)
                ->setCountry($country)
                ->setCountryImage(@$data['countryImage'])
                ->setAllowedPlans(json_encode($simlyResponse['allowedPlans']));
        } else {
            $esim = $this->mr->getRepository(Esim::class)->findOneBy(['esimId' => $data['esimId']]);
            if (!$esim) {
                $logs = new Logs;
                $logs
                    ->setidentifier("simly_purchaseTopup")
                    ->seturl("simly/purchaseTopup")
                    ->setrequest(json_encode($data['esimId']))
                    ->seterror("Esim not found");

                return new JsonResponse([
                    'status' => false,
                    'message' => 'Esim not found'
                ], 404);
            }

            $esim
                ->setStatus('active')
                ->setMatchingId($simlyResponse['matchingID'])
                ->setQrCodeImageUrl($simlyResponse['qrCodeImageUrl'])
                ->setQrCodeString($simlyResponse['qrCodeString'])
                ->setTopups(json_encode($simlyResponse['topups']))
                ->setTransaction(json_encode($simlyResponse['transaction']));
        }

        $this->mr->persist($esim);
        $this->mr->flush();

        $order
            ->setStatus(Order::$statusOrder['COMPLETED'])
            ->setEsimsId($esim->getId());

        $this->mr->persist($order);
        $this->mr->flush();
        $userDetails = $notificationServices->GetuserDetails($SuyoolUserId);

        $userName = $userDetails[0] . ' ' . $userDetails[1];
        $params = json_encode([
            'amount' => $order->getamount(),
            'currency' => $order->getCurrency(),
            'plan' => @$esim->getPlan(),
            'fname' => $userName,
            'type' => $parentPlanType

        ]);
        $additionalData = "";
        if (isset($data['esimId'])) {
            $content = $notificationServices->getContent('AcceptedSimlyTopupPayment');
        } else {
            $content = $notificationServices->getContent('AcceptedSimlyPurshasePayment');
        }
        $bulk = 0;
        $notificationServices->addNotification($SuyoolUserId, $content, $params, $bulk, $additionalData);

        $updateUtilitiesAdditionalData = json_encode([
            'simID' => $order->getEsimsId(),
            'amount' => $order->getamount(),
            'currency' => $order->getCurrency(),
        ]);

        $responseUpdateUtilities = $suyoolServices->UpdateUtilities($order->getamount(), $updateUtilitiesAdditionalData, $transId);
        if ($responseUpdateUtilities[0]) {
            $message = "Simly Purchase was successful";
        } else {
            $message = "Simly Purchase was successful but the utilities were not updated";
        }

        try {
            $logs = new Logs;
            $logs
                ->setidentifier("simly_purchaseTopup")
                ->seturl("simly/purchaseTopup")
                ->setrequest(json_encode($data))
                ->setresponse(json_encode($simlyResponse))
                ->seterror($message);

            $this->mr->persist($logs);
            $this->mr->flush();
        } catch (\Exception $e) {
        }

        return new JsonResponse([
            'status' => true,
            'message' => $message,
            'data' => $simlyResponse
        ], 200);
    }

    /**
     * @Route("/simly/getUsageOfEsim", name="app_simly_getUsageOfESIM")
     */
    public function GetUsageOfESIM(Request $request, SimlyServices $simlyServices)
    {
        $suyoolUserId = $this->session->get('suyoolUserId');
        // $suyoolUserId = 89;

        $esims = $this->mr->getRepository(Esim::class)->findBy(['suyoolUserId' => $suyoolUserId], ['id' => 'DESC']);
        $usage = [];
        // dd($esims);
        if (!empty($esims)) {
            foreach ($esims as $esim) {
                $res = $simlyServices->FetchUsageOfPurchasedESIM($esim->getEsimId());
                $res['country'] = $esim->getCountry();
                $res['plan'] = $esim->getPlan();
                $res['esimId'] = $esim->getEsimId();
                $res['countryImage'] = $esim->getCountryImage();
                $res['initialPrice'] = $esim->getInitialPrice();
                $res['qrCodeString'] = $esim->getQrCodeString();
                $res['qrCodeImage'] = $esim->getQrCodeImageUrl();
                $res['PlanType'] = $esim->getParentPlanType();

                if ($res)
                    $usage[] = $res;
            }
        }

        // if (empty($usage)) return new JsonResponse([
        //     'status' => false,
        //     'message' => 'No usage found'
        // ]);


        return new JsonResponse([
            'status' => true,
            'message' => $usage
        ], 200);
    }

    /**
     * @Route("/simly/GetEsimDetails", name="GetEsimDetails")
     */
    public function GetEsimDetails(Request $request, SimlyServices $simlyServices)
    {
        $data = json_decode($request->getContent(), true);

        $esims = $this->mr->getRepository(Esim::class)->findBy(['esimId' => $data['esimId']]);
        $fetchDataUsage = $simlyServices->FetchUsageOfPurchasedESIM($data['esimId']);
        $simlyPlan = $simlyServices->GetPlanHavingSlug($esims[0]->getPlan());
        $NetworkAvailable = $simlyServices->GetAvailableNetworkFromGivenId($esims[0]->getPlan());

        $Details = [
            'country' => $esims[0]->getCountry(),
            'countryImage' => $esims[0]->getCountryImage(),
            'DataUsage' => $fetchDataUsage,
            'simlyPlan' => $simlyPlan,
            'NetworkAvailable' => $NetworkAvailable
        ];

        return new JsonResponse([
            'status' => true,
            'message' => $Details
        ], 200);
    }
}
