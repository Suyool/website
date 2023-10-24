<?php

namespace App\Controller\Admin;

use App\Entity\Admin;
use App\Service\DashboardService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{

    private $LotoRepository;
    private $alfaRepository;

    public function __construct(ManagerRegistry $mr)
    {
        $this->LotoRepository=$mr->getManager('loto');
        $this->alfaRepository=$mr->getManager('alfa');
    }

    /**
     * @Route("/admin", name="admin_homepage")
     */
    public function indexAction(Request $request)
    {
        $dashboard = new DashboardService();
        $loto=$dashboard->LotoDashboard($this->LotoRepository);
        $alfa=$dashboard->AlfaDashboard($this->alfaRepository);
        return $this->render('Admin/dashboard.html.twig', array(
            'loto'=>$loto,
            'alfa'=>$alfa
        ));
    }
}
