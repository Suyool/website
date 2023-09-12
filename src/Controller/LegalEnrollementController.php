<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LegalEnrollementController extends AbstractController
{
    /**
     * @Route("/legal_enrollment", name="app_legal_enrollment")
     */
    public function index(): Response
    {
        // $parameters['Test'] = "tst";
        $parameters['ENV'] = $_ENV['APP_ENV'];
        return $this->render('legal_enrollement/index.html.twig', [
            'parameters' => $parameters
        ]);
    }
}
