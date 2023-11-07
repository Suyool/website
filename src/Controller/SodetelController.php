<?php

namespace App\Controller;

use App\Entity\Sodetel\Order;
use App\Repository\SodetelOrdersRepository;
use App\Service\BobServices;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class SodetelController extends AbstractController
{
    private $mr;
    private $hash_algo;
    private $certificate;
    private $notMr;
    private $params;
    public $cipher_algorithme = "AES128";
    public $key = "SY1X24elh9eG3fpOaHcWlQ9h2bHaqimdIDoyoOaFoi0rukAj3Z";
    public $iv = "fgu26y9e43wc8dj2"; //initiallization vector for decrypt
    private $session;

    public function __construct(ManagerRegistry $mr, $certificate, $hash_algo, ParameterBagInterface $params, SessionInterface $sessionInterface)
    {
        $this->mr = $mr->getManager('sodetel');
        $this->hash_algo = $hash_algo;
        $this->certificate = $certificate;
        $this->notMr = $mr->getManager('notification');
        $this->params = $params;
        $this->session = $sessionInterface;
    }

    /**
     * @Route("/sodetel", name="app_sodetel")
     */
    public function index() {
        $parameters['deviceType'] = 'android';
        return $this->render('sodetel/index.html.twig', [
            'parameters' => $parameters
        ]);
    }

    /**
     * PostPaid
     * Provider : Sodetel
     * Desc: Send Pin to user based on phoneNumber
     * @Route("/sodetel/bill", name="app_sodetel_bill",methods="POST")
     */
    public function bill(Request $request )
    {
        $ordersRepository = $this->mr->getRepository(Order::class);

        $data = json_decode($request->getContent(), true);
        $SuyoolUserId = $data['suyoolUserId'];
        $status = $data['status'];
        $amount = $data['amount'];
        $currency = $data['currency'];
        $transId = $data['transId'];

        $order = new Order();
        $order->setSuyoolUserId($SuyoolUserId);
        $order->setStatus($status);
        $order->setAmount($amount);
        $order->setCurrency($currency);
        $order->setTransId($transId);

        $res = $ordersRepository->saveOrder($order);

        return new JsonResponse($res? [
            'status' => true,
            'message' => "Bill Paid Successfully",
            'invoicesId' => 1234
        ] : [
            'status' => false,
            'message' => "Bill Paid Failed",
            'invoicesId' => 0
        ], 200);
    }

}