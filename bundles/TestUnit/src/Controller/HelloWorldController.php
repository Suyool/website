<?php

namespace TestUnit\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HelloWorldController extends AbstractController
{
    #[Route('/TestUnit/',name:'bundle_test')]
    public function index()
    {
        return $this->render('hello_world.html.twig');
    }
}