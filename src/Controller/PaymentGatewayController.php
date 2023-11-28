<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class PaymentGatewayController extends AbstractController
{
    /**
     * @Route("/payment_gateway", name="payment_gateway_main")
     */
    public function index()
    {
        return $this->render('paymentGateway/index.html.twig');
    }

    /**
     * @Route("/payment_gateway_QR", name="payment_gateway_qr")
     */
    public function payWithQR(): Response
    {
        return $this->render('payment/qr.html.twig');
    }

    /**
     * @Route("/payment_gateway_BOB", name="payment_gateway_bob")
     */
    public function payWithBoB(): Response
    {

        return $this->render('payment/bob.html.twig');
    }
}