<?php

namespace App\Controller;

use App\Translation\translation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class PaymentController extends AbstractController
{

    private $trans;

    public function __construct(translation $trans)
    {
        $this->trans=$trans;
    }

    /**
     * @Route("/payment", name="app_payment")
     */
    public function index(Request $request,TranslatorInterface $translator): Response
    {
        $trans=$this->trans->translation($request,$translator);

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
