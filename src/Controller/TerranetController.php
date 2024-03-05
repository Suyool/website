<?php


namespace App\Controller;

use App\Entity\Notification\Users;
use App\Entity\TerraNet\Order;
use App\Entity\TerraNet\Product;
use App\Service\DecryptService;
use App\Service\NotificationServices;
use App\Service\SuyoolServices;
use App\Service\TerraNetService;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\LogsService;

class TerranetController extends AbstractController
{
    private $apiService;
    private $session;
    private $suyoolServices;
    private $mr;
    private $params;
    private $not;

    public function __construct(TerraNetService $apiService, SessionInterface $sessionInterface, ParameterBagInterface $params, ManagerRegistry $mr, LoggerInterface $loggerInterface)
    {
        $this->apiService = $apiService;
        $this->session = $sessionInterface;
        $this->suyoolServices = new SuyoolServices($params->get('TERRANET_MERCHANT_ID'), null, null, null, $loggerInterface);
        $this->mr = $mr->getManager('terranet');
        $this->params = $params;
        $this->not = $mr->getManager('notification');
    }

    /**
     * @Route("/terraNet", name="terranet_main")
     */
    public function index(NotificationServices $notificationServices)
    {
        $useragent = $_SERVER['HTTP_USER_AGENT'];

        //$_POST['infoString'] = "3mzsXlDm5DFUnNVXA5Pu8T1d5nNACEsiiUEAo7TteE/x3BGT3Oy3yCcjUHjAVYk3";

        if (isset($_POST['infoString'])) {
            $decrypted_string = $this->suyoolServices->decrypt($_POST['infoString']);
            $suyoolUserInfo = explode("!#!", $decrypted_string);
            $devicetype = stripos($useragent, $suyoolUserInfo[1]);

            if ($notificationServices->checkUser($suyoolUserInfo[0], $suyoolUserInfo[2]) && $devicetype) {
                $SuyoolUserId = $suyoolUserInfo[0];
                $this->session->set('suyoolUserId', $SuyoolUserId);
                //$this->session->set('suyoolUserId', 155);

                $parameters['deviceType'] = $suyoolUserInfo[1];

                return $this->render('terranet/index.html.twig', [
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
     * @Route("/terraNet/get_accounts", name="terranet_get_accounts")
     */
    public function getAccounts(Request $request)
    {

        $requestData = $request->getContent();
        if (!empty($requestData)) {
            $data = json_decode($requestData, true);
            $username = $data['username'];
            $accounts = $this->apiService->getAccounts($username);
            if ($accounts) {
                $PPPLoginName = $accounts[0]['PPPLoginName'];
                $this->session->set('PPPLoginName', $PPPLoginName);
                $response = $this->apiService->getProducts($PPPLoginName);
                if ($response) {
                    $flag = 1;
                } else {
                    $flag = 2;
                    $response = "No Available Products";
                }
            } else {
                $response = "
                The number you entered was not found in the system.
                Kindly try another one.
              ";

                $flag = 2;
            }
            return new JsonResponse([
                'flag' => $flag,
                'return' => $response
            ], 200);
        }
    }

    /**
     * @Route("/terraNet/refill_customer_terranet", name="terranet_refill_customer")
     */
    public function refillCustomerTerranet(Request $request, NotificationServices $notificationServices)
    {
        $requestData = $request->getContent();
        $data = json_decode($requestData, true);
        if (!empty($data)) {
            $amount = null;
            $currency = null;
            $description = null;
            $productCost = null;
            $productOriginalHT = null;
            $suyoolUserId = $this->session->get('suyoolUserId');
            $PPPLoginName = $this->session->get('PPPLoginName');
            //$PPPLoginName = 'L314240';
            $ProductId = $data['productId'];
            $flagCode = null;
            $cachedProducts = $this->apiService->getProductsFromCache($PPPLoginName);
            $filteredProducts = array_filter($cachedProducts, function ($product) use ($ProductId) {
                return $product['ProductId'] == $ProductId;
            });

            // Check if any product matches the given ProductId
            if (!empty($filteredProducts)) {
                // Retrieve the first matching product (assuming there's only one)
                $selectedProduct = reset($filteredProducts);
                $amount = $selectedProduct['Price'];
                $currency = $selectedProduct['Currency'];
                $description = $selectedProduct['Description'];
                $productCost = $selectedProduct['Cost'];
                $productOriginalHT = $selectedProduct['OriginalHT'];
            } else {
                // Return a JSON response when no product matches the given ProductId
                return new JsonResponse([
                    'status' => 'error',
                    'message' => 'Product not found',
                ], 404);
            }

            if ($suyoolUserId != null) {

                $product = new Product();
                $product->setProductId((int) $ProductId);
                $product->setDescription((string) $description);
                $product->setPrice((float) $amount);
                $product->setCost((float) $productCost);
                $product->setOriginalHT((float) $productOriginalHT);
                $product->setCurrency((string) $currency);

                $order = new Order();
                $order->setsuyoolUserId($suyoolUserId);
                $order->setstatus("pending");
                $order->setamount($amount);
                $order->setcurrency($currency);
                $order->setProduct($product);

                $product->setOrder($order);
                $product->setOrderId($order->getId());

                $this->mr->persist($product);
                $this->mr->persist($order);
                $this->mr->flush();

                $checkBalance = $this->checkBalance($suyoolUserId, $order->getId(), $amount, $currency);
                $checkBalance = json_decode($checkBalance->getContent(), true);
                $checkBalance = $checkBalance['response'];
                $pushlog = new LogsService($this->mr);

                if ($checkBalance[0]) {
                    $transID = $checkBalance[1];
                    $orderupdate = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $suyoolUserId, 'status' => Order::$statusOrder['PENDING']]);

                    $orderupdate
                        ->setstatus(Order::$statusOrder['HELD'])
                        ->settransId($checkBalance[1]);
                    $this->mr->persist($orderupdate);
                    $this->mr->flush();

                    $refillAccount = $this->refillAccount($PPPLoginName, $ProductId, $order->getId(), $transID);
                    if ($refillAccount == true) {
                        $IsSuccess = true;
                        $additionalDataArray[] = ['suyoolUserId' => $suyoolUserId];
                        $additionalData = json_encode($additionalDataArray, true);

                        if ($data['accountType'] == 'username')
                            $content = $notificationServices->getContent('terranetLandlineRecharged');
                        else
                            $content = $notificationServices->getContent('terranetLandlineRecharged');

                        $bulk = 0;
                        $params = json_encode([
                            'amount' => $amount,
                            'userAccount' => $PPPLoginName,
                            'type' => $description
                        ]);
                        $notificationServices->addNotification($suyoolUserId, $content, $params, $bulk, '');

                        $updateUtility = $this->suyoolServices->UpdateUtilities($amount, $additionalData, $transID);
                        $pushlog->pushLogs(new Logs, "terraNet_Update_utilities", @$updateUtility[3], @$updateUtility[2], "Utilities/UpdateUtilityPayment");

                        if ($updateUtility) {
                            $orderupdate3 = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $suyoolUserId, 'status' => Order::$statusOrder['PURCHASED']]);
                            //update te status from purshased to completed
                            $orderupdate3
                                ->setstatus(Order::$statusOrder['COMPLETED'])
                                ->seterror("SUCCESS");
                            $this->mr->persist($orderupdate3);
                            $this->mr->flush();

                            $message = "Success";
                        } else {
                            $orderupdate3 = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $suyoolUserId, 'status' => Order::$statusOrder['PURCHASED']]);
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
                        $responseUpdateUtilities = $this->suyoolServices->UpdateUtilities(0, "", $transID);
                        $pushlog->pushLogs(new Logs, "terraNet_Update_utilities", @$responseUpdateUtilities[3], @$responseUpdateUtilities[2], "Utilities/UpdateUtilityPayment");

                        if ($responseUpdateUtilities[0]) {
                            $orderupdate4 = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $suyoolUserId, 'status' => Order::$statusOrder['HELD']]);
                            $orderupdate4
                                ->setstatus(Order::$statusOrder['CANCELED'])
                                ->seterror("reversed error from terranet");;

                            $this->mr->persist($orderupdate4);
                            $this->mr->flush();
                            $message = "Success return money!!";
                        } else {
                            $orderupdate4 = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $suyoolUserId, 'status' => Order::$statusOrder['HELD']]);
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
                    $orderupdate1 = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $suyoolUserId, 'status' => Order::$statusOrder['PENDING']]);
                    $orderupdate1
                        ->setstatus(Order::$statusOrder['CANCELED'])
                        ->seterror($checkBalance[1]);
                    $this->mr->persist($orderupdate1);
                    $this->mr->flush();
                    $IsSuccess = false;
                    $message = json_decode($checkBalance[1], true);
                    if (isset($checkBalance[2])) {
                        $flagCode = $checkBalance[2];
                    }
                }
            } else {
                $IsSuccess = false;
                $message = "Don't have userId in session please contact the administrator or login";
            }
        } else {
            $IsSuccess = false;
            $message = "You dont have a bundle available";
        }

        return new JsonResponse([
            'message' => $message,
            'IsSuccess' => $IsSuccess,
            'flagCode' => $flagCode,
        ], 200);
    }

    private function checkBalance($suyoolUserId, $orderId, $amount, $currency)
    {
        $merchantId = $this->params->get('TERRANET_MERCHANT_ID');
        $order_id = $merchantId . "-" . $orderId;
        $fees = 0;
        $pushutility = $this->suyoolServices->PushUtilities($suyoolUserId, $order_id, $amount, $currency, $fees);
        $pushlog = new LogsService($this->mr);
        $pushlog->pushLogs(new Logs, "terraNet_pushUtilities", @$pushutility[4], @$pushutility[5], @$pushutility[7], @$pushutility[6]);
        return new JsonResponse([
            'response' => $pushutility
        ], 200);
    }

    private function refillAccount($PPPLoginName, $ProductId, $orderId, $transID)
    {
        $suyoolUserId = $this->session->get('suyoolUserId');

        $orderupdate2 = $this->mr->getRepository(Order::class)->findOneBy(['id' => $orderId, 'suyoolUserId' => $suyoolUserId, 'status' => Order::$statusOrder['HELD']]);

        if ($orderupdate2) {
            $refillAccount = $this->apiService->refillCustomerTerranet($PPPLoginName, $ProductId, $transID);
            $checkTransaction = json_decode($this->checkTransactionStatus($transID)->getContent(), true);
            if ($checkTransaction['status'] == 'true') {
                $orderupdate2
                    ->setstatus(Order::$statusOrder['PURCHASED']);
                $this->mr->persist($orderupdate2);
                $this->mr->flush();
            }
            return $checkTransaction['status'];
        } else {
            return new JsonResponse(['error' => 'Order not found'], 404);
        }
    }


    /**
     * @Route("/check_transaction_status", name="terranet_check_transaction_status")
     */
    public function checkTransactionStatus($tranID)
    {

        $response = $this->apiService->checkTransactionStatus($tranID);

        return new JsonResponse([
            'status' => $response
        ]);
    }

    /**
     * @Route("/get_transactions", name="get_transactions")
     */
    public function getTransactions(Request $request)
    {
        $fromDate = $request->request->get('fromDate', '31-10-2023');
        $toDate = $request->request->get('toDate', '01-11-2023');
        $fromDate = new \DateTime('2023-10-01');
        $toDate = new \DateTime('2023-11-01');

        $response = $this->apiService->getTransactions($fromDate, $toDate);
        $transactions = $response['Transactions'];
        $errorCode = $response['ErrorCode'];
        $errorMessage = $response['ErrorMessage'];

        return new JsonResponse([
            'transactions' => $transactions,
            'errorCode' => $errorCode,
            'errorMessage' => $errorMessage,
        ]);
    }

    /**
     * @Route("api/terranet", name="ap1_terranet",methods="GET")
     */
    public function checkWebkey(NotificationServices $notificationServices)
    {
        $webkey = apache_request_headers();
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
     * @Route("api/terranet/get_accounts", name="ap2_terranet_get_accounts")
     */
    public function getacountsapi(Request $request, NotificationServices $notificationServices)
    {
        try {
            $webkey = apache_request_headers();
            $webkeyDecrypted = SuyoolServices::decryptWebKey($webkey);

            if ($notificationServices->checkUser($webkeyDecrypted['merchantId'], $webkeyDecrypted['lang']) &&  $webkeyDecrypted['devicesType'] == "CORPORATE") {
                $requestData = $request->getContent();
                if (!empty($requestData)) {
                    $data = json_decode($requestData, true);
                    $username = $data['username'];
                    $accounts = $this->apiService->getAccounts($username);
                    if ($accounts) {
                        $PPPLoginName = $accounts[0]['PPPLoginName'];
                        $this->session->set('PPPLoginName', $PPPLoginName);
                        $response = $this->apiService->getProducts($PPPLoginName);
                        $data = [];
                        foreach ($response as $response) {
                            $data[] = [
                                "ProductId" => $response['ProductId'],
                                "Description" => $response['Description'],
                                "Price" => $response['Price'],
                                "Cost" => $response['Cost'],
                                "OriginalHT" => $response['OriginalHT'],
                                "Currency" => $response['Currency'],
                                "image" => (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . "/build/images/terraNet/circle_product_{$response['ProductId']}.png",
                                "icon" => (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . "/build/images/terraNet/product_{$response['ProductId']}.svg"
                            ];
                        };
                        if ($response) {
                            $flag = 1;
                            $message="Data retrieved";
                            $parameters = [
                                'flag' => $flag,
                                'return' => $data
                            ];
                        } else {
                            $flag = 2;
                            $data = "No Available Products";
                            $message=$data;
                            $parameters = [
                                'Popup' => [
                                    "Title" => "No Available Products",
                                    "globalCode" => 0,
                                    "flagCode" => 800,
                                    "Message" => @$data,
                                    "isPopup" => true
                                ]
                                ];
                        }
                    } else {
                        $data = "The number you entered was not found in the system. <br>Kindly try another one.";
                        $message=$data;
                        $flag = 2;
                        $parameters = [
                            'Popup' => [
                                "Title" => "Number Not Found",
                                "globalCode" => 0,
                                "flagCode" => 800,
                                "Message" => @$data,
                                "isPopup" => true
                            ]
                            ];
                    }
                }
                return new JsonResponse([
                    'status' => true,
                    'message' => @$message,
                    'data' => $parameters

                ], 200);
            } else {
                return new JsonResponse([
                    'status' => false,
                    'message' => "Unauthorize",
                    'data'=>[
                        'Popup'=>[
                            "Title" => "Unauthorize",
                            "globalCode" => 0,
                            "flagCode" => 801,
                            "Message" => "You have been unauthorized",
                            "isPopup" => true
                        ]
                    ]
                ], 401);
            }
        } catch (Exception $e) {
            return new JsonResponse([
                'status' => false,
                'message' => "An error has occured",
                'data' => [
                    'Popup' => [
                        "Title" => "An error has occured",
                        "globalCode" => 0,
                        "flagCode" => 802,
                        "Message" => "An error has occured",
                        "isPopup" => true
                    ]
                ]
            ], 500);
        }
    }

    /**
     * @Route("api/terranet/refill_customer_terranet", name="ap3_terranet_refill")
     */
    public function refillapi(Request $request, NotificationServices $notificationServices)
    {
        try {
            $webkey = apache_request_headers();
            $webkeyDecrypted = SuyoolServices::decryptWebKey($webkey);

            if ($notificationServices->checkUser($webkeyDecrypted['merchantId'], $webkeyDecrypted['lang']) &&  $webkeyDecrypted['devicesType'] == "CORPORATE") {
                $requestData = $request->getContent();
                $data = json_decode($requestData, true);
                if (!empty($data)) {
                    $amount = null;
                    $currency = null;
                    $description = null;
                    $productCost = null;
                    $productOriginalHT = null;
                    $suyoolUserId = $webkeyDecrypted['merchantId'];
                    // $PPPLoginName = $this->session->get('PPPLoginName');
                    $PPPLoginName = $data['PPPLoginName'];
                    $ProductId = $data['productId'];
                    $flagCode = null;
                    $cachedProducts = $this->apiService->getProductsFromCache($PPPLoginName);
                    $filteredProducts = array_filter($cachedProducts, function ($product) use ($ProductId) {
                        return $product['ProductId'] == $ProductId;
                    });

                    // Check if any product matches the given ProductId
                    if (!empty($filteredProducts)) {
                        // Retrieve the first matching product (assuming there's only one)
                        $selectedProduct = reset($filteredProducts);
                        $amount = $selectedProduct['Price'];
                        $currency = $selectedProduct['Currency'];
                        $description = $selectedProduct['Description'];
                        $productCost = $selectedProduct['Cost'];
                        $productOriginalHT = $selectedProduct['OriginalHT'];
                    } else {
                        // Return a JSON response when no product matches the given ProductId
                        return new JsonResponse([
                            'status' => 'error',
                            'message' => 'Product not found',
                        ], 404);
                    }

                    if ($suyoolUserId != null) {

                        $product = new Product();
                        $product->setProductId((int) $ProductId);
                        $product->setDescription((string) $description);
                        $product->setPrice((float) $amount);
                        $product->setCost((float) $productCost);
                        $product->setOriginalHT((float) $productOriginalHT);
                        $product->setCurrency((string) $currency);

                        $order = new Order();
                        $order->setsuyoolUserId($suyoolUserId);
                        $order->setstatus("pending");
                        $order->setamount($amount);
                        $order->setcurrency($currency);
                        $order->setProduct($product);

                        $product->setOrder($order);
                        $product->setOrderId($order->getId());

                        $this->mr->persist($product);
                        $this->mr->persist($order);
                        $this->mr->flush();

                        $suyooler = $this->not->getRepository(Users::class)->findOneBy(['suyoolUserId' => $suyoolUserId]);

                        $checkBalance = $this->checkBalance($suyoolUserId, $order->getId(), $amount, $currency);
                        $checkBalance = json_decode($checkBalance->getContent(), true);
                        $checkBalance = $checkBalance['response'];
                        $pushlog = new LogsService($this->mr);

                        if ($checkBalance[0]) {
                            $transID = $checkBalance[1];
                            $orderupdate = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $suyoolUserId, 'status' => Order::$statusOrder['PENDING']]);

                            $orderupdate
                                ->setstatus(Order::$statusOrder['HELD'])
                                ->settransId($checkBalance[1]);
                            $this->mr->persist($orderupdate);
                            $this->mr->flush();

                            $refillAccount = $this->refillAccount($PPPLoginName, $ProductId, $order->getId(), $transID);
                            if ($refillAccount == true) {
                                $IsSuccess = true;
                                $additionalDataArray[] = ['suyoolUserId' => $suyoolUserId];
                                $additionalData = json_encode($additionalDataArray, true);
                                if ($suyooler->getType() == 2) {
                                    $content = $notificationServices->getContent('terranetLandlineRecharged');
                                    $bulk = 0;
                                    $params = json_encode([
                                        'amount' => $amount,
                                        'userAccount' => $PPPLoginName,
                                        'type' => $description
                                    ]);
                                    $notificationServices->addNotification($suyoolUserId, $content, $params, $bulk, '');
                                } else {
                                    $content = $notificationServices->getContent('terranetLandlineRechargedCorporate');
                                    $params = json_encode([
                                        'amount' => $amount,
                                        'userAccount' => $PPPLoginName,
                                        'type' => $description,
                                        'name'=>$data['PayerName']
                                    ]);
                                    $bulk = 1; //1 for broadcast 0 for unicast
                                    $notificationServices->addNotification($data["getUsersToReceiveNotification"], $content, $params, $bulk, $additionalData);
                                }

                                $updateUtility = $this->suyoolServices->UpdateUtilities($amount, $additionalData, $transID);
                                $pushlog->pushLogs(new Logs, "terraNet_Update_utilities", @$updateUtility[3], @$updateUtility[2], "Utilities/UpdateUtilityPayment");

                                if ($updateUtility) {
                                    $orderupdate3 = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $suyoolUserId, 'status' => Order::$statusOrder['HELD']]);
                                    //update te status from purshased to completed
                                    $orderupdate3
                                        ->setstatus(Order::$statusOrder['COMPLETED'])
                                        ->seterror("SUCCESS");
                                    $this->mr->persist($orderupdate3);
                                    $this->mr->flush();

                                    // $message = "Success";
                                    $popup = [
                                        "Title" => "Terranet Bill Paid Successfully",
                                        "globalCode" => 0,
                                        "flagCode" => 0,
                                        "Message" => "You have successfully paid your Terranet landline of L.L " . number_format($order->getamount()) . ".",
                                        "isPopup" => true
                                    ];
                                    $message = "Success";
                                    $messageBack = "Success";
                                } else {
                                    $orderupdate3 = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $suyoolUserId, 'status' => Order::$statusOrder['PURCHASED']]);
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
                                $responseUpdateUtilities = $this->suyoolServices->UpdateUtilities(0, "", $transID);
                                $pushlog->pushLogs(new Logs, "terraNet_Update_utilities", @$responseUpdateUtilities[3], @$responseUpdateUtilities[2], "Utilities/UpdateUtilityPayment");

                                if ($responseUpdateUtilities[0]) {
                                    $orderupdate4 = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $suyoolUserId, 'status' => Order::$statusOrder['HELD']]);
                                    $orderupdate4
                                        ->setstatus(Order::$statusOrder['CANCELED'])
                                        ->seterror("reversed error from terranet");;

                                    $this->mr->persist($orderupdate4);
                                    $this->mr->flush();
                                    $message = "Success return money!!";
                                } else {
                                    $orderupdate4 = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $suyoolUserId, 'status' => Order::$statusOrder['HELD']]);
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
                            $orderupdate1 = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $suyoolUserId, 'status' => Order::$statusOrder['PENDING']]);
                            $orderupdate1
                                ->setstatus(Order::$statusOrder['CANCELED'])
                                ->seterror($checkBalance[1]);
                            $this->mr->persist($orderupdate1);
                            $this->mr->flush();
                            $IsSuccess = false;
                            $message = json_decode($checkBalance[1], true);
                            if (isset($checkBalance[2])) {
                                $flagCode = $checkBalance[2];
                            }
                            if ($flagCode == -1) {
                                $message = "You cannot purchase now";
                                $messageBack = "You cannot purchase now";
                            } else {
                                $popup = [
                                    "Title" => @$message['Title'],
                                    "globalCode" => 0,
                                    "flagCode" => @$message['ButtonOne']['Flag'],
                                    "Message" => @$message['SubTitle'],
                                    "isPopup" => true
                                ];
                            }
                        }
                    } else {
                        $IsSuccess = false;
                        $message = "Don't have userId in session please contact the administrator or login";
                    }
                } else {
                    $IsSuccess = false;
                    $message = "You dont have a bundle available";
                }
                $parameters = [
                    'message' => $message,
                    'IsSuccess' => $IsSuccess,
                    'flagCode' => $flagCode,
                    'Popup' => @$popup,
                ];
                return new JsonResponse([
                    'status' => true,
                    'message' => @$messageBack,
                    'data' => $parameters
                ], 200);
            } else {
                return new JsonResponse([
                    'status' => false,
                    'message' => 'unauthorize'
                ], 401);
            }
        } catch (Exception $e) {
            return new JsonResponse([
                'status' => true,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
