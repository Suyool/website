<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends AbstractController
{
    /**
     * @Route("/dashboard", name="admin_homepage")
     */
    public function indexAction(Request $request)
    {
        return $this->render('Admin/dashboard.html.twig', array());
    }

}