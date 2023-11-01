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

        return new JsonResponse([
            'accounts' => $response
        ]);
    }

    /**
     * @Route("/get_products", name="terranet_get_products")
     */
    public function getProducts(Request $request)
    {
        $PPPLoginName = $request->query->get('PPPLoginName','L314240');
        $response = $this->apiService->getProducts($PPPLoginName);

        return $this->render('terranet/products.html.twig', [
            'products' => $response,
        ]);
    }

    /**
     * @Route("/refill_customer_terranet", name="terranet_refill_customer")
     */
    public function refillCustomerTerranet(Request $request)
    {
        $PPPLoginName = $request->request->get('PPPLoginName','L314240');
        $ProductId = $request->request->get('ProductId','-851');
        $TransactionID = $request->request->get('TransactionID','1');

        $response = $this->apiService->refillCustomerTerranet($PPPLoginName, $ProductId, $TransactionID);

        return new JsonResponse([
            'refill' => $response
        ]);
    }

    /**
     * @Route("/check_transaction_status", name="terranet_check_transaction_status")
     */
    public function checkTransactionStatus(Request $request)
    {
        $TransactionID = $request->request->get('TransactionID','1');

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
        $fromDate = $request->request->get('fromDate','31-10-2023');
        $toDate = $request->request->get('toDate','01-11-2023');
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