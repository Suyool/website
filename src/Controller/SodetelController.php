<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SodetelController extends AbstractController
{
    /**
     * @Route("/sodetel", name="sodetel")
     */
    public function index(): Response
    {
        $parameters['deviceType'] = "Android";
        return $this->render('sodetel/index.html.twig', [
            'controller_name' => 'SodetelController',
            'parameters' => $parameters,
        ]);
    }
}
