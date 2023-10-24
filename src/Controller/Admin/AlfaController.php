<?php

namespace App\Controller\Admin;



use App\Entity\Alfa\Logs;
use App\Entity\Alfa\Order;
use App\Entity\Alfa\Postpaid;
use App\Entity\Alfa\PostpaidRequest;
use App\Entity\Alfa\Prepaid;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AlfaController extends AbstractController
{
    private $mr;


    public function __construct(ManagerRegistry $mr)
    {
        $this->mr = $mr->getManager('alfa');

    }

    /**
     * @Route("dashadmin/alfa/prepaid", name="admin_alfa_prepaid")
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
        return $this->render('Admin/Alfa/prepaid.html.twig', [
            'prepaids' => $pagination,
        ]);
    }

    /**
     * @Route("dashadmin/alfa/postpaidRequest", name="admin_alfa_postpaidRequest")
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
        return $this->render('Admin/Alfa/postpaidRequest.html.twig', [
            'postpaidRequests' => $pagination,
        ]);
    }

    /**
     * @Route("dashadmin/alfa/postpaid", name="admin_alfa_postpaid")
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
        return $this->render('Admin/Alfa/postpaid.html.twig', [
            'postpaids' => $pagination,
        ]);
    }

    /**
     * @Route("admin/alfa/orders", name="admin_alfa_orders")
     */
    public function getOrders(Request $request,PaginatorInterface $paginator): Response
    {
        $orders=$this->mr->getRepository(Order::class)->OrderSubscription();
        $pagination = $paginator->paginate(
            $orders,
            $request->get('page', 1),
            15
        );
        return $this->render('Admin/Alfa/orders.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * @Route("admin/alfa/ordersPost", name="admin_alfa_ordersPost")
     */
    public function getOrdersPost(Request $request,PaginatorInterface $paginator): Response
    {
        $orders=$this->mr->getRepository(Order::class)->OrderSubscription('postpaid_id');
        $pagination = $paginator->paginate(
            $orders,
            $request->get('page', 1),
            15
        );
        return $this->render('Admin/Alfa/ordersPost.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * @Route("admin/alfa/ordersPre", name="admin_alfa_ordersPre")
     */
    public function getOrdersPre(Request $request,PaginatorInterface $paginator): Response
    {
        $orders=$this->mr->getRepository(Order::class)->OrderSubscription('prepaid_id');
        $pagination = $paginator->paginate(
            $orders,
            $request->get('page', 1),
            15
        );
        return $this->render('Admin/Alfa/ordersPre.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * @Route("admin/alfa/logs", name="admin_alfa_logs")
     */
    public function getLogs(Request $request,PaginatorInterface $paginator): Response
    {
        $emailSubscribersRepository = $this->mr->getRepository(PostpaidRequest::class)->findBy(['suyoolUserId'=>$request->query->get('suyoolUserid')]);
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

