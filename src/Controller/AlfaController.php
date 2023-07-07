<?php

namespace App\Controller;

use App\Entity\Alfa\Order;
use App\Entity\Alfa\Postpaid;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AlfaController extends AbstractController
{

    private $mr;
    // private $session;
    // private $certificate;
    // private $hash_algo;

    public function __construct(ManagerRegistry $mr)
    {
        $this->mr = $mr->getManager('alfa');
        // $this->session = $session;
        // $this->certificate = $certificate;
        // $this->hash_algo = $hash_algo;
    }



    /**
     * @Route("/alfa", name="app_alfa")
     */
    public function index(): Response
    {

        $postpaid = $this->mr->getRepository(Postpaid::class)->findAll();
        $orders = $this->mr->getRepository(Order::class)->findAll();
        dd($orders);
        $parameters['Test'] = "tst";

        return $this->render('alfa/index.html.twig', [
            'parameters' => $parameters
        ]);
    }
}