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
     * @Route("dashadmin/touch/prepaid", name="admin_touch_prepaid")
     */
    public function getPrepaid(Request $request,PaginatorInterface $paginator): Response
    {
        $emailSubscribersRepository = $this->mr->getRepository(Prepaid::class);
        $allSubscribersQuery = $emailSubscribersRepository->createQueryBuilder('pr')
            ->getQuery();

        $pagination = $paginator->paginate(
            $allSubscribersQuery,  // Query to paginate
            $request->get('page', 1),   // Current page number
            15              // Records per page
        );
        return $this->render('Admin/Touch/prepaid.html.twig', [
            'prepaids' => $pagination,
        ]);
    }

    /**
     * @Route("dashadmin/touch/postpaidRequest", name="admin_touch_postpaidRequest")
     */
    public function getPostPaidRequest(Request $request,PaginatorInterface $paginator): Response
    {
        $emailSubscribersRepository = $this->mr->getRepository(PostpaidRequest::class);
        $allSubscribersQuery = $emailSubscribersRepository->createQueryBuilder('ppr')
            ->getQuery();

        $pagination = $paginator->paginate(
            $allSubscribersQuery,  // Query to paginate
            $request->get('page', 1),   // Current page number
            15              // Records per page
        );
        return $this->render('Admin/Touch/postpaidRequest.html.twig', [
            'postpaidRequests' => $pagination,
        ]);
    }

    /**
     * @Route("dashadmin/touch/postpaid", name="admin_touch_postpaid")
     */
    public function getPostpaid(Request $request,PaginatorInterface $paginator): Response
    {
        $emailSubscribersRepository = $this->mr->getRepository(Postpaid::class);
        $allSubscribersQuery = $emailSubscribersRepository->createQueryBuilder('pd')
            ->getQuery();

        $pagination = $paginator->paginate(
            $allSubscribersQuery,  // Query to paginate
            $request->get('page', 1),   // Current page number
            15              // Records per page
        );
        return $this->render('Admin/Touch/postpaid.html.twig', [
            'postpaids' => $pagination,
        ]);
    }

    /**
     * @Route("dashadmin/touch/orders", name="admin_touch_orders")
     */
    public function getOrders(Request $request,PaginatorInterface $paginator): Response
    {
        $emailSubscribersRepository = $this->mr->getRepository(Order::class);
        $allSubscribersQuery = $emailSubscribersRepository->createQueryBuilder('o')
            ->getQuery();

        $pagination = $paginator->paginate(
            $allSubscribersQuery,  // Query to paginate
            $request->get('page', 1),   // Current page number
            15              // Records per page
        );
        return $this->render('Admin/Touch/orders.html.twig', [
            'orders' => $pagination,
        ]);
    }

    /**
     * @Route("dashadmin/touch/logs", name="admin_touch_logs")
     */
    public function getLogs(Request $request,PaginatorInterface $paginator): Response
    {
        $emailSubscribersRepository = $this->mr->getRepository(Logs::class);
        $allSubscribersQuery = $emailSubscribersRepository->createQueryBuilder('l')
            ->getQuery();

        $pagination = $paginator->paginate(
            $allSubscribersQuery,  // Query to paginate
            $request->get('page', 1),   // Current page number
            15              // Records per page
        );
        return $this->render('Admin/Touch/logs.html.twig', [
            'logs' => $pagination,
        ]);
    }
}

