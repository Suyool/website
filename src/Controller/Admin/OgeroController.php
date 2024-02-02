<?php

namespace App\Controller\Admin;


use App\Entity\Ogero\Landline;
use App\Entity\Ogero\LandlineRequest;
use App\Entity\Ogero\Order;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OgeroController extends AbstractController
{
    private $mr;


    public function __construct(ManagerRegistry $mr)
    {
        $this->mr = $mr->getManager('ogero');

    }

    /**
     * @Route("admin/ogero/landLine", name="admin_ogero_landLine")
     */
    public function getLandLine(Request $request,PaginatorInterface $paginator): Response
    {
        $landLineRepository = $this->mr->getRepository(Landline::class);
        $allLandlineQuery = $landLineRepository->createQueryBuilder('ln')
            ->getQuery();

        $pagination = $paginator->paginate(
            $allLandlineQuery,  // Query to paginate
            $request->get('page', 1),   // Current page number
            15              // Records per page
        );
        return $this->render('Admin/Ogero/landLine.html.twig', [
            'landLines' => $pagination,
        ]);
    }

    /**
     * @Route("admin/ogero/landLineRequest", name="admin_ogero_landLineReqest")
     */
    public function getLandLineRequest(Request $request,PaginatorInterface $paginator): Response
    {
        $landLineReqRepository = $this->mr->getRepository(LandlineRequest::class);
        $allLandLineReqQuery = $landLineReqRepository->createQueryBuilder('lnr')
            ->getQuery();

        $pagination = $paginator->paginate(
            $allLandLineReqQuery,  // Query to paginate
            $request->get('page', 1),   // Current page number
            15              // Records per page
        );
        return $this->render('Admin/Ogero/landLineRequest.html.twig', [
            'landLineRequests' => $pagination,
        ]);
    }

    /**
     * @Route("admin/ogero/orders", name="admin_ogero_orders")
     */
    public function getOrders(Request $request,PaginatorInterface $paginator): Response
    {
        $ordersRepository = $this->mr->getRepository(Order::class);
        //dd($ordersRepository);
        $allordersQuery = $ordersRepository->findBy([],['id'=>'DESC']);

        $pagination = $paginator->paginate(
            $allordersQuery,  // Query to paginate
            $request->get('page', 1),   // Current page number
            15              // Records per page
        );
        return $this->render('Admin/Ogero/orders.html.twig', [
            'orders' => $pagination,
        ]);
    }

}

