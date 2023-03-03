<?php

namespace App\Controller;
use App\Translation\translation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class RequestController extends AbstractController
{ 
    private $trans;

    public function __construct(translation $trans)
    {
        $this->trans=$trans;
    }

    /**
     * @Route("/request", name="app_request")
     */
    public function index(Request $request,TranslatorInterface $translator): Response
    {
        $trans=$this->trans->translation($request,$translator);

        $parameters['currency'] = "dolar";
        $parameters['currentPage'] = "payment_landingPage";

        return $this->render('request/index.html.twig',$parameters);
    }

     /**
     * @Route("/request/generateCode", name="generateCode")
     */
    public function generateCode(Request $request,TranslatorInterface $translator): Response
    {
        $trans=$this->trans->translation($request,$translator);

        $parameters['currency'] = "dollar";
        $parameters['currentPage'] = "generate_Code";

        return $this->render('request/generateCode.html.twig',$parameters);
    }

    /**
     * @Route("/request/codeGenerated", name="codeGenerated")
     */
    public function codeGenerated(Request $request,TranslatorInterface $translator): Response
    {
        $trans=$this->trans->translation($request,$translator);


        $parameters['currency'] = "dollar";
        $parameters['currentPage'] = "GenerateCode2";

        return $this->render('request/codeGenerated.html.twig',$parameters);
    }

      /**
     * @Route("/request/visaCard", name="visaCard")
     */
    public function visaCard(Request $request,TranslatorInterface $translator): Response
    {
        $trans=$this->trans->translation($request,$translator);

        
        $parameters['currency'] = "dollar";
        $parameters['currentPage'] = "visaCard";

        return $this->render('request/visaCard.html.twig',$parameters);
    }
}
