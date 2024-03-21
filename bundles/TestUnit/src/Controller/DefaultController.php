<?php

namespace TestUnit\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    private $params;

    #[Route('/TestUnit/',name:'bundle_test')]
    public function index()
    {
        // return new JsonResponse([
        //     'status'=>true,
        //     'message'=>'My first bundle'
        // ],200);
        return $this->render('@TestUnitBundle/hello_world.html.twig');
    }
}