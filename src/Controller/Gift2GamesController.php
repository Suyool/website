<?php

namespace App\Controller;

use App\Entity\Gift2Games\Order;
use App\Entity\Gift2Games\Product;
use App\Repository\Gift2GamesOrdersRepository;
use App\Service\Gift2GamesService;
use App\Service\NotificationServices;
use App\Service\SuyoolServices;
use Doctrine\Persistence\ManagerRegistry;
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
    private $suyoolServices;
    private $mr;
    private $gamesService;
    private $notificationServices;

    public function __construct( ParameterBagInterface $params, SessionInterface $sessionInterface,ManagerRegistry $mr,Gift2GamesService $gamesService, NotificationServices $notificationServices)
    {
        $this->params = $params;
        $this->session = $sessionInterface;
        $this->mr = $mr->getManager('gift2games');
        $this->suyoolServices = new SuyoolServices($params->get('GIFT2GAMES_MERCHANT_ID'));
        $this->gamesService = $gamesService;
        $this->notificationServices = $notificationServices;
    }

    /**
     * @Route("/gift2games", name="app_gift2_games")
     */
    public function index(): Response
    {
//        $parameters['deviceType'] = "Android";
//        return $this->render('gift2_games/index.html.twig', [
//            'parameters' => $parameters
//        ]);
        $useragent = $_SERVER['HTTP_USER_AGENT'];

        $_POST['infoString'] = "3mzsXlDm5DFUnNVXA5Pu8T1d5nNACEsiiUEAo7TteE/x3BGT3Oy3yCcjUHjAVYk3";

        if (isset($_POST['infoString'])) {
            $decrypted_string = $this->suyoolServices->decrypt($_POST['infoString']);
            $suyoolUserInfo = explode("!#!", $decrypted_string);
            $devicetype = stripos($useragent, $suyoolUserInfo[1]);

            if ($this->notificationServices->checkUser($suyoolUserInfo[0], $suyoolUserInfo[2]) && $devicetype) {
                $SuyoolUserId = $suyoolUserInfo[0];
                $this->session->set('suyoolUserId', $SuyoolUserId);
                $this->session->set('suyoolUserId', 155);

                $parameters['deviceType'] = $suyoolUserInfo[1];

                return $this->render('gift2_games/index.html.twig', [
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
     * @Route("/gift2games/categories", name="app_g2g_categories")
     */
    public function getCategories()
    {

        $results = $this->gamesService->getCategories();

        return new JsonResponse([
            'status' => $results['status'],
            'Payload' => $results['data'],
        ], 200);
    }

    /**
     * @Route("/gift2games/products/{categoryId}", name="app_g2g_products")
     */
    public function getProducts($categoryId)
    {

        $results = $this->gamesService->getProducts($categoryId);

        return new JsonResponse([
            'status' => $results['status'],
            'Payload' => $results['data'],
        ], 200);
    }

    /**
     * PostPaid
     * Provider : Gift2Games
     * Desc: Retrieve Channel Results
     * @Route("/gift2games/product/pay", name="app_git2games_bill_pay",methods="POST")
     */
    public function pay(Request $request)
    {

        $suyoolServices = $this->suyoolServices;
        $data = json_decode($request->getContent(), true);
        $SuyoolUserId = $this->session->get('suyoolUserId');
        $amount = $data['amount'];
        $description = $data['desc'];
        $flagCode = null;
        if ($data != null) {
            $product = new Product();
            $product->setProductId((int) $data['productId']);
            $product->setDescription((string) $description);
            $product->setPrice((float) $amount);
            $product->setCurrency((string) $data['currency']);

            $order = new Order;
            $order
                ->setsuyoolUserId($SuyoolUserId)
                ->settransId(null)
                ->setstatus(Order::$statusOrder['PENDING'])
                ->setamount($amount)
                ->setcurrency($data['currency']);

            $product->setOrder($order);
            $product->setOrderId($order->getId());

            $this->mr->persist($product);
            $this->mr->persist($order);
            $this->mr->flush();

//            $checkBalance = $this->checkBalance($SuyoolUserId, $order->getId(), $amount, $data['currency']);
//            $checkBalance = json_decode($checkBalance->getContent(), true);
//            $checkBalance = $checkBalance['response'];
            $checkBalance = array(0=>"true",1=>'Ref-GN1234551');
            $transactionID = $checkBalance[1];
            if ($checkBalance[0]) {
                //set order status to held
                $orderupdate1 = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $SuyoolUserId, 'status' => Order::$statusOrder['PENDING']]);
                $orderupdate1
                    ->settransId($transactionID)
                    ->setstatus(Order::$statusOrder['HELD']);

                $this->mr->persist($orderupdate1);
                $this->mr->flush();
                $purchase = $this->Purchase($data['productId'], $transactionID, $order->getId());

                if ($purchase == true) {
                    $IsSuccess = true;
                    $additionalDataArray[] = [];
                    $additionalData = json_encode($additionalDataArray, true);
                     $content = $this->notificationServices->getContent('terranetLandlineRecharged');

                    $bulk = 0;
                    $params = json_encode([
                        'amount' => $amount,
                        'userAccount' => '',
                        'type' => $description
                    ]);
                   // $notificationServices->addNotification($suyoolUserId, $content, $params, $bulk, '');

                    //$updateUtility = $this->suyoolServices->UpdateUtilities($amount, $additionalData, $transactionID);
                    $updateUtility = true;
                    if ($updateUtility) {
                        $orderupdate3 = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $SuyoolUserId, 'status' => Order::$statusOrder['PURCHASED']]);
                        //update te status from purshased to completed
                        $orderupdate3
                            ->setstatus(Order::$statusOrder['COMPLETED'])
                            ->seterror("SUCCESS");
                        $this->mr->persist($orderupdate3);
                        $this->mr->flush();
                        $message = "Success";
                    } else {
                        $orderupdate3 = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $SuyoolUserId, 'status' => Order::$statusOrder['PURCHASED']]);
                        $orderupdate3
                            ->setstatus(Order::$statusOrder['CANCELED'])
                            ->seterror($updateUtility[1]);
                        $this->mr->persist($orderupdate3);
                        $this->mr->flush();
                        $message = "something wrong while UpdateUtilities";
                    }

                } else {
                    $IsSuccess = false;
                    //if not purchase return money
                    $responseUpdateUtilities = $this->suyoolServices->UpdateUtilities(0, "", $transactionID);
                    if ($responseUpdateUtilities[0]) {
                        $orderupdate4 = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $SuyoolUserId, 'status' => Order::$statusOrder['HELD']]);
                        $orderupdate4
                            ->setstatus(Order::$statusOrder['CANCELED'])
                            ->seterror("reversed error from Gift2Games");

                        $this->mr->persist($orderupdate4);
                        $this->mr->flush();
                        $message = "Success return money!!";
                    } else {
                        $orderupdate4 = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $SuyoolUserId, 'status' => Order::$statusOrder['HELD']]);
                        $orderupdate4
                            ->setstatus(Order::$statusOrder['CANCELED'])
                            ->seterror($responseUpdateUtilities[1]);
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
                    ->seterror($checkBalance[1]);
                $this->mr->persist($orderupdate3);
                $this->mr->flush();
                $IsSuccess = false;
                $message = json_decode($checkBalance[1], true);
                if (isset($checkBalance[2])) {
                    $flagCode = $checkBalance[2];
                }
            }
        } else {
            $IsSuccess = false;
            $message = "You dont have a bundle available";
        }

        return new JsonResponse([
            'status' => true,
            'message' => $message,
            'IsSuccess' => $IsSuccess,
            'flagCode' => $flagCode,
        ], 200);

    }

    private function checkBalance($suyoolUserId, $orderId, $amount, $currency)
    {
        $merchantId = $this->params->get('GIFT2GAMES_MERCHANT_ID') ;
        $order_id = $merchantId . "-" . $orderId;
        $fees = 0;
        $pushutility = $this->suyoolServices->PushUtilities($suyoolUserId, $order_id, $amount, $currency, $fees);

        return new JsonResponse([
            'response' => $pushutility
        ], 200);
    }

    private function Purchase($ProductId, $transID, $orderId)
    {
        $suyoolUserId = $this->session->get('suyoolUserId');

        $orderupdate2 = $this->mr->getRepository(Order::class)->findOneBy(['id' => $orderId, 'suyoolUserId' => $suyoolUserId, 'status' => Order::$statusOrder['HELD']]);

        if ($orderupdate2) {
            $refillAccount = $this->gamesService->createOrder($ProductId, $transID);

            if ($refillAccount) {
                $orderupdate2
                    ->setstatus(Order::$statusOrder['PURCHASED']);
                $this->mr->persist($orderupdate2);
                $this->mr->flush();
            }
            return $refillAccount;

        } else {
            return new JsonResponse(['error' => 'Order not found'], 404);
        }
    }
}
