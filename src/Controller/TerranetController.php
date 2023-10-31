<?php


namespace App\Controller;


use App\Service\TerraNetService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TerranetController extends AbstractController
{
    private $apiService;

    public function __construct(TerraNetService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * @Route("/get_accounts", name="terranet_get_accounts")
     */
    public function getAccounts(Request $request)
    {
        $username = $request->request->get('username', 'L314240'); // Default to 'L314240' if not provided.


        $response = $this->apiService->getAccounts($username);

        $accounts = $response['Accounts'];
        $errorCode = $response['ErrorCode'];
        $errorMessage = $response['ErrorMessage'];

        return new JsonResponse([
            'accounts' => $accounts,
            'errorCode' => $errorCode,
            'errorMessage' => $errorMessage,
        ]);
    }

    /**
     * @Route("/get_products", name="terranet_get_products")
     */
    public function getProducts(Request $request)
    {
        $PPPLoginName = $request->query->get('PPPLoginName');
        $response = $this->apiService->getProducts($PPPLoginName);

        $products = $response['Products'];
        $errorCode = $response['ErrorCode'];
        $errorMessage = $response['ErrorMessage'];

        return new JsonResponse([
            'products' => $products,
            'errorCode' => $errorCode,
            'errorMessage' => $errorMessage,
        ]);
    }

    /**
     * @Route("/refill_customer_terranet", name="terranet_refill_customer")
     */
    public function refillCustomerTerranet(Request $request)
    {
        $PPPLoginName = $request->request->get('PPPLoginName');
        $ProductId = $request->request->get('ProductId');
        $TransactionID = $request->request->get('TransactionID');

        $response = $this->apiService->refillCustomerTerranet($PPPLoginName, $ProductId, $TransactionID);

        $errorCode = $response['ErrorCode'];
        $errorMessage = $response['ErrorMessage'];

        return new JsonResponse([
            'errorCode' => $errorCode,
            'errorMessage' => $errorMessage,
        ]);
    }

    /**
     * @Route("/check_transaction_status", name="terranet_check_transaction_status")
     */
    public function checkTransactionStatus(Request $request)
    {
        $TransactionID = $request->request->get('TransactionID');

        $response = $this->apiService->checkTransactionStatus($TransactionID);

        $errorCode = $response['ErrorCode'];
        $errorMessage = $response['ErrorMessage'];

        return new JsonResponse([
            'errorCode' => $errorCode,
            'errorMessage' => $errorMessage,
        ]);
    }

    /**
     * @Route("/get_transactions", name="get_transactions")
     */
    public function getTransactions(Request $request)
    {
        $fromDate = $request->request->get('fromDate');
        $toDate = $request->request->get('toDate');

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