<?php

namespace App\Controller;


use App\Entity\Simly\Esim;
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
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");
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
     * @Route("/simly", name="app_simly")
     */
    public function index(NotificationServices $notificationServices)
    {
        $parameters['deviceType'] = 'Android';
        dd($parameters);

        // return $this->render('simly/index.html.twig', [
        //     'parameters' => $parameters
        // ]);
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

         $res = $simlyServices->GetAvailableNetworkFromGivenId('simly_FRA_1GB_7D');

        // $res = $simlyServices->PurchaseTopup('simly_FRA_1GB_7D');
//         $res = $simlyServices->PurchaseTopup('simly_FRA_1GB_7D', "65cf183ab08a52056b17017b");
        // dd($res);

//         $res = $simlyServices->FetchUsageOfPurchasedESIM("65cf183ab08a52056b17017b");
         dd($res);
    }


    /**
     * @Route("/simly/getAllAvailableCountries", name="app_simly_getAllAvailableCountries")
     */
    public function GetAllAvailableCountries(SimlyServices $simlyServices, Memcached $Memcached)
    {
        $filter =  $Memcached->getAllCountriesBySimly($simlyServices);
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
        if(!$code) return new JsonResponse([
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
        if(!$planId) return new JsonResponse([
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
    public function PurchaseTopup(Request $request, SimlyServices $simlyServices, SuyoolServices $suyoolServices) {
//        $SuyoolUserId = $this->session->get('suyoolUserId');
        $SuyoolUserId = 218;

        $data = json_decode($request->getContent(), true);

        if(!isset($data['planId'])){
            //logs here
            return new JsonResponse([
                'status' => false,
                'message' => 'planId and esimid are required'
            ], 400);
        }

        $simlyMerchId = $this->params->get('SIMLY_MERCHANT_ID');

        $order = new Order();
        $order
            ->setEsimsId(0)
            ->setStatus(Order::$statusOrder['PENDING'])
            ->setAmount(0)
            ->setFees(0)
            ->setCurrency('USD')
            ->setTransId(0);

        if (isset($data['esimId'])) {
            $order->setType('topup');
        }else {
            $order->setType('esim');
        }

        $this->mr->persist($order);
        $this->mr->flush();

        $order_id = $simlyMerchId."-".$order->getId();
        $utilityResponse = $suyoolServices->PushUtilities($SuyoolUserId, $order_id, $order->getAmount(), $order->getCurrency(), 0);

        if(!$utilityResponse[0]){
            //logs here

            $order->setStatus(Order::$statusOrder['CANCELED'])
                ->setError($utilityResponse[1]);

            $this->mr->persist($order);
            $this->mr->flush();

            return new JsonResponse([
                'status' => false,
                'message' => 'Error in pushing utility'
            ], 500);
        }

        $transId = $utilityResponse[1];

        $order
            ->setStatus(Order::$statusOrder['HELD'])
            ->setTransId($transId);

        $this->mr->persist($order);
        $this->mr->flush();

        $simlyResponse = $simlyServices->PurchaseTopup($data['planId'], $data['esimId']);

        if (!isset($simlyResponse['id'])) {
            //logs here with $simlyResponse['message']
            //return the money to the user

            $order->setStatus(Order::$statusOrder['CANCELED'])
                ->setError($simlyResponse['message']);

            $this->mr->persist($order);
            $this->mr->flush();

            $responseUpdateUtilities = $suyoolServices->UpdateUtilities(0, "", $transId);
            if ($responseUpdateUtilities[0]) {
                $message = "Simly Purchase failed and the money was returned to the user";
            }else {
                $message = "Simly Purchase failed and the money was not returned to the user";
            }

            $this->mr->persist($order);
            $this->mr->flush();

            return new JsonResponse([
                'status' => false,
                'message' => $message
            ], 500);
        }

        if ($order->getType() == 'esim') {
            $esim = new Esim();
        }else {
            $esim = $this->mr->getRepository(Esim::class)->find($simlyResponse['id']);
        }

        $esim
            ->setEsimsId($simlyResponse['id'])
            ->setSuyoolUserId($SuyoolUserId)
            ->setStatus('active')
            ->setSmdp($simlyResponse['smdp'])
            ->setMatchingId($simlyResponse['matchingId'])
            ->setQrCodeImageUrl($simlyResponse['qrCodeImageUrl'])
            ->setQrCodeString($simlyResponse['qrCodeString'])
            ->setTopups(json_encode($simlyResponse['topups']))
            ->setTransaction(json_encode($simlyResponse['transaction']))
            ->setPlan($data['plan'])
            ->setAllowedPlans(json_encode($simlyResponse['allowedPlans']));


        $this->mr->persist($esim);
        $this->mr->flush();

        $order
            ->setStatus(Order::$statusOrder['COMPLETED'])
            ->setEsimsId($esim->getId());

        $this->mr->persist($order);
        $this->mr->flush();

        //logs here

        $responseUpdateUtilities = $suyoolServices->UpdateUtilities(1, $order_id, $transId);
        if ($responseUpdateUtilities[0]) {
            $message = "Simly Purchase was successful";
        }else {
            $message = "Simly Purchase was successful but the utilities were not updated";
        }

        return new JsonResponse([
            'status' => true,
            'message' => $message
        ], 200);

    }

    /**
     * @Route("/simly/getUsageOfEsim", name="app_simly_getUsageOfESIM")
     */
    public function GetUsageOfESIM(Request $request, SimlyServices $simlyServices){
        $esimId = $request->get('esimId');
        if(!$esimId) return new JsonResponse([
            'status' => false,
            'message' => 'esimId is required'
        ], 400);

        $res = $simlyServices->FetchUsageOfPurchasedESIM($esimId);
        return new JsonResponse([
            'status' => true,
            'data' => $res
        ], 200);
    }

}
