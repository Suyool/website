<?php

namespace App\Controller\Admin;

use App\Entity\Admin;
use App\Entity\Loto\LOTO_draw;
use App\Service\DashboardService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{

    private $LotoRepository;
    private $alfaRepository;
    private $touchRepository;
    private $supportRepository;
    private $paymentRepository;
    private $ogeroRepository;

    public function __construct(ManagerRegistry $mr)
    {
        $this->LotoRepository=$mr->getManager('loto');
        $this->alfaRepository=$mr->getManager('alfa');
        $this->touchRepository=$mr->getManager('touch');
        $this->supportRepository=$mr->getManager('default');
        $this->paymentRepository=$mr->getManager('topup');
        $this->ogeroRepository = $mr->getManager('ogero');
    }

    /**
     * @Route("/admin", name="admin_homepage")
     */
    public function indexAction(Request $request)
    {
        $dashboard = new DashboardService();
        $drawId=$this->LotoRepository->getRepository(LOTO_draw::class)->findOneBy([], ['drawdate' => 'DESC']);
        $loto=$dashboard->LotoDashboard($this->LotoRepository,$drawId->getdrawId());
        $alfa=$dashboard->AlfaDashboard($this->alfaRepository);
        $touch=$dashboard->TouchDashboard($this->touchRepository);
        $support=$dashboard->SupportDashboard($this->supportRepository);
        $payment=$dashboard->PaymentDashboard($this->paymentRepository);
        $ogero=$dashboard->OgeroDashboard($this->ogeroRepository);
        return $this->render('Admin/dashboard.html.twig', array(
            'loto'=>$loto,
            'alfa'=>$alfa,
            'touch'=>$touch,
            'support'=>$support,
            'payment'=>$payment,
            'ogero'=>$ogero
        ));
    }
}
