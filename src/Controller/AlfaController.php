<?php

namespace App\Controller;

use App\Entity\Alfa\Order;
use App\Entity\Alfa\Postpaid;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Utils\Helper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\Response;

class AlfaController extends AbstractController
{

    private $BOB_API_HOST;
    private $LOTO_API_HOST;

    private $mr;
    private $client;

    public function __construct(ManagerRegistry $mr, HttpClientInterface $client)
    {
        $this->mr = $mr->getManager('alfa');
        $this->client = $client;

        //todo: if prod environment
        if ($_ENV['APP_ENV'] == 'prod') {
            $this->BOB_API_HOST = 'https://services.bob-finance.com:8445/BoBFinanceAPI/WS/';
            $this->LOTO_API_HOST = 'https://backbone.lebaneseloto.com/Service.asmx/';
        } else {
            $this->BOB_API_HOST = 'https://185.174.240.230:8445/BoBFinanceAPI/WS/';
            $this->LOTO_API_HOST = 'https://backbone.lebaneseloto.com/Service.asmx/';
        }
    }

    private function _decodeGzipString(string $gzipString): string
    {
        // Decode GZip string
        $decodedString = '';
        $decodedData = @gzdecode($gzipString);

        // Check if the decoding was successful
        if ($decodedData !== false) {
            $decodedString = $decodedData;
        }

        return $decodedString;
    }

    /**
     * @Route("/alfa", name="app_alfa")
     */
    public function index()
    {
        $postpaid = $this->mr->getRepository(Postpaid::class)->findAll();
        $orders = $this->mr->getRepository(Order::class)->findAll();
        // dd($postpaid);
        $parameters['Test'] = "tst";

        return $this->render('alfa/index.html.twig', [
            'parameters' => $parameters
        ]);
    }

    /**
     * PostPaid
     * Provider : BOB
     * Desc: Send Pin to user based on phoneNumber
     * @Route("/alfa/bill", name="app_alfa_bill",methods="POST")
     */
    public function bill(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        if ($data != null) {

            $response = $this->client->request('POST', $this->BOB_API_HOST . '/SendPinRequest', [
                'body' => json_encode([
                    "ChannelType" => "API",
                    "AlfaPinParam" => [
                        "GSMNumber" => "70102030"
                        // "GSMNumber" => "03184740"
                    ],
                    "Credentials" => [
                        "User" => "suyool1",
                        "Password" => "SUYOOL1"
                        // "User" => "suyool",
                        // "Password" => "p@123123"
                    ]
                ]),
                'headers' => [
                    'Content-Type' => 'application/json'
                ]
            ]);

            $content = $response->getContent();
            $content = $response->toArray();

            dd($content);
        } else {
            $message = "not connected";
        }

        return new JsonResponse([
            'status' => true,
            'message' => $message
        ], 200);
    }

    /**
     * PostPaid
     * Provider : BOB
     * Desc: Retrieve Channel Results 
     * @Route("/alfa/bill/pay", name="app_alfa_bill_pay",methods="POST")
     */
    public function billPay(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        if ($data != null) {
            $response = $this->client->request('POST', $this->BOB_API_HOST . '/RetrieveChannelResults', [
                'body' => json_encode([
                    "ChannelType" => "API",
                    "ItemId" => "1",
                    "VenId" => "1",
                    "ProductId" => "4",
                    "TransactionId" => "tst",

                    "AlfaBillResult" => [
                        "Fees" => "tst",
                        "TransactionId" => "tst",
                        "Amount" => "tst",
                        "Amount1" => "tst",
                        "ReferenceNumber" => "tst",
                        "Fees1" => "tst",
                        "Amount2" => "tst",
                        "InformativeOriginalWSAmount" => "tst",
                        "TotalAmount" => "tst",
                        "Currency" => "tst",
                        "Rounding" => "tst",
                        "AdditionalFees" => "tst",
                    ],
                    "Credentials" => [
                        "User" => "suyool1",
                        "Password" => "SUYOOL1"
                    ]
                ]),
                'headers' => [
                    'Content-Type' => 'application/json'
                ]
            ]);

            $content = $response->getContent();
            $content = $response->toArray();

            dd($content);
        } else {
            $message = "not connected";
        }

        return new JsonResponse([
            'status' => true,
            'message' => $message
        ], 200);
    }


    /**
     * PrePaid
     * Provider : LOTO
     * Desc: Fetch ReCharge vouchers
     * @Route("/alfa/ReCharge", name="app_alfa_ReCharge",methods="POST")
     */
    public function ReCharge()
    {
        $response = $this->client->request('POST', "{$this->LOTO_API_HOST}GetAllVouchersType", [
            'body' => json_encode([
                "Token" => "",
            ]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);

        $content = $response->getContent();
        $content = $response->toArray();

        return new JsonResponse([
            'status' => true,
            'message' => $content
        ], 200);
    }
}
