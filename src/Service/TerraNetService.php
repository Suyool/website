<?php


namespace App\Service;

use App\Entity\TerraNet\Account;
use App\Entity\TerraNet\Product;
use App\Entity\TerraNet\RefillTransaction;
use App\Entity\TerraNet\Transaction;
use Doctrine\Persistence\ManagerRegistry;
use GuzzleHttp\Client;
use App\Utils\Helper;

class TerraNetService
{
    private $helper;
    private $mr;
    private $soapClient;

    public function __construct(Helper $helper, ManagerRegistry $mr)
    {
        $this->helper = $helper;
        $this->mr = $mr->getManager('terranet');
        $this->soapClient = new \SoapClient('https://psp.terra.net.lb/TerraRefill.asmx?WSDL', [
            'trace' => 1,
        ]);
    }
    public function getAccounts(string $username)
    {
        try {
            $result = $this->soapClient->__soapCall("GetAccounts", [
                'Uid' => 'SuyoolWS',
                'Pid' => 'sd^$lKoihb61',
                'Username' => $username,
            ]);
            $accounts = $result->GetAccountsResult;
            $errorCode = $result->errorCode;
            $errorMessage = $result->errorMessage;

            return [
                'Accounts' => $accounts,
                'ErrorCode' => $errorCode,
                'ErrorMessage' => $errorMessage,
            ];
        } catch (\SoapFault $e) {
            return [
                'Accounts' => null,
                'ErrorCode' => -1,
                'ErrorMessage' => $e->getMessage(),
            ];
        }
    }

//    public function getAccounts($username)
//    {
//        $url = 'https://psp.terra.net.lb/GetAccounts';
//
//        $data = [
//            'Uid' => 'SuyoolWS',
//            'Pid' => 'sd^$lKoihb61',
//            'Username' => $username,
//        ];
//
//        $response = $this->helper->clientRequest('POST', $url, $data);
//        if ($response->getStatusCode() === 200) {
//            $responseBody = $response->getContent();
//            $responseData = json_decode($responseBody, true);
//            foreach ($responseData['Accounts'] as $accountData) {
//                $account = new Account();
//                $account->setCustomerid($accountData['customerid']);
//                $account->setPPPLoginName($accountData['PPPLoginName']);
//                $account->setFirstname($accountData['firstname']);
//                $account->setLastname($accountData['lastname']);
//                $this->mr->persist($account);
//            }
//            $this->mr->flush();
//
//            return $responseData;
//        } else {
//            return [
//                'ErrorCode' => $response->getStatusCode(),
//                'ErrorMessage' => 'API request failed',
//            ];
//        }
//    }

    public function getProducts($PPPLoginName)
    {
        $url = 'https://psp.terra.net.lb/GetProducts';

        // Prepare the request data
        $data = [
            'Uid' => 'SuyoolWS',
            'Pid' => 'sd^$lKoihb61',
            'PPPLoginName' => $PPPLoginName,
        ];

        $response = $this->helper->clientRequest('POST', $url, $data);

        if ($response->getStatusCode() === 200) {
            $responseBody = $response->getContent();
            $responseData = json_decode($responseBody, true);

            foreach ($responseData['Products'] as $productData) {
                $product = new Product();
                $product->setProductid($productData['productid']);
                $product->setDescription($productData['description']);
                $product->setPrice($productData['price']);
                $product->setCost($productData['cost']);
                $product->setOriginalHT($productData['originalHT']);
                $product->setCurrency($productData['currency']);

                $this->mr->persist($product);
            }
            $this->mr->flush();

            return $responseData;
        } else {
            return [
                'ErrorCode' => $response->getStatusCode(),
                'ErrorMessage' => 'API request failed',
            ];
        }
    }

    public function refillCustomerTerranet($PPPLoginName, $ProductId, $TransactionID)
    {
        $url = 'https://psp.terra.net.lb/RefillCustomerTerranet';

        $data = [
            'Uid' => 'SuyoolWS',
            'Pid' => 'sd^$lKoihb61',
            'PPPLoginName' => $PPPLoginName,
            'ProductId' => $ProductId,
            'TransactionID' => $TransactionID,
        ];

        $response = $this->helper->clientRequest('POST', $url, $data);

        if ($response->getStatusCode() === 200) {
            $responseBody = $response->getContent();
            $responseData = json_decode($responseBody, true);

            $transaction = new RefillTransaction();
            $transaction
                ->setPPPLoginName($PPPLoginName)
                ->setProductId($ProductId)
                ->setTransactionID($TransactionID)
                ->setErrorCode($responseData['ErrorCode'])
                ->setErrorMessage($responseData['ErrorMessage']);

            $this->mr->persist($transaction);

            $this->mr->flush();

            return $responseData;

        } else {
            return [
                'ErrorCode' => $response->getStatusCode(),
                'ErrorMessage' => 'API request failed',
            ];
        }
    }

    public function checkTransactionStatus($TransactionID)
    {
        $url = 'https://psp.terra.net.lb/CheckTransactionStatus';

        $data = [
            'Uid' => 'SuyoolWS',
            'Pid' => 'sd^$lKoihb61',
            'TransactionID' => $TransactionID,
        ];

        $response = $this->helper->clientRequest('POST', $url, $data);

        if ($response->getStatusCode() === 200) {
            $responseBody = $response->getContent();
            $responseData = json_decode($responseBody, true);

            return $responseData;
        } else {
            return [
                'ErrorCode' => $response->getStatusCode(),
                'ErrorMessage' => 'API request failed',
            ];
        }
    }

    public function getTransactions($fromDate, $toDate)
    {
        $url = 'https://psp.terra.net.lb/GetTransactions';

        $data = [
            'Uid' => 'SuyoolWS',
            'Pid' => 'sd^$lKoihb61',
            'fromDate' => $fromDate,
            'toDate' => $toDate,
        ];

        $response = $this->helper->clientRequest('POST', $url, $data);

        if ($response->getStatusCode() === 200) {
            $responseBody = $response->getContent();
            $responseData = json_decode($responseBody, true);

            foreach ($responseData['Transactions'] as $transactionData) {
                $transaction = new Transaction();
                $transaction->setTransactionID($transactionData['TransactionID']);
                $transaction->setProductId($transactionData['ProductId']);
                $transaction->setUsername($transactionData['Username']);
                $transaction->setPaidAmount($transactionData['PaidAmount']);
                $transaction->setCancelled($transactionData['Cancelled']);
                $transaction->setDate(new \DateTime($transactionData['Date']));

                $this->entityManager->persist($transaction);
            }

            $this->entityManager->flush();


            return $responseData;
        } else {
            return [
                'ErrorCode' => $response->getStatusCode(),
                'ErrorMessage' => 'API request failed',
            ];
        }
    }
}
