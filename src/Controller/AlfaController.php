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
use Symfony\Component\HttpFoundation\Response;

class AlfaController extends AbstractController
{
    public function decodeGzipString(string $gzipString): string
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

    private $mr;

    public function __construct(ManagerRegistry $mr)
    {
        $this->mr = $mr->getManager('alfa');
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
     * @Route("/alfa/bill", name="app_alfa_bill",methods="POST")
     */
    public function bill(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        if ($data != null) {
            $params['mobileNumber'] = json_encode($data['mobileNumber'], true);

            $form_data = [
                "ChannelType" => "API",
                "AlfaPinParam" => [
                    "GSMNumber" => "70102030"
                    // "GSMNumber" => "03184740"
                ],
                "Credentials" => [
                    "User" => "suyool1",
                    "Password" => "SUYOOL1"
                ]
                // "Credentials" => [
                //     "User" => "suyool",
                //     "Password" => "p@123123"
                // ]
            ];

            $params['data'] = json_encode($form_data);
            $params['url'] = '/BoBFinanceAPI/WS/SendPinRequest';
            // dd($params['data']);

            /*** Call the api ***/
            $response = Helper::send_curl($params, 'alfa');
            dd($response);

            $parameters['update_utility_response'] = json_decode($response, true);
            $res = $parameters['update_utility_response']['Response'];
            $decodedString = $this->decodeGzipString(base64_decode($res));
            dd($decodedString);
            dd($res);
            $message = "connected";

            dd($parameters['update_utility_response']);
        } else {
            $message = "not connected";
        }

        dd($data);

        return new JsonResponse([
            'status' => true,
            'message' => $message
        ], 200);
    }


    // /**
    //  * @Route("/alfa/bill/pay", name="app_alfa_bill_pay",methods="POST")
    //  */
    // public function billPay(Request $request)
    // {
    //     $data = json_decode($request->getContent(), true);

    //     if ($data != null) {
    //         $params['mobileNumber'] = json_encode($data['mobileNumber'], true);

    //         $form_data = [
    //             "ChannelType" => "API",
    //             "ItemId" => "1",
    //             "VenId" => "1",
    //             "ProductId" => "4",
    //             "TransactionId" => $tst,

    //             "AlfaBillResult" => [
    //                 "Fees" => $tst,
    //                 "TransactionId" => $tst,
    //                 "Amount" => $tst,
    //                 "Amount1" => $tst,
    //                 "ReferenceNumber" => $tst,
    //                 "Fees1" => $tst,
    //                 "Amount2" => $tst,
    //                 "InformativeOriginalWSAmount" => $tst,
    //                 "TotalAmount" => $tst,
    //                 "Currency" => $tst,
    //                 "Rounding" => $tst,
    //                 "AdditionalFees" => $tst,
    //             ],
    //             "Credentials" => [
    //                 "User" => "suyool1",
    //                 "Password" => "SUYOOL1"
    //             ]
    //         ];

    //         $params['data'] = json_encode($form_data);
    //         $params['url'] = '/BoBFinanceAPI/WS/RetrieveChannelResults';
    //         // dd($params['data']);

    //         /*** Call the api ***/
    //         $response = Helper::send_curl($params, 'alfa');

    //         $parameters['update_utility_response'] = json_decode($response, true);
    //         $message = "connected";

    //         dd($parameters['update_utility_response']);
    //     } else {
    //         $message = "not connected";
    //     }

    //     dd($data);

    //     return new JsonResponse([
    //         'status' => true,
    //         'message' => $message
    //     ], 200);
    // }
}