<?php

namespace App\Controller\Admin;

use App\Entity\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{

    /**
     * @Route("/dashadmin", name="admin_homepage")
     */
    public function indexAction(Request $request)
    {
        return $this->render('Admin/dashboard.html.twig', array());
    }
}
