<?php


namespace App\Controller\Admin;


use App\Entity\Gift2Games\Logs;
use App\Entity\Gift2Games\Order;
use App\Entity\Gift2Games\Product;
use App\Form\SearchAlfaOrdersForm;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class Gift2GamesController extends AbstractController
{
    private $mr;


    public function __construct(ManagerRegistry $mr)
    {
        $this->mr = $mr->getManager('gift2games');

    }

    /**
     * @Route("admin/gift2games/prepaid", name="admin_gift2games_products")
     */
    public function getProducts(Request $request,PaginatorInterface $paginator): Response
    {

        $query = $this->mr->getRepository(Product::class)->findAll();

        // Paginate the results
        $products = $paginator->paginate(
            $query,                      // Query to paginate
            $request->query->getInt('page', 1), // Get the current page from the request
            10                           // Number of items per page
        );

        return $this->render('Admin/Gift2games/products.html.twig', [
            'products' => $products,
        ]);
    }


    /**
     * @Route("admin/gift2games/orders", name="admin_gift2games_orders")
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
        return $this->render('Admin/Gift2games/orders.html.twig', [
            'pagination' => $pagination,
            'form'=>$AlfaSearchForm
        ]);
    }


    /**
     * @Route("admin/gift2games/logs", name="admin_gift2games_logs")
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
        return $this->render('Admin/Gift2games/logs.html.twig', [
            'logs' => $pagination,
        ]);
    }
}