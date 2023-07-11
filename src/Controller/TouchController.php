<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TouchController extends AbstractController
{
    /**
     * @Route("/touch", name="app_touch")
     */
    public function index(): Response
    {
        $parameters['Test'] = "tst";

        return $this->render('touch/index.html.twig', [
            'parameters' => $parameters
        ]);
    }
}