<?php

namespace App\Controller\Admin;

use App\Entity\Touch\Logs;
use App\Entity\Touch\Order;
use App\Entity\Touch\Postpaid;
use App\Entity\Touch\PostpaidRequest;
use App\Entity\Touch\Prepaid;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TouchController extends AbstractController
{
    private $mr;


    public function __construct(ManagerRegistry $mr)
    {
        $this->mr = $mr->getManager('touch');
    }

    /**
     * @Route("admin/touch/prepaid", name="admin_touch_prepaid")
     */
    public function getPrepaid(Request $request,PaginatorInterface $paginator): Response
    {
        $prepaid= $this->mr->getRepository(Prepaid::class)->findOneBy(['id'=>$request->query->get('prepaidId')]); 
        return $this->render('Admin/Alfa/prepaid.html.twig', [
            'prepaids' => $prepaid,
        ]);
    }

    /**
     * @Route("admin/touch/postpaidRequest", name="admin_touch_postpaidRequest")
     */
    public function getPostPaidRequest(Request $request,PaginatorInterface $paginator): Response
    {
        $emailSubscribersRepository = $this->mr->getRepository(PostpaidRequest::class)->findAll();
        $pagination = $paginator->paginate(
            $emailSubscribersRepository,  // Query to paginate
            $request->get('page', 1),   // Current page number
            15              // Records per page
        );
        return $this->render('Admin/Alfa/postpaidRequest.html.twig', [
            'postpaidRequests' => $pagination,
        ]);
    }

    /**
     * @Route("admin/touch/postpaid", name="admin_touch_postpaid")
     */
    public function getPostpaid(Request $request,PaginatorInterface $paginator): Response
    {
        $postpaid = $this->mr->getRepository(Postpaid::class)->findOneBy(['id'=>$request->query->get('postpaidId')]);
        return $this->render('Admin/Alfa/postpaid.html.twig', [
            'postpaids' => $postpaid,
        ]);
    }

    /**
     * @Route("admin/touch/orders", name="admin_touch_orders")
     */
    public function getOrders(Request $request,PaginatorInterface $paginator): Response
    {
        $orders=$this->mr->getRepository(Order::class)->OrderSubscription();

        $form=$this->createForm(SearchAlfaOrdersForm::class);

        $AlfaSearchForm=$form->createView();

        $form->handleRequest($request);


        if ($form->isSubmitted()) {

            $searchQuery=$request->get('search_alfa_orders_form');
            $orders=$this->mr->getRepository(order::class)->OrderSubscription(null,$searchQuery);

        }

        $pagination = $paginator->paginate(
            $orders,
            $request->get('page', 1),
            15
        );
        return $this->render('Admin/Alfa/orders.html.twig', [
            'pagination' => $pagination,
            'form'=>$AlfaSearchForm
        ]);
    }

    /**
     * @Route("admin/touch/ordersPost", name="admin_touch_ordersPost")
     */
    public function getOrdersPost(Request $request,PaginatorInterface $paginator): Response
    {
        $orders=$this->mr->getRepository(Order::class)->OrderSubscription('postpaid_id');

        $form=$this->createForm(SearchAlfaOrdersForm::class);

        $AlfaSearchForm=$form->createView();

        $form->handleRequest($request);


        if ($form->isSubmitted()) {

            $searchQuery=$request->get('search_alfa_orders_form');
            $orders=$this->mr->getRepository(order::class)->OrderSubscription('postpaid_id',$searchQuery);

        }

        $pagination = $paginator->paginate(
            $orders,
            $request->get('page', 1),
            15
        );
        return $this->render('Admin/Alfa/ordersPost.html.twig', [
            'pagination' => $pagination,
            'form'=>$AlfaSearchForm
        ]);
    }

    /**
     * @Route("admin/touch/ordersPre", name="admin_touch_ordersPre")
     */
    public function getOrdersPre(Request $request,PaginatorInterface $paginator): Response
    {
        $orders=$this->mr->getRepository(Order::class)->OrderSubscription('prepaid_id');

        $form=$this->createForm(SearchAlfaOrdersForm::class);

        $AlfaSearchForm=$form->createView();

        $form->handleRequest($request);


        if ($form->isSubmitted()) {

            $searchQuery=$request->get('search_alfa_orders_form');
            $orders=$this->mr->getRepository(order::class)->OrderSubscription('prepaid_id',$searchQuery);

        }

        $pagination = $paginator->paginate(
            $orders,
            $request->get('page', 1),
            15
        );
        return $this->render('Admin/Alfa/ordersPre.html.twig', [
            'pagination' => $pagination,
            'form'=>$AlfaSearchForm
        ]);
    }

    /**
     * @Route("admin/touch/logs", name="admin_touch_logs")
     */
    public function getLogs(Request $request,PaginatorInterface $paginator): Response
    {
        $emailSubscribersRepository = $this->mr->getRepository(Logs::class)->findAll();
        // dd($emailSubscribersRepository);
        $pagination = $paginator->paginate(
            $emailSubscribersRepository,  // Query to paginate
            $request->get('page', 1),   // Current page number
            15              // Records per page
        );
        return $this->render('Admin/Alfa/logs.html.twig', [
            'logs' => $pagination,
        ]);
    }
}

