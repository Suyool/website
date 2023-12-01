<?php

namespace App\Controller;

use App\Entity\Sodetel\Order;
use App\Entity\Sodetel\Product;
use App\Repository\SodetelOrdersRepository;
use App\Repository\SodetelProductsRepository;
use App\Service\NotificationServices;
use App\Service\SodetelService;
use App\Service\SuyoolServices;
use Doctrine\Persistence\ManagerRegistry;
use EasyCorp\Bundle\EasyAdminBundle\Dto\DashboardDto;
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

    public function __construct(ManagerRegistry $mr, $certificate, $hash_algo, ParameterBagInterface $params, SessionInterface $sessionInterface)
    {
        $this->mr = $mr->getManager('sodetel');
        $this->params = $params;
        $this->session = $sessionInterface;
    }

    /**
     * @Route("/sodetel", name="sodetel")
     */
    public function index(NotificationServices $notificationServices): Response
    {
        $useragent = $_SERVER['HTTP_USER_AGENT'];
        $_POST['infoString'] = "3mzsXlDm5DFUnNVXA5Pu8T1d5nNACEsiiUEAo7TteE/x3BGT3Oy3yCcjUHjAVYk3";


        if (isset($_POST['infoString'])) {
            $decrypted_string = SuyoolServices::decrypt($_POST['infoString']);
            $suyoolUserInfo = explode("!#!", $decrypted_string);
            $devicetype = stripos($useragent, $suyoolUserInfo[1]);
//            $devicetype = "Android";
            if ($notificationServices->checkUser($suyoolUserInfo[0], $suyoolUserInfo[2]) && $devicetype) {
//                $SuyoolUserId = $suyoolUserInfo[0];
            $SuyoolUserId = 218;
            $this->session->set('suyoolUserId', $SuyoolUserId);
            // $this->session->set('suyoolUserId', 155);

            $parameters['deviceType'] = $suyoolUserInfo[1];


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
//            "bundle": "bundle",
//             "identifier": "96170000000",
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


        $suyoolServices = new SuyoolServices($this->params->get('SODETEL_POSTPAID_MERCHANT_ID'));
        $data = json_decode($request->getContent(), true);

        $SuyoolUserId = $this->session->get('suyoolUserId');
        $SuyoolUserId = 218;


        $flagCode = null;
        $IsSuccess = false;
        $dataPayResponse = [];
        $status = 200;
        $message = "";

        if ($data != null) {
            $order = new Order;
            $order->setSuyoolUserId($SuyoolUserId)
                ->setAmount($data['refillData']['pricettc'])
                ->setCurrency($data['refillData']['currency'])
                ->setTransId(null)
                ->setStatus(Order::$statusOrder['PENDING'])
                ->setProduct(null);

            $this->mr->persist($order);
            $this->mr->flush();

            $order_id = $this->params->get('SODETEL_POSTPAID_MERCHANT_ID') . $order->getId();

            $utilityResponse = $suyoolServices->PushUtilities($SuyoolUserId, $order_id, $order->getAmount(), $order->getCurrency(), 0);
            if ($utilityResponse[0]) {
                $order->setStatus(Order::$statusOrder['HELD']);

                $this->mr->persist($order);
                $this->mr->flush();

                $rechargeInfo = $sodetelService->refill($data['bundle'], $data['refillData']['plancode'], $data['identifier'], $order->getId());
                if ($rechargeInfo) {
                    $sodetelArr = json_decode($rechargeInfo, true);
                    $sodetelData = $sodetelArr[0];
//                    dd($sodetelData);
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
//                            'currency' => $order->getCurrency(),
                            // number to be calculated later

                            'userAccount' => $data['identifier'],
                            'type' => "$data[bundle]"
                        ]);

                        $additionalData = '';

                        $content = $notificationServices->getContent('AcceptedAlfaPayment');
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

                    } else {
                        $order->setStatus(Order::$statusOrder['CANCELED'])
                            ->setError($sodetelData['message']);
                        $this->mr->persist($order);
                        $this->mr->flush();

                        $IsSuccess = false;
                        $message = $sodetelData['message'];
                        $dataPayResponse = -1;
                    }
                }
            } else {
//                dd($utilityResponse);
                $order->setstatus(Order::$statusOrder['CANCELED'])
                    ->seterror($utilityResponse[1]);
                $this->mr->persist($order);
                $this->mr->flush();

                $message = $utilityResponse[1];
                $flagCode = $utilityResponse[2];

                $status = 200;
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
}
