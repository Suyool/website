<?php

namespace App\Controller;

use App\Translation\translation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class AlfaEmployeeController extends AbstractController
{

    private $trans;

    public function __construct(translation $trans)
    {
        $this->trans = $trans;
    }
    #[Route('/alfa-employee', name: 'app_alfa_employee')]
    public function index(Request $request,TranslatorInterface $translatorInterface): Response
    {
        $parameters = $this->trans->translation($request, $translatorInterface);
        return $this->render('alfa_employee/index.html.twig',$parameters);
    }
}
