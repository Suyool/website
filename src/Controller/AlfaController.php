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

class AlfaController extends AbstractController
{
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
     * @Route("/alfa/play", name="app_alfa_request",methods="POST")
     */
    public function play(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        if ($data != null) {
            $params['mobileNumber'] = json_encode($data['mobileNumber'], true);

            $form_data = [
                "ChannelType" => "API",
                "AlfaPinParam" => [
                    "GSMNumber" => "70102030"
                ],
                "Credentials" => [
                    "User" => "suyool1",
                    "Password" => "SUYOOL1"
                ]
            ];

            $params['data'] = json_encode($form_data);
            $params['url'] = '/BoBFinanceAPI/WS/RetrieveChannelResults';
            // dd($params['data']);

            /*** Call the api ***/
            $response = Helper::send_curl($params, 'alfa');

            $parameters['update_utility_response'] = json_decode($response, true);
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
}