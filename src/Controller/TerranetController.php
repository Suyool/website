<?php


namespace App\Controller;


use App\Entity\TerraNet\Order;
use App\Entity\TerraNet\Product;
use App\Service\DecryptService;
use App\Service\NotificationServices;
use App\Service\SuyoolServices;
use App\Service\TerraNetService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TerranetController extends AbstractController
{
    private $apiService;
    private $session;
    private $suyoolServices;
    private $mr;
    private $params;

    public function __construct(TerraNetService $apiService, SessionInterface $sessionInterface, ParameterBagInterface $params, ManagerRegistry $mr)
    {
        $this->apiService = $apiService;
        $this->session = $sessionInterface;
        $this->suyoolServices = new SuyoolServices($params->get('TERRANET_MERCHANT_ID'));
        $this->mr = $mr->getManager('terranet');
        $this->params = $params;
    }

    /**
     * @Route("/terraNet", name="terranet_main")
     */
    public function index(NotificationServices $notificationServices)
    {
        $useragent = $_SERVER['HTTP_USER_AGENT'];
        //$_POST['infoString'] = "3mzsXlDm5DFUnNVXA5Pu8T1d5nNACEsiiUEAo7TteE/x3BGT3Oy3yCcjUHjAVYk3";

        if (isset($_POST['infoString'])) {
            $decrypted_string = DecryptService::decrypt($_POST['infoString']);
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
                if ($response){
                    $flag = 1;
                }else{
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
                'flag'=> $flag,
                'return' => $response
            ], 200);

        }
    }

    /**
     * @Route("/terraNet/refill_customer_terranet", name="terranet_refill_customer")
     */
    public function refillCustomerTerranet(Request $request,NotificationServices $notificationServices)
    {
        $requestData = $request->getContent();
        $data = json_decode($requestData, true);
        if (!empty($data)) {
            $amount = $data['productPrice'];
            $currency = $data['productCurrency'];
            $description = $data['productDescription'];
            $suyoolUserId = $this->session->get('suyoolUserId');
            $PPPLoginName = $this->session->get('PPPLoginName');
            //$PPPLoginName = 'L314240';
            $ProductId = $data['productId'];
            $flagCode = null;

            if ($suyoolUserId != null) {

                $product = new Product();
                $product->setProductId((int) $ProductId);
                $product->setDescription((string) $description);
                $product->setPrice((float) $amount);
                $product->setCost((float) $data['productCost']);
                $product->setOriginalHT((float) $data['productOriginalHT']);
                $product->setCurrency((string) $data['productCurrency']);

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

                if ($checkBalance[0]) {
                    $transID = $checkBalance[1];
                    $orderupdate = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $suyoolUserId, 'status' => Order::$statusOrder['PENDING']]);

                    $orderupdate
                        ->setstatus(Order::$statusOrder['HELD']);
                    $this->mr->persist($orderupdate);
                    $this->mr->flush();

                    $refillAccount = $this->refillAccount($PPPLoginName, $ProductId, $order->getId(), $transID);
                    if ($refillAccount == true) {
                        $IsSuccess = true;
                        $additionalDataArray[] = ['suyoolUserId' => $suyoolUserId];
                        $additionalData = json_encode($additionalDataArray, true);
                        $content = $notificationServices->getContent('terranetLandlineRecharged');
                        $bulk = 0;
                        $params = json_encode([
                            'amount' => $amount,
                            'userAccount' => $PPPLoginName,
                            'type' => $description
                        ]);
                        $notificationServices->addNotification($suyoolUserId, $content, $params, $bulk, '');

                        $updateUtility = $this->suyoolServices->UpdateUtilities($amount, $additionalData, $transID);
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
}