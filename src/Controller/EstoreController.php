<?php

namespace App\Controller;

use App\Entity\Estore\Company;
use App\Entity\Estore\Price;
use App\Entity\Estore\Product;
use Doctrine\Persistence\ManagerRegistry;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class EstoreController extends AbstractController
{
    private $mr;
    private $client;

    public function __construct(ManagerRegistry $mr, HttpClientInterface $client)
    {
        $this->mr = $mr->getManager('estore');
        $this->client = $client;
    }

    private function _decodeGzipString(string $gzipString): string
    {
        $decodedString = '';
        $decodedData = @gzdecode($gzipString);

        if ($decodedData !== false) {
            $decodedString = $decodedData;
        }

        return $decodedString;
    }




    // /**
    //  * @Route("/estore", name="app_estore")
    //  */
    // public function index()
    // {

    //     $response = $this->client->request('POST', 'https://services.bob-finance.com:8445/BoBFinanceAPI/WS/GetChannelConfigurationFile', [
    //         'body' => json_encode([
    //             "ChannelType" => "API",
    //             "PreConfiguredDataId" => "21",
    //             "Credentials" => [
    //                 "User" => "suyool",
    //                 "Password" => "p@123123"
    //             ]
    //         ]),
    //         'headers' => [
    //             'Content-Type' => 'application/json'
    //         ]
    //     ]);
    //     $content = $response->getContent();

    //     $ApiResponse = json_decode($content, true);
    //     $res = $ApiResponse['Response'];
    //     $decodedString = $this->_decodeGzipString(base64_decode($res));

    //     $JsonResponse = json_decode($decodedString);
    //     $JsonResponse = json_decode($decodedString, true);
    //     $companies = $JsonResponse['ConfigurationFileDataId:21'][0];
    //     // dd($companies);
    //     // foreach ($companies as $company) {
    //     //     $Company = new Company;
    //     //     $Company
    //     //         ->setcompanyId($company[0])
    //     //         ->setcompanyDescription($company[1])
    //     //         ->setinternalCode($company[2])
    //     //         ->setproductId($company[3])
    //     //         ->setproductDescription($company[4])
    //     //         ->setservicesCode($company[5])
    //     //         ->setservicesProviderCode($company[6]);

    //     //     $this->mr->persist($Company);
    //     //     $this->mr->flush();
    //     // }

    //     return $this->render('estore/index.html.twig', [
    //         'controller_name' => 'EstoreController',
    //     ]);
    // }
    /**
     * @Route("/estore", name="app_estore")
     */




    // public function index()
    // {

    //     $response = $this->client->request('POST', 'https://services.bob-finance.com:8445/BoBFinanceAPI/WS/GetChannelConfigurationFile', [
    //         'body' => json_encode([
    //             "ChannelType" => "API",
    //             "PreConfiguredDataId" => "22",
    //             "Credentials" => [
    //                 "User" => "suyool",
    //                 "Password" => "p@123123"
    //             ]
    //         ]),
    //         'headers' => [
    //             'Content-Type' => 'application/json'
    //         ]
    //     ]);
    //     $content = $response->getContent();

    //     $ApiResponse = json_decode($content, true);
    //     $res = $ApiResponse['Response'];
    //     $decodedString = $this->_decodeGzipString(base64_decode($res));

    //     $JsonResponse = json_decode($decodedString);
    //     $JsonResponse = json_decode($decodedString, true);
    //     $Products = $JsonResponse['ConfigurationFileDataId:22'][0];
    //     // dd($Products);
    //     foreach ($Products as $product) {
    //         $Product = new Product;
    //         $Product
    //             ->setproductId($product[0])
    //             ->setproductName($product[1])
    //             ->setdenominationId($product[2])
    //             ->setdenominationDescription($product[3])
    //             ->setparentConfigurationDataId($product[4])
    //             ->setserviceCode($product[5])
    //             ->setserviceProviderCode($product[6]);

    //         $this->mr->persist($Product);
    //         $this->mr->flush();
    //     }

    //     return $this->render('estore/index.html.twig', [
    //         'controller_name' => 'EstoreController',
    //     ]);
    // }

    public function index()
    {

        $response = $this->client->request('POST', 'https://services.bob-finance.com:8445/BoBFinanceAPI/WS/GetChannelConfigurationFile', [
            'body' => json_encode([
                "ChannelType" => "API",
                "PreConfiguredDataId" => "23",
                "Credentials" => [
                    "User" => "suyool",
                    "Password" => "p@123123"
                ]
            ]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        $content = $response->getContent();

        $ApiResponse = json_decode($content, true);
        $res = $ApiResponse['Response'];
        $decodedString = $this->_decodeGzipString(base64_decode($res));

        $JsonResponse = json_decode($decodedString);
        $JsonResponse = json_decode($decodedString, true);
        $Prices = $JsonResponse['ConfigurationFileDataId:23'][0];
        dd($Prices);

        return $this->render('estore/index.html.twig', [
            'controller_name' => 'EstoreController',
        ]);
    }
}
