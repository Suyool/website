<?php

namespace App\Controller;

use App\Entity\Notification\Users;
use App\Entity\Sodetel\Logs;
use App\Entity\Sodetel\Order;
use App\Entity\Sodetel\Product;
use App\Entity\Sodetel\SodetelRequest;
use App\Service\NotificationServices;
use App\Service\SodetelService;
use App\Service\SuyoolServices;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class SodetelController extends AbstractController
{

    private $mr;
    private $params;
    private $session;
    private $not;
    private $loggerInterface;


    public function __construct(ManagerRegistry $mr, $certificate, $hash_algo, ParameterBagInterface $params, SessionInterface $sessionInterface, LoggerInterface $loggerInterface)
    {
        $this->mr = $mr->getManager('sodetel');
        $this->not = $mr->getManager('notification');
        $this->params = $params;
        $this->session = $sessionInterface;
        $this->loggerInterface = $loggerInterface;
    }

    /**
     * @Route("/sodetel", name="sodetel")
     */
    public function index(NotificationServices $notificationServices): Response
    {
        $useragent = $_SERVER['HTTP_USER_AGENT'];
        //        $_POST['infoString'] = "3mzsXlDm5DFUnNVXA5Pu8T1d5nNACEsiiUEAo7TteE/x3BGT3Oy3yCcjUHjAVYk3";
        //        $_POST['infoString'] = "fDw1fGSFl9P1u6pVDvVFTJAuMCD8nnbrdOm3klT/EuBs+IueXRHFPorgUh30SnQ+";

        if (isset($_POST['infoString'])) {
            $decrypted_string = SuyoolServices::decrypt($_POST['infoString']); //['device'=>"aad", asdfsd]
            $suyoolUserInfo = explode("!#!", $decrypted_string);
            $checkIfCorporate = $suyoolUserInfo[1];
            $devicetype = stripos($useragent, $suyoolUserInfo[1]);
            //            $devicetype = "Android";

            if ($notificationServices->checkUser($suyoolUserInfo[0], $suyoolUserInfo[2]) && ($devicetype || $checkIfCorporate == "CORPORATE")) {
                $SuyoolUserId = $suyoolUserInfo[0];
                $this->session->set('suyoolUserId', $SuyoolUserId);

                $parameters['deviceType'] = $suyoolUserInfo[1];

                $parameters['suyoolUserId'] = $_POST['infoString'];

                return $this->render('sodetel/index.html.twig', [
                    'controller_name' => 'SodetelController',
                    'parameters' => $parameters,
                ]);
            } else {
                return $this->render('ExceptionHandling.html.twig');
            }
        } else {
            return $this->render('ExceptionHandling.html.twig');
        }
    }

    /**
     * Provider : Sodetel
     * Desc: Retrieve Sodetel Results
     * @Route("/sodetel/bundles", name="app_sodetel_bundles ",methods="POST")
     */
    public function getCards(Request $request, SodetelService $sodetelService)
    {
        $parameters = json_decode($request->getContent(), true);
        $service = $parameters['service'];
        $identifier = $parameters['identifier'];
        $cards = $sodetelService->getAvailableCards($service, $identifier);

        if (isset($cards[0])) {
            $logs = new Logs;
            $logs
                ->setidentifier("Sodetel Request")
                ->seturl("https://ws.sodetel.net.lb/getavailablecards.php")
                ->setrequest(json_encode(array($service, $identifier)))
                ->setresponse(null)
                ->seterror(json_encode($cards[1]));
            $this->mr->persist($logs);
            $this->mr->flush();
        }
        if (isset($cards['status']) && $cards['status']) {
            $response = new Response();
            $arr[0] = true;
            $arr[1] = json_encode($cards['data']);

            $request = new SodetelRequest;
            $request
                ->setIdentifier($identifier)
                ->setServices($arr[1]);

            $this->mr->persist($request);
            $this->mr->flush();

            $arr[2] = $request->getId();

            $response->setContent(json_encode($arr));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

        $logs = new Logs;
        $logs
            ->setidentifier("Sodetel Request")
            ->seturl("https://ws.sodetel.net.lb/getavailablecards.php")
            ->setrequest(json_encode(array($service, $identifier)))
            ->setresponse(json_encode($cards))
            ->seterror(null);

        $response = new Response();
        $response->setContent(json_encode($cards));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * Provider : Sodetel
     * Desc: Retrieve Sodetel Results
     * @Route("/sodetel/refill", name="app_sodetel_refill ",methods="POST")
     */
    public function refill(Request $request, SodetelService $sodetelService, NotificationServices $notificationServices)
    {
        //        request: {
        //            "bundle": "dsl",
        //             "identifier": "96170000000",
        //             "requestId": 1,
        //             "refillData": {
        //                "plancode": "vs1",
        //                "plandescription": "Fiber extra 12GB",
        //                "pricettc": 233100,
        //                "priceht": 210000,
        //                "price": 233100,
        //                "currency": "LBP",
        //                "sayrafa": 85500
        //             }
        //        }
        $data = json_decode($request->getContent(), true);
        if (isset($data["suyoolUserId"])) {
            $webkey = SuyoolServices::decrypt($data["suyoolUserId"]);
            $suyoolUserInfo = explode("!#!", $webkey);
            $SuyoolUserId = $suyoolUserInfo[0];
        } else {
            $SuyoolUserId = $this->session->get('suyoolUserId');
        }

        $flagCode = null;
        $IsSuccess = false;
        $dataPayResponse = [];
        $status = 200;
        $message = "";
        $suyooler = $this->not->getRepository(Users::class)->findOneBy(['suyoolUserId' => $SuyoolUserId]);


        if ($data != null && isset($data['requestId'])) {
            $sodetelMerchantId = $data['bundle'] == "4g" ? $this->params->get('SODETEL_4G_MERCHANT_ID') : $this->params->get('SODETEL_POSTPAID_MERCHANT_ID');
            $suyoolServices = new SuyoolServices($sodetelMerchantId);

            $requestId = $data['requestId'];

            $sodetelRequest = $this->mr->getRepository(SodetelRequest::class)->find($requestId);
            if ($sodetelRequest) {
                $services = json_decode($sodetelRequest->getServices(), true);

                $matchingObject = null;

                foreach ($services as $key => $value) {
                    if (is_array($value) && isset($value['plancode']) && $value['plancode'] === $data['refillData']['plancode']) {
                        $matchingObject = $value;
                        break;
                    }
                }

                $order = new Order;
                $order->setSuyoolUserId($SuyoolUserId)
                    ->setUtilityMerchantId($sodetelMerchantId)
                    ->setAmount($matchingObject['pricettc'])
                    ->setCurrency($matchingObject['currency'])
                    ->setTransId(null)
                    ->setStatus(Order::$statusOrder['PENDING'])
                    ->setIdentifier($data['identifier'])
                    ->setProduct(null)
                    ->setRequestId($data['requestId']);

                $this->mr->persist($order);
                $this->mr->flush();

                $order_id = $sodetelMerchantId . "-" . $order->getId();

                $utilityResponse = $suyoolServices->PushUtilities($SuyoolUserId, $order_id, $order->getAmount(), $order->getCurrency(), 0);

                if ($utilityResponse[0]) {
                    $order->setStatus(Order::$statusOrder['HELD']);

                    $this->mr->persist($order);
                    $this->mr->flush();

                    $transId = $utilityResponse[1];

                    $order->setTransId($transId);
                    $this->mr->persist($order);
                    $this->mr->flush();

                    $rechargeInfo = $sodetelService->refill($data['bundle'], $data['refillData']['plancode'], $data['identifier'], $order->getId());
                    if ($rechargeInfo) {
                        $sodetelArr = json_decode($rechargeInfo, true);
                        $sodetelData = $sodetelArr[0];
                        if ($sodetelData['result']) {
                            $product = new Product;
                            $product
                                ->setType($data['bundle'])
                                ->setPlanCode($data['refillData']['plancode'])
                                ->setPlanDescription($data['refillData']['plandescription'])
                                ->setPricettc($data['refillData']['pricettc'])
                                ->setPriceHt($data['refillData']['priceht'])
                                ->setPrice($data['refillData']['price'])
                                ->setCurrency($data['refillData']['currency'])
                                ->setSayrafa($data['refillData']['sayrafa']);

                            $this->mr->persist($product);
                            $this->mr->flush();

                            $order->setStatus(Order::$statusOrder['PURCHASED'])
                                ->setProduct($product)
                                ->setTransId($utilityResponse[1]);

                            $this->mr->persist($order);
                            $this->mr->flush();

                            //notification body
                            $params = json_encode([
                                'amount' => $order->getAmount(),
                                'currency' => 'L.L',
                                'username' => $data['identifier'],
                                'type' => $data['bundle']
                            ]);

                            $additionalData = '';

                            $notificationType = $data['bundle'] == "4g" ? 'AcceptedSodetel4GPayment' : 'AcceptedSodetelDSLPayment';

                            $content = $notificationServices->getContent($notificationType);
                            $bulk = 0; //1 for broadcast 0 for unicast
                            $notificationServices->addNotification($SuyoolUserId, $content, $params, $bulk, $additionalData);

                            $updateUtilitiesAdditionalData = json_encode([
                                'Fees' => 0,
                                'TransactionId' => $product->getId(),
                                'Amount' => $order->getAmount(),
                                'TotalAmount' => $order->getAmount(),
                                'Currency' => $order->getCurrency(),
                            ]);

                            //tell the .net that total amount is paid
                            $responseUpdateUtilities = $suyoolServices->UpdateUtilities($order->getAmount(), $updateUtilitiesAdditionalData, $order->gettransId());
                            if ($responseUpdateUtilities[0]) {
                                $order->setStatus(Order::$statusOrder['COMPLETED'])
                                    ->setError("SUCCESS");
                                $this->mr->persist($order);
                                $this->mr->flush();

                                $dataPayResponse = ['amount' => $order->getAmount(), 'currency' => $order->getCurrency(), 'fees' => 0, 'id' => $sodetelData['id'], 'password' => $sodetelData['password']];
                                $message = "Success";
                                $IsSuccess = true;
                            } else {
                                $order->setStatus(Order::$statusOrder['CANCELED'])
                                    ->setError($responseUpdateUtilities[1]);

                                $message = "something wrong while UpdateUtilities";
                                $dataPayResponse = -1;
                            }

                            $logs = new Logs;
                            $logs
                                ->setidentifier("Sodetel Request")
                                ->seturl("https://ws.sodetel.net.lb/getavailablecards.php")
                                ->setrequest(json_encode(array($data['bundle'], $data['refillData']['plancode'], $data['identifier'], $order->getId())))
                                ->setresponse(json_encode($sodetelData))
                                ->seterror(null);
                        } else {
                            $logs = new Logs;
                            $logs
                                ->setidentifier("Sodetel Request")
                                ->seturl("https://ws.sodetel.net.lb/getavailablecards.php")
                                ->setrequest(json_encode(array($data['bundle'], $data['refillData']['plancode'], $data['identifier'], $order->getId())))
                                ->setresponse(json_encode($sodetelData))
                                ->seterror($sodetelData['message']);
                            $this->mr->persist($logs);
                            $this->mr->flush();

                            //return the money to the user
                            $responseUpdateUtilities = $suyoolServices->UpdateUtilities(0, "", $transId);
                            if ($responseUpdateUtilities[0]) {
                                $order->setStatus(Order::$statusOrder['CANCELED'])
                                    ->setError($sodetelData['message']);

                                $message = "Money returned to the user";
                            } else if (isset($responseUpdateUtilities[1])) {
                                $order->setStatus(Order::$statusOrder['CANCELED'])
                                    ->setError($responseUpdateUtilities[1]);

                                $message = "something wrong while UpdateUtilities";
                            }

                            $this->mr->persist($order);
                            $this->mr->flush();

                            $IsSuccess = false;
                            $dataPayResponse = -1;
                        }
                    } else {
                        $responseUpdateUtilities = $suyoolServices->UpdateUtilities(0, "", $transId);
                        if ($responseUpdateUtilities[0]) {
                            $order->setStatus(Order::$statusOrder['CANCELED'])
                                ->setError("something wrong while refill");

                            $message = "Money returned to the user";
                        } else if (isset($responseUpdateUtilities[1])) {
                            $order->setStatus(Order::$statusOrder['CANCELED'])
                                ->setError($responseUpdateUtilities[1]);

                            $message = "something wrong while UpdateUtilities";
                        }
                        $this->mr->persist($order);
                        $this->mr->flush();

                        $IsSuccess = false;
                        $dataPayResponse = -1;
                    }
                } else {
                    $order->setstatus(Order::$statusOrder['CANCELED'])
                        ->seterror($utilityResponse[1]);
                    $this->mr->persist($order);
                    $this->mr->flush();

                    $logs = new Logs;
                    $logs
                        ->setidentifier("Sodetel Request")
                        ->seturl("Utilities/PushUtilityPayment")
                        ->setrequest(json_encode(array($SuyoolUserId, $order_id, $order->getAmount(), $order->getCurrency(), 0)))
                        ->setresponse(null)
                        ->seterror(json_encode($utilityResponse));

                    $this->mr->persist($logs);
                    $this->mr->flush();

                    $message = $utilityResponse[1];
                    $flagCode = $utilityResponse[2];

                    $status = 200;
                }
            } else {
                $message = "Request not found";
                $flagCode = "";
                $status = 400;
            }
        } else {
            $message = "bad request";
            $flagCode = "";
            $status = 400;
        }
        return new JsonResponse([
            'status' => true,
            'message' => $message,
            'IsSuccess' => $IsSuccess,
            'flagCode' => $flagCode,
            'data' => $dataPayResponse,
        ], $status);
    }

    /**
     * @Route("api/sodetel", name="ap1_sodetel",methods="GET")
     */
    public function checkWebkey(NotificationServices $notificationServices)
    {
        $webkey = apache_request_headers();
        $webkey = $webkey['Authorization'];
        $webkeyDecrypted = SuyoolServices::decryptWebKey($webkey);

        if ($notificationServices->checkUser($webkeyDecrypted['merchantId'], $webkeyDecrypted['lang']) &&  $webkeyDecrypted['devicesType'] == "CORPORATE") {
            return new JsonResponse([
                'status' => true
            ]);
        } else {
            return new JsonResponse([
                'status' => false
            ]);
        }
    }

    /**
     * Provider : Sodetel
     * Desc: Retrieve Sodetel Results
     * @Route("api/sodetel/bundles", name="ap2_sodetel_bundles ",methods="POST")
     */
    public function getCardsApi(Request $request, NotificationServices $notificationServices, SodetelService $sodetelService)
    {
        $webkey = apache_request_headers();
        $webkey = $webkey['Authorization'];
        $webkeyDecrypted = SuyoolServices::decryptWebKey($webkey);

        if ($notificationServices->checkUser($webkeyDecrypted['merchantId'], $webkeyDecrypted['lang']) &&  $webkeyDecrypted['devicesType'] == "CORPORATE") {
            $parameters = json_decode($request->getContent(), true);
            $service = $parameters['service'];
            $identifier = $parameters['identifier'];
            $cards = $sodetelService->getAvailableCards($service, $identifier);
            // dd($cards);
            foreach ($cards['data'] as $key => $cardsToPushImage) {
                if ($key == "customerid") {
                    continue;
                }
                $cards['data'][$key][] = [
                    'image' => (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . "/build/images/sodetel/{$cardsToPushImage['plancode']}.svg",
                    'icon' => $item['icon'] = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . "/build/images/sodetel/{$cardsToPushImage['plancode']}-cir.svg"
                ];
                $cardResponseJson[] = [
                    'plancode' => $cards['data'][$key]['plancode'],
                    'plandescription' => $cards['data'][$key]['plandescription'],
                    'pricettc' => $cards['data'][$key]['pricettc'],
                    'priceht' => $cards['data'][$key]['priceht'],
                    'price' => $cards['data'][$key]['price'],
                    'currency' => $cards['data'][$key]['currency'],
                    'sayrafa' => $cards['data'][$key]['sayrafa'],
                    'image' => $cards['data'][$key][0]['image'],
                    'icon' => $cards['data'][$key][0]['icon'],
                    'customerid' => $cards['data']['customerid'],
                ];
            }

            if (isset($cards[0])) {
                $logs = new Logs;
                $logs
                    ->setidentifier("Sodetel Request")
                    ->seturl("https://ws.sodetel.net.lb/getavailablecards.php")
                    ->setrequest(json_encode(array($service, $identifier)))
                    ->setresponse(null)
                    ->seterror(json_encode($cards[1]));
                $this->mr->persist($logs);
                $this->mr->flush();
            }
            if (isset($cards['status']) && $cards['status']) {
                $response = new Response();
                $arr[0] = true;
                $arr[1] = json_encode($cards['data']);

                $request = new SodetelRequest;
                $request
                    ->setIdentifier($identifier)
                    ->setServices($arr[1]);

                $this->mr->persist($request);
                $this->mr->flush();

                $arr[2] = $request->getId();

                $logs = new Logs;
                $logs
                    ->setidentifier("Sodetel Request")
                    ->seturl("https://ws.sodetel.net.lb/getavailablecards.php")
                    ->setrequest(json_encode(array($service, $identifier)))
                    ->setresponse(json_encode($cards))
                    ->seterror(null);

                $this->mr->persist($logs);
                $this->mr->flush();

                return new JsonResponse([
                    'status' => true,
                    'message'=>"Success retrieve data",
                    'data' => ['display'=>$cardResponseJson, 'requestId' => $arr[2]],
                ], 200);
            }

            $logs = new Logs;
                $logs
                    ->setidentifier("Sodetel Request")
                    ->seturl("https://ws.sodetel.net.lb/getavailablecards.php")
                    ->setrequest(json_encode(array($service, $identifier)))
                    ->setresponse(json_encode($cards))
                    ->seterror(null);

                $this->mr->persist($logs);
                $this->mr->flush();

            return new JsonResponse([
                'status' => true,
                'data' => $cards
            ]);
        } else {
            return new JsonResponse([
                'status' => false,
                'message' => "Unauthorize"
            ], 401);
        }
    }

    /**
     * Provider : Sodetel
     * Desc: Retrieve Sodetel Results
     * @Route("api/sodetel/refill", name="ap3_sodetel_refill ",methods="POST")
     */
    public function refillApi(Request $request, SodetelService $sodetelService, NotificationServices $notificationServices)
    {
        //        request: {
        //            "bundle": "dsl",
        //             "identifier": "96170000000",
        //             "requestId": 1,
        //             "refillData": {
        //                "plancode": "vs1",
        //                "plandescription": "Fiber extra 12GB",
        //                "pricettc": 233100,
        //                "priceht": 210000,
        //                "price": 233100,
        //                "currency": "LBP",
        //                "sayrafa": 85500
        //             }
        //        }
        $webkey = apache_request_headers();
        $webkey = $webkey['Authorization'];
        $webkeyDecrypted = SuyoolServices::decryptWebKey($webkey);

        if ($notificationServices->checkUser($webkeyDecrypted['merchantId'], $webkeyDecrypted['lang']) &&  $webkeyDecrypted['devicesType'] == "CORPORATE") {
            $data = json_decode($request->getContent(), true);
            $SuyoolUserId = $webkeyDecrypted['merchantId'];

            $flagCode = null;
            $IsSuccess = false;
            $dataPayResponse = [];
            $status = 200;
            $message = "";
            $suyooler = $this->not->getRepository(Users::class)->findOneBy(['suyoolUserId' => $SuyoolUserId]);


            if ($data != null && isset($data['requestId'])) {
                $sodetelMerchantId = $data['bundle'] == "4g" ? $this->params->get('SODETEL_4G_MERCHANT_ID') : $this->params->get('SODETEL_POSTPAID_MERCHANT_ID');
                $suyoolServices = new SuyoolServices($sodetelMerchantId, null, null, null, $this->loggerInterface);

                $requestId = $data['requestId'];

                $sodetelRequest = $this->mr->getRepository(SodetelRequest::class)->find($requestId);
                if ($sodetelRequest) {
                    $services = json_decode($sodetelRequest->getServices(), true);

                    $matchingObject = null;

                    foreach ($services as $key => $value) {
                        if (is_array($value) && isset($value['plancode']) && $value['plancode'] === $data['plancode']) {
                            $matchingObject = $value;
                            break;
                        }
                    }

                    $order = new Order;
                    $order->setSuyoolUserId($SuyoolUserId)
                        ->setUtilityMerchantId($sodetelMerchantId)
                        ->setAmount($matchingObject['pricettc'])
                        ->setCurrency($matchingObject['currency'])
                        ->setTransId(null)
                        ->setStatus(Order::$statusOrder['PENDING'])
                        ->setIdentifier($data['identifier'])
                        ->setProduct(null)
                        ->setRequestId($data['requestId']);

                    $this->mr->persist($order);
                    $this->mr->flush();

                    $order_id = $sodetelMerchantId . "-" . $order->getId();

                    $utilityResponse = $suyoolServices->PushUtilities($SuyoolUserId, $order_id, $order->getAmount(), $order->getCurrency(), 0);

                    if ($utilityResponse[0]) {
                        $order->setStatus(Order::$statusOrder['HELD']);

                        $this->mr->persist($order);
                        $this->mr->flush();

                        $transId = $utilityResponse[1];

                        $order->setTransId($transId);
                        $this->mr->persist($order);
                        $this->mr->flush();

                        $rechargeInfo = $sodetelService->refill($data['bundle'], $matchingObject['plancode'], $data['identifier'], $order->getId());
                        if ($rechargeInfo) {
                            $sodetelArr = json_decode($rechargeInfo, true);
                            $sodetelData = $sodetelArr[0];
                            if ($sodetelData['result']) {
                                $product = new Product;
                                $product
                                    ->setType($data['bundle'])
                                    ->setPlanCode($matchingObject['plancode'])
                                    ->setPlanDescription($matchingObject['plandescription'])
                                    ->setPricettc($matchingObject['pricettc'])
                                    ->setPriceHt($matchingObject['priceht'])
                                    ->setPrice($matchingObject['price'])
                                    ->setCurrency($matchingObject['currency'])
                                    ->setSayrafa($matchingObject['sayrafa']);

                                $this->mr->persist($product);
                                $this->mr->flush();

                                $order->setStatus(Order::$statusOrder['PURCHASED'])
                                    ->setProduct($product)
                                    ->setTransId($utilityResponse[1]);

                                $this->mr->persist($order);
                                $this->mr->flush();

                                //notification body
                                $params = json_encode([
                                    'amount' => $order->getAmount(),
                                    'currency' => 'L.L',
                                    'username' => $data['identifier'],
                                    'type' => $data['bundle']
                                ]);

                                $additionalData = '';

                                // $notificationType = $data['bundle'] == "4g" ? 'AcceptedSodetel4GPayment' : 'AcceptedSodetelDSLPayment';


                                if ($suyooler->getType() == 2) {
                                    $notificationType = $data['bundle'] == "4g" ? 'AcceptedSodetel4GPayment' : 'AcceptedSodetelDSLPayment';
                                    $content = $notificationServices->getContent($notificationType);
                                    $bulk = 0; //1 for broadcast 0 for unicast
                                    $notificationServices->addNotification($SuyoolUserId, $content, $params, $bulk, $additionalData);
                                } else {
                                    $notificationType = $data['bundle'] == "4g" ? 'AcceptedSodetel4GPayment' : 'AcceptedSodetelDSLPaymentCorporate';
                                    $content = $notificationServices->getContent($notificationType);
                                    $bulk = 1; //1 for broadcast 0 for unicast
                                    $notificationServices->addNotification($data["getUsersToReceiveNotification"], $content, $params, $bulk, $additionalData);
                                }

                                $content = $notificationServices->getContent($notificationType);
                                $bulk = 0; //1 for broadcast 0 for unicast
                                $notificationServices->addNotification($SuyoolUserId, $content, $params, $bulk, $additionalData);

                                $updateUtilitiesAdditionalData = json_encode([
                                    'Fees' => 0,
                                    'TransactionId' => $product->getId(),
                                    'Amount' => $order->getAmount(),
                                    'TotalAmount' => $order->getAmount(),
                                    'Currency' => $order->getCurrency(),
                                ]);

                                //tell the .net that total amount is paid
                                $responseUpdateUtilities = $suyoolServices->UpdateUtilities($order->getAmount(), $updateUtilitiesAdditionalData, $order->gettransId());
                                if ($responseUpdateUtilities[0]) {
                                    $order->setStatus(Order::$statusOrder['COMPLETED'])
                                        ->setError("SUCCESS");
                                    $this->mr->persist($order);
                                    $this->mr->flush();

                                    $dataPayResponse = ['amount' => $order->getAmount(), 'currency' => $order->getCurrency(), 'fees' => 0, 'id' => $sodetelData['id'], 'password' => $sodetelData['password']];
                                    $message = "Success";
                                    $IsSuccess = true;
                                } else {
                                    $order->setStatus(Order::$statusOrder['CANCELED'])
                                        ->setError($responseUpdateUtilities[1]);

                                    $message = "something wrong while UpdateUtilities";
                                    $dataPayResponse = -1;
                                }

                                $logs = new Logs;
                                $logs
                                    ->setidentifier("Sodetel Request")
                                    ->seturl("https://ws.sodetel.net.lb/getavailablecards.php")
                                    ->setrequest(json_encode(array($data['bundle'], $matchingObject['plancode'], $data['identifier'], $order->getId())))
                                    ->setresponse(json_encode($sodetelData))
                                    ->seterror(null);
                            } else {
                                $logs = new Logs;
                                $logs
                                    ->setidentifier("Sodetel Request")
                                    ->seturl("https://ws.sodetel.net.lb/getavailablecards.php")
                                    ->setrequest(json_encode(array($data['bundle'], $matchingObject['plancode'], $data['identifier'], $order->getId())))
                                    ->setresponse(json_encode($sodetelData))
                                    ->seterror($sodetelData['message']);
                                $this->mr->persist($logs);
                                $this->mr->flush();

                                //return the money to the user
                                $responseUpdateUtilities = $suyoolServices->UpdateUtilities(0, "", $transId);
                                if ($responseUpdateUtilities[0]) {
                                    $order->setStatus(Order::$statusOrder['CANCELED'])
                                        ->setError($sodetelData['message']);

                                    $message = "Money returned to the user";
                                } else if (isset($responseUpdateUtilities[1])) {
                                    $order->setStatus(Order::$statusOrder['CANCELED'])
                                        ->setError($responseUpdateUtilities[1]);

                                    $message = "something wrong while UpdateUtilities";
                                }

                                $this->mr->persist($order);
                                $this->mr->flush();

                                $IsSuccess = false;
                                $dataPayResponse = -1;
                            }
                        } else {
                            $responseUpdateUtilities = $suyoolServices->UpdateUtilities(0, "", $transId);
                            if ($responseUpdateUtilities[0]) {
                                $order->setStatus(Order::$statusOrder['CANCELED'])
                                    ->setError("something wrong while refill");

                                $message = "Money returned to the user";
                            } else if (isset($responseUpdateUtilities[1])) {
                                $order->setStatus(Order::$statusOrder['CANCELED'])
                                    ->setError($responseUpdateUtilities[1]);

                                $message = "something wrong while UpdateUtilities";
                            }
                            $this->mr->persist($order);
                            $this->mr->flush();

                            $IsSuccess = false;
                            $dataPayResponse = -1;
                        }
                    } else {
                        $order->setstatus(Order::$statusOrder['CANCELED'])
                            ->seterror($utilityResponse[1]);
                        $this->mr->persist($order);
                        $this->mr->flush();

                        $logs = new Logs;
                        $logs
                            ->setidentifier("Sodetel Request")
                            ->seturl("Utilities/PushUtilityPayment")
                            ->setrequest(json_encode(array($SuyoolUserId, $order_id, $order->getAmount(), $order->getCurrency(), 0)))
                            ->setresponse(null)
                            ->seterror(json_encode($utilityResponse));

                        $this->mr->persist($logs);
                        $this->mr->flush();

                        $message = $utilityResponse[1];
                        $flagCode = $utilityResponse[2];

                        $status = 200;
                    }
                } else {
                    $message = "Request not found";
                    $flagCode = "";
                    $status = 400;
                }
            } else {
                $message = "bad request";
                $flagCode = "";
                $status = 400;
            }
            $parameters = [
                'message'=>$message,
                'IsSuccess' => $IsSuccess,
                'flagCode' => $flagCode,
                'data' => $dataPayResponse,
            ];
            return new JsonResponse([
                'status' => true,
                'message' => "Success",
                'data' => $parameters,
            ], $status);
        }else {
            return new JsonResponse([
                'status' => false,
                'message' => "Unauthorize"
            ],401);
        }
    }
}
