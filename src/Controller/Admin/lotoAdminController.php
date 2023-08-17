<?php

namespace App\Controller\Admin;

use App\Controller\Admin\ConfigureMenuItems\ConfigureMenuItems;
use App\Entity\Loto\loto;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class lotoAdminController extends AbstractDashboardController
{

    /**
     * @Route("/loto", name="admin_loto")
     */
    public function index(): Response
    {
        
        // dd("ok");
        // $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        // return $this->redirect($adminUrlGenerator->setController(DefaultController::class)->generateUrl());

        return $this->render('Admin/Loto/loto.html.twig');

    }

    

    
    public function configureMenuItems(): iterable
    {
        $configureMenuItems = new ConfigureMenuItems();
        return $configureMenuItems->configureMenuItems();
    }
}
