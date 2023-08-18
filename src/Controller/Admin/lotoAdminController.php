<?php

namespace App\Controller\Admin;

use App\Controller\Admin\ConfigureMenuItems\ConfigureMenuItems;
use App\Entity\Loto\loto;
use App\Entity\Loto\order;
use Doctrine\Persistence\ManagerRegistry;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class lotoAdminController extends AbstractDashboardController
{

    private $mr;

    public function __construct(ManagerRegistry $mr)
    {
        $this->mr=$mr;
    }

    /**
     * @Route("/loto", name="admin_loto")
     */
    public function index(): Response
    {

        $orders=$this->mr->getRepository(order::class)->OrderSubscription();
        // dd($orders);

        $parameters['orders']=$orders;

        // dd("ok");
        // $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        // return $this->redirect($adminUrlGenerator->setController(DefaultController::class)->generateUrl());

        return $this->render('Admin/Loto/loto.html.twig',$parameters);

    }

    

    
    public function configureMenuItems(): iterable
    {
        $configureMenuItems = new ConfigureMenuItems();
        return $configureMenuItems->configureMenuItems();
    }
}
