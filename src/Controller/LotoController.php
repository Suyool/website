<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class LotoController extends AbstractController
{
    /**
     * @Route("/loto", name="app_loto")
     */
    public function index(Request $request)
    {
        return $this->render('loto/index.html.twig');
    }
}