<?php

namespace App\Controller;

use App\Entity\Rates;
use App\Utils\Helper;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     *
     * @param Request $request
     * @param TranslatorInterface $translator
     * @return Response
     * @throws \Psr\Cache\InvalidArgumentException
     */

    public function indexAction(Request $request, TranslatorInterface $translator)
    {
        // Get the locale from the URL parameter
    $locale = $request->query->get('lang');
    // dd($locale);

    // Set the locale for the translator
    if(isset($locale)){
    $translator->setLocale($locale);
    setcookie('lang', $locale, time() + (86400 * 30), "/");
    }
    if(isset($_COOKIE['lang'])){
        $translator->setLocale($_COOKIE['lang']);
    }
        return $this->render('base.html.twig');
    }
}