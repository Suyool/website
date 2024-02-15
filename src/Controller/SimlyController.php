<?php

namespace App\Controller;


use App\Service\Memcached;
use App\Service\NotificationServices;
use App\Service\SimlyServices;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Response;

class SimlyController extends AbstractController
{
    private $mr;
    private $params;
    public $cipher_algorithme = "AES128";
    public $key = "SY1X24elh9eG3fpOaHcWlQ9h2bHaqimdIDoyoOaFoi0rukAj3Z";
    public $iv = "fgu26y9e43wc8dj2";
    private $session;
    private $Memcached;

    public function __construct(ManagerRegistry $mr, $certificate, $hash_algo, ParameterBagInterface $params, SessionInterface $sessionInterface, SimlyServices $simlyServices, Memcached $memcached)
    {
        // $this->mr = $mr->getManager('simly');
        // $this->params = $params;
        // $this->session = $sessionInterface;
        // $this->Memcached = $memcached;
    }

    /**
     * @Route("/simly", name="app_simly")
     */
    public function index(NotificationServices $notificationServices)
    {
        $parameters['deviceType'] = 'Android';
        dd($parameters);

        // return $this->render('simly/index.html.twig', [
        //     'parameters' => $parameters
        // ]);
    }

    /**
     * @Route("/simly/api", name="app_simly_api")
     */
    public function API(SimlyServices $simlyServices)
    {
        $authenticationResult = $simlyServices->Authentication();

        $response = new Response(json_encode($authenticationResult['data']));
        $response->headers->set('Content-Type', 'application/json');
        $response->setStatusCode($authenticationResult['code']);

        return $response;
    }
}
