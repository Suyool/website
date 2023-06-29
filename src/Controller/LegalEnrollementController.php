<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LegalEnrollementController extends AbstractController
{
    /**
     * @Route("/legal_enrollement", name="app_legal_enrollement")
     */
    public function index(): Response
    {
        $parameters['Test'] = "tst";

        return $this->render('legal_enrollement/index.html.twig',[
            'parameters' => $parameters
        ]);
    }
}
