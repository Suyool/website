<?php

namespace App\Controller\Admin;

use App\Controller\Admin\ConfigureMenuItems\ConfigureMenuItems;
use App\Entity\Loto\loto;
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


class lotoAdminController extends AbstractDashboardController
{

    private $mr;
    private $pagination;
    private $request;

    public function __construct(ManagerRegistry $mr,PaginatorInterface $pagination,RequestStack $request)
    {
        $this->mr=$mr;
        $this->pagination=$pagination;
        $this->request=$request->getCurrentRequest();
    }

    public function configureAssets(): Assets
    {
        return parent::configureAssets()->addWebpackEncoreEntry('admin');;

    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
                        ->setTitle('Suyool')
                        ->disableUrlSignatures(); 
    }

    /**
     * @Route("dashadmin/orders",name="admin_loto_orders")
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
            // dd("ok");
            // $searchQuery=$form->getData();
            // dd($searchQuery);
            $searchQuery=$this->request->get('search_loto_form');
            $orders=$this->mr->getRepository(order::class)->OrderSubscription($searchQuery);
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            // $task = $form->getData();

            // ... perform some action, such as saving the task to the database

        }


        // $parameters['orders']=$orders;

        $pagination = $this->pagination->paginate(
            $orders,  // Query to paginate
            $currentPage,   // Current page number
            20             // Records per page
        );

        // dd($pagination);
        // if($pagination->getItems())

        $parameters['pagination']=$pagination;
        // dd($parameters);
        // $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        // return $this->redirect($adminUrlGenerator->setController(DefaultController::class)->generateUrl());

        return $this->render('Admin/Loto/loto.html.twig',$parameters);

    }

    /**
     * @Route("dashadmin/tickets", name="admin_loto_tickets")
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
        // if($currentPage>1){
        //     dd($pagination);
        // }

        $parameters['pagination']=$pagination;

        return $this->render('Admin/Loto/tickets.html.twig',$parameters);
    }

    

    
    // public function configureMenuItems(): iterable
    // {
    //     $configureMenuItems = new ConfigureMenuItems();
    //     return $configureMenuItems->configureMenuItems();
    // }
}
