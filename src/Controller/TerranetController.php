<?php


namespace App\Controller;


use App\Entity\TerraNet\Order;
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
        $_POST['infoString'] = "3mzsXlDm5DFUnNVXA5Pu8T1d5nNACEsiiUEAo7TteE/x3BGT3Oy3yCcjUHjAVYk3";

        if (isset($_POST['infoString'])) {
            $decrypted_string = DecryptService::decrypt($_POST['infoString']);
            $suyoolUserInfo = explode("!#!", $decrypted_string);
            $devicetype = stripos($useragent, $suyoolUserInfo[1]);

            if ($notificationServices->checkUser($suyoolUserInfo[0], $suyoolUserInfo[2]) && $devicetype) {
                $SuyoolUserId = $suyoolUserInfo[0];
                $this->session->set('suyoolUserId', $SuyoolUserId);
                $this->session->set('suyoolUserId', 155);

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
            if (!empty($accounts)) {
                $PPPLoginName = $accounts[0]['PPPLoginName'];
                $this->session->set('PPPLoginName', $PPPLoginName);
                $response = $this->apiService->getProducts($PPPLoginName);

            } else {
                $response = "Invalid Accounts";
            }
            return new JsonResponse([
                'return' => $response
            ], 200);

        }
    }

    /**
     * @Route("/terraNet/refill_customer_terranet", name="terranet_refill_customer")
     */
    public function refillCustomerTerranet(Request $request)
    {
        $requestData = $request->getContent();
        $data = json_decode($requestData, true);
        if (!empty($data)) {
            $amount = $data['productPrice'];
            $currency = $data['productCurrency'];
            $suyoolUserId = $this->session->get('suyoolUserId');
            $PPPLoginName = $this->session->get('PPPLoginName');
            $PPPLoginName = 'L314240';
            $ProductId = $data['productId'];

            if ($suyoolUserId != null) {
                $order = new Order();
                $order->setsuyoolUserId($suyoolUserId);
                $order->setstatus("pending");
                $order->setamount($amount);
                $order->setcurrency($currency);

                $this->mr->persist($order);
                $this->mr->flush();
                //$checkBalance['status'] = true;
                $checkBalance = $this->checkBalance($suyoolUserId, $order->getId(), $amount, $currency);
                $checkBalance = json_decode($checkBalance, true);
                //$transID = 3213;

                if ($checkBalance['status'] == true) {
                    $refillAccount = $this->refillAccount($PPPLoginName, $ProductId, $order->getId());
                    if ($refillAccount == true) {
                        $status = true;
                        $message = "Terranet Bill Paid Successfully";

                        $additionalDataArray[] = ['suyoolUserId' => $suyoolUserId];
                        $additionalData = json_encode($additionalDataArray, true);

                        $updateutility = $this->suyoolServices->UpdateUtilities($amount, $additionalData,$transID );
                        if ($updateutility) {
                            $orderupdate = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $suyoolUserId, 'status' => Order::$statusOrder['PURCHASED']]);
                            //update te status from purshased to completed
                            $orderupdate
                                ->setstatus(Order::$statusOrder['COMPLETED'])
                                ->seterror("SUCCESS");
                            $this->mr->persist($orderupdate);
                            $this->mr->flush();

                            //$dataPayResponse = ['amount' => $order->getamount(), 'currency' => $order->getcurrency()];
                            $message = "Success";
                        } else {
                            $orderupdate = $this->mr->getRepository(Order::class)->findOneBy(['id' => $order->getId(), 'suyoolUserId' => $suyoolUserId, 'status' => Order::$statusOrder['PURCHASED']]);
                            $orderupdate
                                ->setstatus(Order::$statusOrder['CANCELED'])
                                ->seterror($updateutility[1]);
                            $this->mr->persist($orderupdate);
                            $this->mr->flush();
                            $message = "something wrong while UpdateUtilities";
                        }

                    }
                }
            } else {
                $status = false;
                $message = "Don't have userId in session please contact the administrator or login";
            }
        } else {
            $status = false;
            $message = "You dont have a bundle available";
        }

        return new JsonResponse([
            'status' => $status,
            'message' => $message,
        ], 200);

    }

    private function checkBalance($suyoolUserId, $orderId, $amount, $currency)
    {
        $merchantId = $this->params->get('TERRANET_MERCHANT_ID'); // 1 for loto merchant
        $order_id = $merchantId . "-" . $orderId;
        $pushutility = $this->suyoolServices->PushUtilities($suyoolUserId, $order_id, $amount, $currency);
        $order = $this->mr->getRepository(Order::class)->find($orderId);

        if ($pushutility[0]) {
            $order->settransId($pushutility[1]);
            $order->setstatus(Order::$statusOrder['HELD']);
            $order->setamount($amount);
            $order->setcurrency($currency);
            $status = true;
            $message = "You have chose your bundle";
            $transID = $pushutility[1];

        } else {
            $order->settransId($pushutility[3]);
            $order->setstatus(Order::$statusOrder['CANCELED']);
            $order->setamount($amount);
            $order->setcurrency($currency);
            $status = false;
            $message = "You dont have enough Cash";
            $transID = $pushutility[3];
        }
        $this->mr->persist($order);
        $this->mr->flush();
        return new JsonResponse([
            'status' => $status,
            'message' => $message,
            'transId'=>$transID,
        ], 200);
    }

    private function refillAccount($PPPLoginName, $ProductId, $orderId)
    {

        $order = $this->mr->getRepository(Order::class)->find($orderId);

        if ($order) {
            $response = $this->apiService->refillCustomerTerranet($PPPLoginName, $ProductId, $orderId);
            if ($response == 'true') {
                $order->setstatus(Order::$statusOrder['PURCHASED']);

                $this->mr->persist($order);
                $this->mr->flush();
            }
            return $response;

        } else {
            return new JsonResponse(['error' => 'Order not found'], 404);
        }
    }


    /**
     * @Route("/check_transaction_status", name="terranet_check_transaction_status")
     */
    public function checkTransactionStatus(Request $request)
    {
        $TransactionID = $request->request->get('TransactionID', '1');

        $response = $this->apiService->checkTransactionStatus($TransactionID);

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
        dd($response);
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