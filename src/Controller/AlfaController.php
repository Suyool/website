<?php

namespace App\Controller;

use App\Entity\Alfa\Order;
use App\Entity\Alfa\Postpaid;
use App\Service\FilteringVoucher;
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

        // dd($data);
        if ($data != null) {
            // $response = $this->client->request('POST', $this->BOB_API_HOST . 'SendPinRequest', [
            //     'body' => json_encode([
            //         "ChannelType" => "API",
            //         "AlfaPinParam" => [
            //             "GSMNumber" => $data["mobileNumber"]
            //             // "GSMNumber" => "70102030"
            //             // "GSMNumber" => "03184740"
            //         ],
            //         "Credentials" => [
            //             "User" => "suyool1",
            //             "Password" => "SUYOOL1"
            //             // "User" => "suyool",
            //             // "Password" => "p@123123"
            //         ]
            //     ]),
            //     'headers' => [
            //         'Content-Type' => 'application/json'
            //     ]
            // ]);

            // $content = $response->getContent();
            // $content = $response->toArray();
            // dd($content);

            // $ApiResponse = json_decode($content, true);
            // $res = $ApiResponse['Response'];
            // $decodedString = $this->_decodeGzipString(base64_decode($res));
            // dd($decodedString);

            // dd($res);
            $message = "connected";

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
     * @Route("/alfa/bill/RetrieveResults", name="app_alfa_RetrieveResults",methods="POST")
     */
    public function RetrieveResults(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        // dd($data);
        if ($data != null) {
            // $response = $this->client->request('POST', $this->BOB_API_HOST . '/RetrieveChannelResults', [
            //     'body' => json_encode([
            //         "ChannelType" => "API",
            //         "ItemId" => "1",
            //         "VenId" => "1",
            //         "ProductId" => "4",

            //         "AlfaBillMeta" => [
            //             "Currency" => $data["currency"],
            //             "GSMNumber" => $data["mobileNumber"],
            //             "PIN" => $data["Pin"],
            //         ],
            //         "Credentials" => [
            //             "User" => "suyool1",
            //             "Password" => "SUYOOL1"
            //         ]
            //     ]),
            //     'headers' => [
            //         'Content-Type' => 'application/json'
            //     ]
            // ]);

            // $content = $response->getContent();
            // $content = $response->toArray();

            // dd($content);

            $Postpaid = new Postpaid;
            $Postpaid->setfees("2")
                ->setfees1("0")
                ->setamount("104.58")
                ->setamount1("0")
                ->setamount2("0")
                ->setreferenceNumber("20230700000042")
                ->setinformativeOriginalWSamount("104.58")
                ->settotalamount("106.58")
                ->setcurrency("USD")
                ->setrounding("0")
                ->setadditionalfees("0")
                ->setSuyoolUserId("1234567")
                ->setPin("0000")
                ->setGsmNumber("70102030")
                ->setTransactionId("1735028");

            $this->mr->persist($Postpaid);
            $this->mr->flush();
            // dd($Postpaid->getId());
            $postpayed = $Postpaid->getId();
            $message = "connected";
        
        } else {
            $message = "not connected";
            $postpayed = -1;
        }

        return new JsonResponse([
            'status' => true,
            'message' => $message,
            'postpayed' => $postpayed
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
        // dd($data);
 
        $Postpaid_With_id = $this->mr->getRepository(Postpaid::class)->findOneBy(['id' => $data["ResponseId"]]);
        dd($Postpaid_With_id);
 
        // if ($data != null) {
        //     $response = $this->client->request('POST', $this->BOB_API_HOST . '/RetrieveChannelResults', [
        //         'body' => json_encode([
        //             "ChannelType" => "API",
        //             "ItemId" => "1",
        //             "VenId" => "1",
        //             "ProductId" => "4",
        //             "TransactionId" => "tst",

        //             "AlfaBillResult" => [
        //                 "Fees" => "tst",
        //                 "TransactionId" => "tst",
        //                 "Amount" => "tst",
        //                 "Amount1" => "tst",
        //                 "ReferenceNumber" => "tst",
        //                 "Fees1" => "tst",
        //                 "Amount2" => "tst",
        //                 "InformativeOriginalWSAmount" => "tst",
        //                 "TotalAmount" => "tst",
        //                 "Currency" => "tst",
        //                 "Rounding" => "tst",
        //                 "AdditionalFees" => "tst",
        //             ],
        //             "Credentials" => [
        //                 "User" => "suyool1",
        //                 "Password" => "SUYOOL1"
        //             ]
        //         ]),
        //         'headers' => [
        //             'Content-Type' => 'application/json'
        //         ]
        //     ]);

        //     $content = $response->getContent();
        //     $content = $response->toArray();

        //     dd($content);
        // } else {
        //     $message = "not connected";
        // }

        return new JsonResponse([
            'status' => true,
            // 'message' => $message
        ], 200);
    }


    /**
     * PrePaid
     * Provider : LOTO
     * Desc: Fetch ReCharge vouchers
     * @Route("/alfa/ReCharge", name="app_alfa_ReCharge",methods="POST")
     */
    public function ReCharge(FilteringVoucher $filteringVoucher)
    {
        $filter = $filteringVoucher->VoucherFilter("ALFA");

        return new JsonResponse([
            'status' => true,
            'message' => $filter
        ], 200);
    }
}
