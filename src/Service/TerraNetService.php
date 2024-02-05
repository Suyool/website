<?php


namespace App\Service;

use App\Entity\TerraNet\Logs;
use Doctrine\Persistence\ManagerRegistry;
use GuzzleHttp\Client;
use App\Utils\Helper;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Cache\Adapter\AdapterInterface;

class TerraNetService
{
    private $helper;
    private $mr;
    private $soapClient;
    private $wsdl;
    private $uid;
    private $pid;
    private $memcachedCache;

    public function __construct(Helper $helper, ManagerRegistry $mr,AdapterInterface  $memcachedCache)
    {
        $this->helper = $helper;
        $this->mr = $mr->getManager('terranet');
        $this->wsdl = 'https://psp.terra.net.lb/TerraRefill.asmx?WSDL';
        $this->uid = 'SuyoolWS';
        $this->pid = 'fg^stpJD&4bCeXVk';
        $this->memcachedCache = $memcachedCache;

        $options = [
            'trace' => 1,
            'encoding' => 'UTF-8',
        ];

        $this->soapClient = new \SoapClient($this->wsdl, $options);
    }

    public function getAccounts($username)
    {
        $params = [
            'uid' => $this->uid,
            'pid' => $this->pid,
            'username' => $username,
            'accounts' => '',
            'errorCode' => '0',
            'errorMessage' => '',
        ];

        try {

            $response = $this->soapClient->GetAccounts($params);

            $logs = new Logs();
            $logs
                ->setidentifier("Bundle Purchase")
                ->seturl("https://psp.terra.net.lb/TerraRefill.asmx?WSDL~GetAccounts")
                ->setrequest(json_encode($params))
                ->setresponse(json_encode($response))
                ->seterror($response->errorMessage);
            $this->mr->persist($logs);
            $this->mr->flush();

            if ($response->errorCode == 0) {
                $anyData = $response->accounts->any;
                $xml = new \SimpleXMLElement($anyData);
                $accountData = [];

                foreach ($xml->NewDataSet->Table1 as $table1) {
                    $accounts = [
                        'PPPLoginName' => (string)$table1->PPPLoginName,
                        'FirstName' => (string)$table1->FirstName,
                        'LastName' => (string)$table1->LastName,
                        'CustomerId' => (string)$table1->CustomerId,
                    ];

                    $accountData[] = $accounts;
                }

                return $accountData;
            } else {
                return false;
            }


        } catch (\SoapFault $fault) {
            return $fault;
        }

    }

    public function getAccountProduct($PPPLoginName)
    {
        $params = [
            'uid' => $this->uid,
            'pid' => $this->pid,
            'pppLoginName' => $PPPLoginName,
            'products' => '',
            'errorCode' => '0',
            'errorMessage' => '',
        ];

        try {
            $client = HttpClient::create();

            $response = $client->request('POST', 'https://psp.terra.net.lb/TerraRefill.asmx', [
                'headers' => [
                    'Content-Type' => 'text/xml; charset=utf-8',
                    'SOAPAction' => 'http://terra.net.lb/GetAccountProduct',
                ],
                'body' => '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:terr="http://terra.net.lb/">
                    <soapenv:Header/>
                    <soapenv:Body>
                        <terr:GetAccountProduct>
                            <terr:uid>SuyoolWS</terr:uid>
                            <terr:pid>sd^$lKoihb61</terr:pid>
                            <terr:pppLoginName>L314240</terr:pppLoginName>
                            <terr:products></terr:products>
                            <terr:errorCode>0</terr:errorCode>
                            <terr:errorMessage></terr:errorMessage>
                        </terr:GetAccountProduct>
                    </soapenv:Body>
                </soapenv:Envelope>',
            ]);
            $content = $response->getContent();

            $xml = new \SimpleXMLElement($content);
            $xml->registerXPathNamespace('ns', 'http://terra.net.lb/');
            $products = $xml->xpath('//ns:GetAccountProductResponse/ns:products/*/*/Table');
            foreach ($products as $product) {
                // Get the Original_HT value
                $originalHTValue = (float) $product->Original_HT;

                // Create a new property OriginalHT with the same value as Original_HT
                $product->addChild('OriginalHT', $originalHTValue);

                // Unset the original Original_HT property
                unset($product->Original_HT);
            }

            $errorCode = (int) $xml->xpath('//errorCode');
            $errorMessage = (int) $xml->xpath('//errorMessage');

            $logs = new Logs();
            $logs
                ->setidentifier("Bundle Purchase")
                ->seturl("https://psp.terra.net.lb/TerraRefill.asmx?WSDL~GetAccountProduct")
                ->setrequest(json_encode($params))
                ->setresponse(json_encode($response))
                ->seterror($errorMessage);

            $this->mr->persist($logs);
            $this->mr->flush();

            if ($errorCode == 0) {
                return $products;
            } else {
                return false;
            }
        } catch (\SoapFault $fault) {
            return $fault;
        }
    }
    public function getProducts($PPPLoginName)
    {
        $cacheKey = 'products_' . $PPPLoginName;


        $params = [
            'uid' => $this->uid,
            'pid' => $this->pid,
            'pppLoginName' => $PPPLoginName,
            'products' => '',
            'errorCode' => '0',
            'errorMessage' => '',
        ];
        try {

            $response = $this->soapClient->GetProducts($params);
            $logs = new Logs();
            $logs
                ->setidentifier("Bundle Purchase")
                ->seturl("https://psp.terra.net.lb/TerraRefill.asmx?WSDL~GetProducts")
                ->setrequest(json_encode($params))
                ->setresponse(json_encode($response))
                ->seterror($response->errorMessage);
            $this->mr->persist($logs);
            $this->mr->flush();
            if ($response->errorCode == 0) {

                $anyData = $response->products->any;
                $xml = new \SimpleXMLElement($anyData);
                $productsArray = [];

                foreach ($xml->NewDataSet->Table as $productData) {
                    $products = [
                        'ProductId' => (int)$productData->ProductId,
                        'Description' => (string)$productData->Description,
                        'Price' => (float)$productData->Price,
                        'Cost' => (float)$productData->Cost,
                        'OriginalHT' => (float)$productData->Original_HT,
                        'Currency' => (string)$productData->Currency,
                    ];
                    $productsArray[] = $products;
//                $product = new Product();
//                $product->setProductId((int) $productData->ProductId);
//                $product->setDescription((string) $productData->Description);
//                $product->setPrice((float) $productData->Price);
//                $product->setCost((float) $productData->Cost);
//                $product->setOriginalHT((float) $productData->Original_HT);
//                $product->setCurrency((string) $productData->Currency);
//                $this->mr->persist($product);

                }
                // $this->mr->flush();
// Save the products in Memcached
                $cacheItem = $this->memcachedCache->getItem($cacheKey);
                $cacheItem->set($productsArray);
                $this->memcachedCache->save($cacheItem);

                return $productsArray;
            } else {
                return false;
            }
        } catch (\SoapFault $fault) {
            return $fault;
        }
    }

    public function refillCustomerTerranet($PPPLoginName, $ProductId, $TransactionID)
    {
        $params = [
            'uid' => $this->uid,
            'pid' => $this->pid,
            'pppLoginName' => $PPPLoginName,
            'productId' => $ProductId,
            'transactionId' => $TransactionID,
            'errorCode' => '0',
            'errorMessage' => '',
        ];

        try {

            $response = $this->soapClient->RefillCustomerTerranet($params);
            $RefillCustomerTerranetResult = $response->RefillCustomerTerranetResult;


            return $RefillCustomerTerranetResult;
        } catch (\SoapFault $fault) {
            return $fault;
        }
    }

    public function checkTransactionStatus($TransactionID)
    {
        $params = [
            'uid' => $this->uid,
            'pid' => $this->pid,
            'transactionId' => $TransactionID,
            'errorCode' => '0',
            'errorMessage' => '',
        ];


        try {
            $response = $this->soapClient->CheckTransactionStatus($params);
            $checkTransactionStatusResult = $response->CheckTransactionStatusResult;

            return $checkTransactionStatusResult;
        } catch (\SoapFault $fault) {
            return $fault;
        }
    }

    public function getTransactions($fromDate, $toDate)
    {
        $params = [
            'uid' => $this->uid,
            'pid' => $this->pid,
            'fromDate' => $fromDate->format('Y-m-d\TH:i:s'),
            'toDate' => $toDate->format('Y-m-d\TH:i:s'),
            'transactions' => '',
            'errorCode' => '0',
            'errorMessage' => '',
        ];
        try {
            $response = $this->soapClient->GetTransactions($params);
            $anyData = $response->transactions->any;
            $xml = new \SimpleXMLElement($anyData);
            $transactionsData = [];
            foreach ($xml->NewDataSet->Table as $transactionData) {
                $transactions = [
                    'TransactionID' => (int)$transactionData->TransactionID,
                    'TransactionDate' => (string)$transactionData->TransactionDate,
                    'Username' => (float)$transactionData->Username,
                    'PackageId' => (float)$transactionData->PackageId,
                    'Cancelled' => (float)$transactionData->Cancelled,
                    'PaidAmount' => (string)$transactionData->PaidAmount,
                ];
                $transactionsData[] = $transactions;

            }
            return $transactionsData;
        } catch (\SoapFault $fault) {
            return $fault;
        }
    }
    public function getProductsFromCache($PPPLoginName)
    {
        // Use cache key based on $PPPLoginName
        $cacheKey = 'products_' . $PPPLoginName;
        // Retrieve data from Memcached
        $cachedProducts = $this->memcachedCache->getItem($cacheKey);
        if ($cachedProducts->isHit()) {
            // Data is present in Memcached, retrieve and return it
            return $cachedProducts->get();
        } else {
            // Data is not present in Memcached, handle accordingly
            return false;
        }
    }
}
