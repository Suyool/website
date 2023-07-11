<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OgeroController extends AbstractController
{
    /**
     * @Route("/ogero", name="app_ogero")
     */
    public function index(): Response
    {
        $parameters['Test'] = "tst";

        return $this->render('ogero/index.html.twig', [
            'parameters' => $parameters
        ]);
    }
}