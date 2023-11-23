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
    public function index(): Response
    {
        $parameters['deviceType'] = "Android";
        return $this->render('sodetel/index.html.twig', [
            'controller_name' => 'SodetelController',
            'parameters' => $parameters,
        ]);
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
//        $SuyoolUserId = $this->session->get('suyoolUserId');
        $SuyoolUserId = 218;
        $flagCode = null;

        if ($data != null) {
//            dd($this->mr->getRepository(Order::class)->findAll());
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
            $message = "";
            if ($utilityResponse[0]) {
                $order->setStatus(Order::$statusOrder['HELD']);

                $this->mr->persist($order);
                $this->mr->flush();

//                dd($order->getStatus());

                $rechargeInfo = $sodetelService->refill($data['bundle'], $data['refillData']['plancode'], $data['identifier'], $order->getId());
//                dd($rechargeInfo);

                if ($rechargeInfo[0]) {
//                if ($rechargeInfo[0]  ) {
//                    dd("rechargeInfo", $rechargeInfo);
                    $product = new Product;

//                    dd($data);

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
                        'currency' => $order->getCurrency(),
                        // number to be calculated later
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
                    if ($responseUpdateUtilities) {
//                        $ordersRepository->updateOrderStatus($order->getId(), $SuyoolUserId, Order::$statusOrder['PURCHASED'], Order::$statusOrder['COMPLETED']);
                        //setError: SUCCESS
                        $order->setStatus(Order::$statusOrder['COMPLETED'])
                            ->setError("SUCCESS");
                        $this->mr->persist($order);
                        $this->mr->flush();

                        $dataPayResponse = ['amount' => $order->getAmount(), 'currency' => $order->getCurrency(), 'fees' => 0];
                        $message = "Success";
                        $IsSuccess = true;
                    } else {
//                        $ordersRepository->updateOrderStatus($order->getId(), $SuyoolUserId, Order::$statusOrder['PURCHASED'], Order::$statusOrder['CANCELED']);
//                                ->seterror($responseUpdateUtilities[1]);
                        $order->setStatus(Order::$statusOrder['CANCELED'])
                            ->setError($responseUpdateUtilities[1]);

                        $message = "something wrong while UpdateUtilities";
                        $dataPayResponse = -1;
                    }
                } else {
//                    $ordersRepository->updateOrderStatus($order->getId(), $SuyoolUserId, Order::$statusOrder['PENDING'], Order::$statusOrder['CANCELED']);
//                            ->seterror($response[1]);

                    $order->setStatus(Order::$statusOrder['CANCELED'])
                        ->setError($rechargeInfo[1]);
                    $this->mr->persist($order);
                    $this->mr->flush();

                    $IsSuccess = false;
                    $message = json_decode($utilityResponse[1], true);
                    if (isset($utilityResponse[2])) {
                        $flagCode = $utilityResponse[2];
                    }
                    $dataPayResponse = -1;
                }
            } else {
                return new JsonResponse([
                    'status' => false,
                    'message' => $utilityResponse[1],
                    'IsSuccess' => false,
                    'flagCode' => $utilityResponse[2],
                    'data' => '',
                ], 200);
            }

            return new JsonResponse([
                'status' => true,
                'message' => $message,
                'IsSuccess' => $IsSuccess,
                'flagCode' => $flagCode,
                'data' => $dataPayResponse,
            ], 200);

            dd($utilityResponse);


        }
    }
}
