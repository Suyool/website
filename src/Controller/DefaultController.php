<?php

namespace App\Controller;

use App\Entity\Rates;
use App\Translation\translation;
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

    private $trans;

    public function __construct(translation $trans)
    {
        $this->trans=$trans;
    }

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
        $trans=$this->trans->translation($request,$translator);
        return $this->render('base.html.twig');
    }
}