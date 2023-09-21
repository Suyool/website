<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AlfaEmployeeController extends AbstractController
{
    #[Route('/alfa-employee', name: 'app_alfa_employee')]
    public function index(): Response
    {
       return $this->render('alfa_employee/index.html.twig');
    }
}
