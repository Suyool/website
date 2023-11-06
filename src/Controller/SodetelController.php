<?php

namespace App\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class SodetelController extends AbstractController
{
    private $mr;
    private $hash_algo;
    private $certificate;
    private $notMr;
    private $params;
    public $cipher_algorithme = "AES128";
    public $key = "SY1X24elh9eG3fpOaHcWlQ9h2bHaqimdIDoyoOaFoi0rukAj3Z";
    public $iv = "fgu26y9e43wc8dj2"; //initiallization vector for decrypt
    private $session;

    public function __construct(ManagerRegistry $mr, $certificate, $hash_algo, ParameterBagInterface $params, SessionInterface $sessionInterface)
    {
        $this->mr = $mr->getManager('alfa');
        $this->hash_algo = $hash_algo;
        $this->certificate = $certificate;
        $this->notMr = $mr->getManager('notification');
        $this->params = $params;
        $this->session = $sessionInterface;
    }

    /**
     * @Route("/sodetel", name="app_sodetel")
     */
    public function index() {
        $parameters['deviceType'] = 'android';
        return $this->render('sodetel/index.html.twig', [
            'parameters' => $parameters
        ]);
    }

}