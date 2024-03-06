<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


class WinDslController extends AbstractController
{
    /**
     * @Route("/windsl", name="app_windsl")
     */
    public function index(){
        $parameters = [];
        return $this->render('windsl/index.html.twig',[
            'parameters'=>$parameters
        ]);
    }
}
