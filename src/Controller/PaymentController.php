<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PaymentController extends AbstractController
{
    /**
     * @Route("/payment", name="app_payment")
     */
    public function index(): Response
    {
        $parameters['currency'] = "dollar";
        $parameters['currentPage'] = "payment_landingPage";

        return $this->render('payment/index.html.twig',$parameters);
    }

     /**
     * @Route("/payment/generateCode", name="generateCode")
     */
    public function generateCode(): Response
    {
        $parameters['currency'] = "dollar";
        $parameters['currentPage'] = "generate_Code";

        return $this->render('payment/generateCode.html.twig',$parameters);
    }

    /**
     * @Route("/payment/codeGenerated", name="codeGenerated")
     */
    public function codeGenerated(): Response
    {
        $parameters['currency'] = "dollar";
        $parameters['currentPage'] = "GenerateCode2";

        return $this->render('payment/codeGenerated.html.twig',$parameters);
    }
}
