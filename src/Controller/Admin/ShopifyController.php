<?php

namespace App\Controller\Admin;

use App\Entity\Shopify\Logs;
use App\Entity\Shopify\MerchantCredentials;
use App\Entity\Shopify\Orders;
use App\Entity\Shopify\OrdersTest;
use App\Entity\Shopify\RequestedData;
use App\Entity\Shopify\Session;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShopifyController extends AbstractController
{
    private $mr;


    public function __construct(ManagerRegistry $mr)
    {
        $this->mr = $mr->getManager('Shopify');

    }

    /**
     * @Route("dashadmin/shopify/credentials", name="admin_shopify_credentials")
     */
    public function credentials(Request $request,PaginatorInterface $paginator): Response
    {
        $emailSubscribersRepository = $this->mr->getRepository(MerchantCredentials::class);
        $allSubscribersQuery = $emailSubscribersRepository->createQueryBuilder('c')
            ->getQuery();

        $pagination = $paginator->paginate(
            $allSubscribersQuery,  // Query to paginate
            $request->get('page', 1),   // Current page number
            15              // Records per page
        );
        return $this->render('Admin/Shopify/credentials.html.twig', [
            'credentials' => $pagination,
        ]);
    }

    /**
     * @Route("dashadmin/shopify/orders", name="admin_shopify_orders")
     */
    public function orders(Request $request,PaginatorInterface $paginator): Response
    {
        $emailSubscribersRepository = $this->mr->getRepository(Orders::class);
        $allSubscribersQuery = $emailSubscribersRepository->createQueryBuilder('o')
            ->getQuery();

        $pagination = $paginator->paginate(
            $allSubscribersQuery,  // Query to paginate
            $request->get('page', 1),   // Current page number
            15              // Records per page
        );
        return $this->render('Admin/Shopify/orders.html.twig', [
            'orders' => $pagination,
        ]);
    }

    /**
     * @Route("dashadmin/shopify/ordersTest", name="admin_shopify_ordersTest")
     */
    public function ordersTest(Request $request,PaginatorInterface $paginator): Response
    {
        $emailSubscribersRepository = $this->mr->getRepository(OrdersTest::class);
        $allSubscribersQuery = $emailSubscribersRepository->createQueryBuilder('ot')
            ->getQuery();

        $pagination = $paginator->paginate(
            $allSubscribersQuery,  // Query to paginate
            $request->get('page', 1),   // Current page number
            15              // Records per page
        );
        return $this->render('Admin/Shopify/ordersTest.html.twig', [
            'orders' => $pagination,
        ]);
    }

    /**
     * @Route("dashadmin/shopify/session", name="admin_shopify_session")
     */
    public function session (Request $request,PaginatorInterface $paginator): Response
    {
        $emailSubscribersRepository = $this->mr->getRepository(Session::class);
        $allSubscribersQuery = $emailSubscribersRepository->createQueryBuilder('s')
            ->getQuery();

        $pagination = $paginator->paginate(
            $allSubscribersQuery,  // Query to paginate
            $request->get('page', 1),   // Current page number
            15              // Records per page
        );
        return $this->render('Admin/Shopify/sessions.html.twig', [
            'sessions' => $pagination,
        ]);
    }

    /**
     * @Route("dashadmin/shopify/requestedData", name="admin_shopify_requestedData")
     */
    public function requestedData (Request $request,PaginatorInterface $paginator): Response
    {
        $emailSubscribersRepository = $this->mr->getRepository(RequestedData::class);
        $allSubscribersQuery = $emailSubscribersRepository->createQueryBuilder('rq')
            ->getQuery();

        $pagination = $paginator->paginate(
            $allSubscribersQuery,  // Query to paginate
            $request->get('page', 1),   // Current page number
            15              // Records per page
        );
        return $this->render('Admin/Shopify/requestedData.html.twig', [
            'requestedData' => $pagination,
        ]);
    }

    /**
     * @Route("dashadmin/shopify/logs", name="admin_shopify_logs")
     */
    public function logs (Request $request,PaginatorInterface $paginator): Response
    {
        $emailSubscribersRepository = $this->mr->getRepository(Logs::class);
        $allSubscribersQuery = $emailSubscribersRepository->createQueryBuilder('l')
            ->getQuery();

        $pagination = $paginator->paginate(
            $allSubscribersQuery,  // Query to paginate
            $request->get('page', 1),   // Current page number
            15              // Records per page
        );
        return $this->render('Admin/Shopify/logs.html.twig', [
            'logs' => $pagination,
        ]);
    }
}

