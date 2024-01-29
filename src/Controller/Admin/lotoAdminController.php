<?php

namespace App\Controller\Admin;

use App\Controller\Admin\ConfigureMenuItems\ConfigureMenuItems;
use App\Entity\Loto\loto;
use App\Entity\Loto\LOTO_draw;
use App\Entity\Loto\order;
use App\Form\SearchLotoFormType;
use Doctrine\Persistence\ManagerRegistry;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class lotoAdminController extends AbstractController
{

    private $mr;
    private $pagination;
    private $request;

    public function __construct(ManagerRegistry $mr,PaginatorInterface $pagination,RequestStack $request)
    {
        $this->mr=$mr->getManager('loto');
        $this->pagination=$pagination;
        $this->request=$request->getCurrentRequest();
    }

    /**
     * @Route("admin/orders",name="admin_loto_orders")
     */
    public function index(): Response
    {

        $orders=$this->mr->getRepository(order::class)->OrderSubscription();
        // dd($orders);

        $currentPage = $this->request->query->get('page', 1);


        $form=$this->createForm(SearchLotoFormType::class);

        $parameters['form']=$form->createView();

        $form->handleRequest($this->request);


        if ($form->isSubmitted()) {

            $searchQuery=$this->request->get('search_loto_form');
            $orders=$this->mr->getRepository(order::class)->OrderSubscription($searchQuery);

        }


        $pagination = $this->pagination->paginate(
            $orders,  // Query to paginate
            $currentPage,   // Current page number
            20             // Records per page
        );

        $parameters['pagination']=$pagination;

        return $this->render('Admin/Loto/loto.html.twig',$parameters);

    }

    /**
     * @Route("admin/tickets", name="admin_loto_tickets")
     */
    public function getTicketsPerOrders(){

        $id=$this->request->get('id');

        $currentPage=$this->request->get('page',1);
        $loto=$this->mr->getRepository(loto::class)->findLotoTicketByOrderId($id);

        // dd($loto);

        $pagination=$this->pagination->paginate(
            $loto,
            $currentPage,
            20
        );
        $parameters['pagination']=$pagination;

        return $this->render('Admin/Loto/tickets.html.twig',$parameters);
    }

     /**
     * @Route("admin/winningTickets", name="admin_loto_winningtickets")
     */
    public function getAllWinningTickets(){

        $winningTickets=$this->mr->getRepository(loto::class)->findAllWinningTickets();

        $parameters['winningTickets']=$winningTickets;

        return $this->render('Admin/Loto/winningtickets.html.twig',$parameters);
    }

     /**
     * @Route("admin/export", name="admin_export_to_excel")
     */
    public function exportToExcel()
    {
        $file_name="winners_".date('Y-m-d').".csv";
        $fields=array('OrderId','Suyooler','ticketId','Draw Number','Loto winning numbers','Zeed winning numbers','winLoto','winZeed','winningStatus','Created');
        $excelData = implode(",",array_values($fields)) . "\n";

        $winningTickets=$this->mr->getRepository(loto::class)->findAllWinningTickets();
        foreach($winningTickets as $winningTickets)
        {
            $lineData = array($winningTickets['id'],$winningTickets['fname']." ".$winningTickets['lname'],$winningTickets['ticketId'],$winningTickets['drawNumber'],$winningTickets['numbers'],$winningTickets['zeednumber1'],$winningTickets['winLoto'],$winningTickets['winZeed'],$winningTickets['winningStatus'],$winningTickets['created']);
            $excelData .= implode(",",array_values($lineData)) . "\n";
        }

        header("Content-Type:application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=\"" .basename($file_name) ."\"");
        echo $excelData;
        exit();
    }

     /**
     * @Route("admin/lastDrawTickets", name="admin_loto_lastdraw")
     */
    public function getAllLastDrawTickets(){

        $drawId=$this->mr->getRepository(LOTO_draw::class)->findOneBy([], ['drawdate' => 'DESC']);
        $currentPage=$this->request->get('page',1);
        $LastTickets=$this->mr->getRepository(loto::class)->findAllLastTickets($drawId->getdrawId());
        $pagination=$this->pagination->paginate(
            $LastTickets,
            $currentPage,
            20
        );
        $parameters['pagination']=$pagination;

        return $this->render('Admin/Loto/lasttickets.html.twig',$parameters);
    }

}
