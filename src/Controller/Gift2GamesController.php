<?php

namespace App\Controller;

use App\Entity\Gift2Games\Categories;
use App\Entity\Gift2Games\Order;
use App\Entity\Gift2Games\Product;
use App\Entity\Gift2Games\Products;
use App\Entity\Gift2Games\Transaction;
use App\Service\Gift2GamesService;
use App\Service\NotificationServices;
use App\Service\SuyoolServices;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use DateTime;

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
     * @Route("/gift2games/{id}", name="admin_categories_edit", requirements={"id"="\d+"}, defaults={"id"=null})
     */
    public function index($id): Response
    {
        $parameters['deviceType'] = "Android";
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
                //$this->session->set('suyoolUserId', 155);

                $parameters['deviceType'] = $suyoolUserInfo[1];
                $parameters['TypeID'] = $id;
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
     * @Route("/gift2games/categories/{type}", name="app_g2g_categories")
     */
    public function getCategories($type)
    {
        $data = $this->mr->getRepository(Categories::class)->findBy(['type' => $type]);
        $categoriesArray = [];

        foreach ($data as $category) {
            $categoriesArray[] = $category->toArray();
        }

        return new JsonResponse([
            'status' => 'success',
            'Payload' => $categoriesArray,
        ], 200);
    }

    /**
     * @Route("/gift2games/categories/{parentId}/childs", name="app_g2g_child_categories")
     */
    public function getChildCategories($parentId)
    {
        // Fetch all child categories for the given parent ID
        $childCategories = $this->mr->getRepository(Categories::class)->findBy(['parent' => $parentId]);
        $childCategoriesArray = [];
        foreach ($childCategories as $childCategory) {
            $childCategoriesArray[] = $childCategory->toArray();
        }

        return new JsonResponse([
            'status' => 'success',
            'Payload' => $childCategoriesArray,
        ], 200);
    }
    /**
     * @Route("/gift2games/products/{categoryId}", name="app_g2g_products")
     */
    public function getProducts($categoryId)
    {
        $data = $this->mr->getRepository(Products::class)->findBy(['categoryId' => $categoryId]);

        // Convert each product to array with limited recursion depth
        $dataArray = [];
        foreach ($data as $product) {
            $dataArray[] = $product->toArray();
        }
        return new JsonResponse([
            'status' => 'success',
            'Payload' => $dataArray,
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

        $data = json_decode($request->getContent(), true);
        $SuyoolUserId = $this->session->get('suyoolUserId');
        $amount = $data['amount'];
        $description = $data['desc'];
        $flagCode = null;
        if ($data != null) {
            $transaction = new Transaction();
            $transaction->setProductId((int) $data['productId']);
            $transaction->setDescription((string) $description);
            $transaction->setPrice((float) $amount);
            $transaction->setCurrency((string) $data['currency']);

            $order = new Order;
            $order
                ->setsuyoolUserId($SuyoolUserId)
                ->settransId(null)
                ->setstatus(Order::$statusOrder['PENDING'])
                ->setamount($amount)
                ->setcurrency($data['currency']);

            $transaction->setOrder($order);
            $transaction->setOrderId($order->getId());

            $this->mr->persist($transaction);
            $this->mr->persist($order);
            $this->mr->flush();

            $checkBalance = $this->checkBalance($SuyoolUserId, $order->getId(), $amount, $data['currency']);
            $checkBalance = json_decode($checkBalance->getContent(), true);
            $checkBalance = $checkBalance['response'];
//            $checkBalance =array(true,123123,1);
            $transactionID = $checkBalance[1];
            $purchaseData = -1;
            if ($checkBalance[0]) {
                //set order status to held
                $orderupdate1 = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $SuyoolUserId, 'status' => Order::$statusOrder['PENDING']]);
                $orderupdate1
                    ->settransId($transactionID)
                    ->setstatus(Order::$statusOrder['HELD']);

                $this->mr->persist($orderupdate1);
                $this->mr->flush();
                $purchase = $this->Purchase($data['productId'], $transactionID, $order->getId());

                if (isset($purchase['status']) && $purchase['status'] == true) {
                    $IsSuccess = true;

                    $purchaseData = json_decode($purchase['data'],true);

                    $dateString = $purchaseData['data']['serialExpiryDate'];
                    if(isset($dateString))
                    {
                        $contentType = 'Gift2GamesVouchersExpiryDate';
                    }else {
                        $contentType = 'Gift2GamesVouchersWithoutExpiryDate';
                    }
                    $content = $this->notificationServices->getContent($contentType);

                    $dateTime = DateTime::createFromFormat('U', $dateString);
                    $formattedDate = $dateTime->format('d/m/Y');

                    $orderupdate1->setSerialCode($purchaseData['data']['serialCode']);
                    $orderupdate1->setSerialNumber($purchaseData['data']['serialNumber']);
                    $orderupdate1->setOrderFake($purchaseData['data']['OrderFake']);

                    $this->mr->persist($orderupdate1);
                    $this->mr->flush();
                    $userDetails = $this->notificationServices->GetuserDetails($SuyoolUserId);
                    $userName = $userDetails[0] . ' ' . $userDetails[1];

                    $bulk = 0;
                    $params = json_encode([
                        'ProviderName' => $data['categoryName'],
                        'fname' => $userName,
                        'amount' => $amount,
                        'type' => $description,
                        'code' => $purchaseData['data']['serialCode'],
                        'serial'=>$purchaseData['data']['serialNumber'],
                        'expiry'=>$formattedDate
                    ]);
                    $additionalData =  $purchaseData['data']['serialCode'] ;

                    $this->notificationServices->addNotification($SuyoolUserId, $content, $params, $bulk, $additionalData);

                    $updateUtility = $this->suyoolServices->UpdateUtilities($amount, $additionalData, $transactionID);
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
            $purchaseData =-1;
        }

        return new JsonResponse([
            'status' => true,
            'message' => $message,
            'IsSuccess' => $IsSuccess,
            'flagCode' => $flagCode,
            'data' => $purchaseData,
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

            if(isset($refillAccount[1])) {
                $dataContent = json_decode($refillAccount[1],true);
                $orderupdate2
                    ->setError($dataContent['message']);
                $this->mr->persist($orderupdate2);
                $this->mr->flush();
            }

            if (isset($refillAccount['data'])) {
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
