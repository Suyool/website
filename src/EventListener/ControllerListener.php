<?php

namespace App\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;

class ControllerListener
{
    protected $em;
    protected $twig;

    public function __construct(EntityManagerInterface $em,\Twig\Environment $twig)
    {
        $this->em = $em;
        $this->twig = $twig;
    }

    public function onKernelTerminate()
    {

    }

    public function onKernelController(ControllerEvent $event)
    {
        if(isset($_GET['test']))die($_SERVER['SERVER_ADDR']);

        $httpHostParams = explode(".",$_SERVER['HTTP_HOST']);
        $currentSubdomain = (isset($_SERVER['HTTP_HOST']))?array_shift($httpHostParams):"";

        $this->twig->addGlobal('is_protected', ($currentSubdomain == "protected"));

        
    }
}
