<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class AreebaPaymentController extends AbstractController
{

    /**
     * @Route("/areeba/{success}", name="app_areeba")
     */
    public function areeba(Request $request,$success)
    {
        $parameters['success']=$success;
        $parameters['Currency']="LL";
        $parameters['Amount']="200000";
        return $this->render('areeba/index.html.twig',$parameters);
    }

}