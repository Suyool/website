<?php

namespace App\Controller;

use App\Entity\Gift2Games\Order;
use App\Repository\Gift2GamesOrdersRepository;
use App\Service\Gift2GamesService;
use App\Service\SuyoolServices;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class Gift2GamesController extends AbstractController
{
    private $params;
    private $session;

    public function __construct( ParameterBagInterface $params, SessionInterface $sessionInterface)
    {
        $this->params = $params;
        $this->session = $sessionInterface;
    }

    /**
     * @Route("/gift2games", name="app_gift2_games")
     */
    public function index(): Response
    {
        $parameters['deviceType'] = "Android";
        return $this->render('gift2_games/index.html.twig', [
            'parameters' => $parameters
        ]);
//        return $this->render('gift2_games/index.html.twig', [
//            'controller_name' => 'Gift2GamesController',
//        ]);
    }

    /**
     * @Route("/gift2games/categories", name="app_g2g_categories")
     */
    public function getCategories(Gift2GamesService $gamesService)
    {
//        $SuyoolUserId = $this->session->get('suyoolUserId');
        $SuyoolUserId = 155;

        $results = $gamesService->getCategories();

        return new JsonResponse([
            'status' => $results['status'],
            'Payload' => $results['data'],
        ], 200);
    }

    /**
     * @Route("/gift2games/products/{categoryId}", name="app_g2g_products")
     */
    public function getProducts($categoryId, Gift2GamesService $gamesService)
    {
//        $SuyoolUserId = $this->session->get('suyoolUserId');
        $SuyoolUserId = 155;

        $results = $gamesService->getProducts($categoryId);

        return new JsonResponse([
            'status' => $results['status'],
            'Payload' => $results['data'],
        ], 200);
    }

    /**
     * PostPaid
     * Provider : Gift2Games
     * Desc: Retrieve Channel Results
     * @Route("/gift2games/product/pay", name="app_alfa_bill_pay",methods="POST")
     */
    public function pay(Request $request, Gift2GamesService $gamesService, Gift2GamesOrdersRepository $ordersRepository)
    {
        $suyoolServices = new SuyoolServices($this->params->get('GIFT2GAMES_MERCHANT_ID'));
        $data = json_decode($request->getContent(), true);
        $SuyoolUserId = $this->session->get('suyoolUserId');

        if ($data != null) {
            //Initial order with status pending
            $order = new Order;
            $order
                ->setsuyoolUserId($SuyoolUserId)
                ->settransId(null)
                ->setstatus(Order::$statusOrder['PENDING'])
                ->setamount($data['amount'])
                ->setcurrency($data['currency']);
//            $this->mr->persist($order);
//            $this->mr->flush();

            $ordersRepository->insertOrder($order);

            $order_id = $this->params->get('GIFT2GAMES_MERCHANT_ID') . "-" . $order->getId();

            //Take amount from .net
            $response = $suyoolServices->PushUtilities($SuyoolUserId, $order_id, $order->getamount(), $this->params->get('CURRENCY_LBP'),$order->getfees());

            dd($response);

            if ($response[0]) {
                //set order status to held
                $orderupdate1 = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $SuyoolUserId, 'status' => Order::$statusOrder['PENDING']]);
                $orderupdate1
                    ->settransId($response[1])
                    ->setstatus(Order::$statusOrder['HELD']);
                $this->mr->persist($orderupdate1);
                $this->mr->flush();

                //paid postpaid from bob Provider
                $billPay = $bobServices->BillPay($Postpaid_With_id);
                if ($billPay[0] != "") {
                    $billPayArray = json_decode($billPay[0], true);
                    //if payment from loto provider success insert prepaid data to db
                    $postpaid = new Postpaid;
                    $postpaid
                        ->settransactionDescription($billPayArray["TransactionDescription"])
                        ->setstatus(Order::$statusOrder['COMPLETED'])
                        ->setfees($Postpaid_With_id->getfees())
                        ->setfees1($Postpaid_With_id->getfees1())
                        ->setdisplayedFees($Postpaid_With_id->getdisplayedFees())
                        ->setamount($Postpaid_With_id->getamount())
                        ->setamount1($Postpaid_With_id->getamount1())
                        ->setamount2($Postpaid_With_id->getamount2())
                        ->setreferenceNumber($Postpaid_With_id->getreferenceNumber())
                        ->setinformativeOriginalWSamount($Postpaid_With_id->getinformativeOriginalWSamount())
                        ->settotalamount($Postpaid_With_id->gettotalamount())
                        ->setcurrency($Postpaid_With_id->getcurrency())
                        ->setrounding($Postpaid_With_id->getrounding())
                        ->setadditionalfees($Postpaid_With_id->getadditionalfees())
                        ->setPin($Postpaid_With_id->getPin())
                        ->setTransactionId($Postpaid_With_id->getTransactionId())
                        ->setSuyoolUserId($Postpaid_With_id->getSuyoolUserId())
                        ->setGsmNumber($Postpaid_With_id->getGsmNumber());
                    $this->mr->persist($postpaid);
                    $this->mr->flush();

                    $IsSuccess = true;

                    $postpaidId = $postpaid->getId();
                    $postpaid = $this->mr->getRepository(Postpaid::class)->findOneBy(['id' => $postpaidId]);

                    //update order by passing prepaidId to order and set status to purshased
                    $orderupdate = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $SuyoolUserId, 'status' => Order::$statusOrder['HELD']]);
                    $orderupdate
                        ->setpostpaidId($postpaid)
                        ->setstatus(Order::$statusOrder['PURCHASED']);
                    $this->mr->persist($orderupdate);
                    $this->mr->flush();

                    //intial notification
                    $params = json_encode([
                        'amount' => $order->getamount(),
                        'currency' => "L.L",
                        'mobilenumber' => $Postpaid_With_id->getGsmNumber(),
                    ]);
                    $additionalData = "";

                    $content = $notificationServices->getContent('AcceptedAlfaPayment');
                    $bulk = 0; //1 for broadcast 0 for unicast
                    $notificationServices->addNotification($SuyoolUserId, $content, $params, $bulk, $additionalData);

                    $updateUtilitiesAdditionalData = json_encode([
                        'Fees' => $Postpaid_With_id->getfees(),
                        'TransactionId' => $Postpaid_With_id->getTransactionId(),
                        'Amount' => $Postpaid_With_id->getamount(),
                        'TotalAmount' => $Postpaid_With_id->gettotalamount(),
                        'Currency' => $Postpaid_With_id->getcurrency(),
                    ]);

                    //tell the .net that total amount is paid
                    $responseUpdateUtilities = $suyoolServices->UpdateUtilities($order->getamount(),  $updateUtilitiesAdditionalData, $orderupdate->gettransId());
                    if ($responseUpdateUtilities) {
                        $orderupdate5 = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $SuyoolUserId, 'status' => Order::$statusOrder['PURCHASED']]);
                        //update te status from purshased to completed
                        $orderupdate5
                            ->setstatus(Order::$statusOrder['COMPLETED'])
                            ->seterror("SUCCESS");
                        $this->mr->persist($orderupdate5);
                        $this->mr->flush();

                        $dataPayResponse = ['amount' => $order->getamount(), 'currency' => $order->getcurrency(),'fees'=>0];
                        $message = "Success";
                    } else {
                        $orderupdate5 = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $SuyoolUserId, 'status' => Order::$statusOrder['PURCHASED']]);
                        $orderupdate5
                            ->setstatus(Order::$statusOrder['CANCELED'])
                            ->seterror($responseUpdateUtilities[1]);
                        $this->mr->persist($orderupdate5);
                        $this->mr->flush();
                        $message = "something wrong while UpdateUtilities";
                        $dataPayResponse = -1;
                    }
                } else {
                    $IsSuccess = false;
                    $dataPayResponse = -1;
                    //if not purchase return money
                    $responseUpdateUtilities = $suyoolServices->UpdateUtilities(0, "", $orderupdate1->gettransId());
                    if ($responseUpdateUtilities[0]) {
                        $orderupdate4 = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $SuyoolUserId, 'status' => Order::$statusOrder['HELD']]);
                        $orderupdate4
                            ->setstatus(Order::$statusOrder['CANCELED'])
                            ->seterror("reversed error from alfa:" . $billPay[2]);;

                        $this->mr->persist($orderupdate4);
                        $this->mr->flush();

                        $message = "Success return money!!";
                    } else {
                        $orderupdate4 = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $SuyoolUserId, 'status' => Order::$statusOrder['HELD']]);
                        $orderupdate4
                            ->setstatus(Order::$statusOrder['CANCELED'])
                            ->seterror($responseUpdateUtilities[1] . " " . $billPay[2]);
                        $this->mr->persist($orderupdate4);
                        $this->mr->flush();
                        $message = "Can not return money!!";
                    }
                }
            } else {
                //if can not take money from .net cancel the state of the order
                $orderupdate3 = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $SuyoolUserId, 'status' => Order::$statusOrder['PENDING']]);
                $orderupdate3
                    ->setstatus(Order::$statusOrder['CANCELED'])
                    ->seterror($response[1]);
                $this->mr->persist($orderupdate3);
                $this->mr->flush();
                $IsSuccess = false;
                $message = json_decode($response[1], true);
                if (isset($response[2])) {
                    $flagCode = $response[2];
                }
                $dataPayResponse = -1;
            }
        } else {
            $IsSuccess = false;
            $dataPayResponse = -1;
            $message = "Can not retrive data !!";
        }

        return new JsonResponse([
            'status' => true,
            'message' => $message,
            'IsSuccess' => $IsSuccess,
            'flagCode' => $flagCode,
            'data' => $dataPayResponse,
        ], 200);

    }


}
