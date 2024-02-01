<?php


namespace App\Controller\Admin;

use App\Entity\Gift2Games\Categories;
use App\Entity\Gift2Games\Logs;
use App\Entity\Gift2Games\Order;
use App\Entity\Gift2Games\Product;
use App\Entity\Gift2Games\Products;
use App\Entity\Gift2Games\Transaction;
use App\Form\ImportCsvType;
use App\Form\SearchAlfaOrdersForm;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\CsvProcessorService;

class Gift2GamesController extends AbstractController
{
    private $mr;


    public function __construct(ManagerRegistry $mr)
    {
        $this->mr = $mr->getManager('gift2games');

    }

    /**
     * @Route("admin/gift2games/products", name="admin_gift2games_products")
     */
    public function getProducts(Request $request,PaginatorInterface $paginator,CsvProcessorService $csvProcessor): Response
    {
        $productsRepository = $this->mr->getRepository(Products::class);
        $productsQuery = $productsRepository->createQueryBuilder('p')
            ->getQuery();
        $importForm = $this->createForm(ImportCsvType::class);

        $importForm->handleRequest($request);

        if ($importForm->isSubmitted() && $importForm->isValid()) {
            // Get the CSV file from the form
            $csvFile = $importForm->get('csvFile')->getData();

            // Process the CSV file and update the database
            $csvProcessor->processCsv($csvFile);

            return $this->redirectToRoute('admin_gift2games_products');
        }

        $products = $paginator->paginate(
            $productsQuery,
            $request->query->getInt('page', 1),
            15
        );

        return $this->render('Admin/Gift2games/products.html.twig', [
            'products' => $products,
            'importForm' => $importForm->createView(),
        ]);
    }

     /**
     * @Route("admin/gift2games/category", name="admin_gift2games_categories")
     */
    public function getCategory(Request $request,PaginatorInterface $paginator): Response
    {

        $query = $this->mr->getRepository(Categories::class)->findAll();
        // dd($query);

        // Paginate the results
        $products = $paginator->paginate(
            $query,                      // Query to paginate
            $request->query->getInt('page', 1), // Get the current page from the request
            10                           // Number of items per page
        );

        return $this->render('Admin/Gift2games/category.html.twig', [
            'products' => $products,
        ]);
    }


    /**
     * @Route("admin/gift2games/orders/{id}", name="admin_gift2games_orders", methods={"GET"})
     * @Route("admin/gift2games/orders", name="admin_gift2games_orders_list", methods={"GET"})
     */
    public function getOrders(Request $request, PaginatorInterface $paginator, $id = null): Response
    {
        if ($id !== null) {
            // Fetch the order details by ID
            $searchQuery = array('id' => $id);
            $order = $this->mr->getRepository(Order::class)->OrderSubscription(null,$searchQuery);
            $order = $order['0'];
            if (!$order) {
                throw $this->createNotFoundException('Order not found');
            }

            return $this->render('Admin/Gift2games/orders.html.twig', [
                'order' => $order,
                'form' => $this->createForm(SearchAlfaOrdersForm::class)->createView(),
            ]);
        }

        // Original code for listing orders
        $orders = $this->mr->getRepository(Order::class)->OrderSubscription();

        $form = $this->createForm(SearchAlfaOrdersForm::class);
        $AlfaSearchForm = $form->createView();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $searchQuery = $request->get('search_alfa_orders_form');
            $orders = $this->mr->getRepository(Order::class)->OrderSubscription(null, $searchQuery);
        }

        $pagination = $paginator->paginate(
            $orders,
            $request->get('page', 1),
            15
        );

        return $this->render('Admin/Gift2games/orders.html.twig', [
            'pagination' => $pagination,
            'form' => $AlfaSearchForm,
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

    /**
     * @Route("admin/gift2games/transactions", name="admin_gift2games_transactions")
     */
    public function getTransactions(Request $request,PaginatorInterface $paginator): Response
    {
        $transactionsRepository = $this->mr->getRepository(Transaction::class);
        $transactionsQuery = $transactionsRepository->createQueryBuilder('t')
            ->getQuery();

        $transactions = $paginator->paginate(
            $transactionsQuery,
            $request->query->getInt('page', 1),
            15
        );

        return $this->render('Admin/Gift2games/transactions.html.twig', [
            'transactions' => $transactions,
        ]);
    }

    /**
     * @Route("/admin/gift2games/orders/export-csv", name="export_csv")
     */
    public function exportCsv(): Response
    {
        $orders = $this->mr->getRepository(Order::class)->OrderSubscription();

        // Generate CSV content
        $csvContent = $this->generateCsvContent($orders);

        // Create a CSV response
        $response = new Response($csvContent);
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="orders.csv"');

        return $response;
    }
    /**
     * @Route("/admin/gift2games/products/export-csv", name="export_csv_product")
     */
    public function exportCsvProducts(): Response
    {
        $products = $this->mr->getRepository(Products::class)->findAll();

        // Generate CSV content
        $csvContent = $this->generateCsvContentProducts($products);

        // Create a CSV response
        $response = new Response($csvContent);
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="products.csv"');

        return $response;
    }

    /**
     * Generate CSV content from orders
     *
     * @param array $orders
     * @return string
     */
    private function generateCsvContent(array $orders): string
    {
        $csvContent = "Id,SuyoolUser,Status,Amount,Currency,TransId,Error,Created\n";

        foreach ($orders as $order) {
            $createdFormatted = ($order['created'] instanceof \DateTime)
                ? $order['created']->format('Y-m-d H:i:s')
                : $order['created'];

            $csvContent .= $this->generateCsvLine([
                $order['id'],
                $order['fname'] . ' ' . $order['lname'],
                $order['status'],
                $order['amount'],
                $order['currency'],
                $order['transId'],
                $order['error'],
                $createdFormatted,
            ]);
        }

        return $csvContent;
    }

    /**
     * Generate CSV content from orders
     *
     * @param array $products
     * @return string
     */
    private function generateCsvContentProducts(array $products): string
    {
        $csvContent = "id,productId,categoryId,title,image,sellPrice,price,discountRate,inStock,currency,canceled\n";

        foreach ($products as $product) {

            $csvContent .= $this->generateCsvLine([
                $product->getId(),
                $product->getProductId(),
                $product->getCategoryId(),
                $product->getTitle(),
                $product->getImage(),
                $product->getSellPrice(),
                $product->getPrice(),
                $product->getDiscountRate(),
                $product->getInStock(),
                $product->getCurrency(),
                $product->getCanceled(),
            ]);
        }

        return $csvContent;
    }

    private function generateCsvLine(array $data): string
    {
        $handle = fopen('php://memory', 'rw');
        fputcsv($handle, $data, ',');

        rewind($handle);
        $line = stream_get_contents($handle);
        fclose($handle);

        return $line;
    }

    /**
     * @Route("admin/gift2games/updateStatus/{id}", name="admin_gift2games_update_canceled")
     */
    public function canceled($id = null)
    {
        $product = $this->mr->getRepository(Products::class)->findOneBy(['id'=>$id]);
        $product->getCanceled() == true ? $product->setCanceled(false) : $product->setCanceled(true) ;
        $this->mr->persist($product);
        $this->mr->flush();

        return $this->redirectToRoute('admin_gift2games_products');
    }

    /**
     * @Route("admin/gift2games/update/{id}", name="admin_gift2games_update")
     */
    public function sellPrice($id)
    {
        $product = $this->mr->getRepository(Products::class)->findOneBy(['id'=>$id]);
        $product->setSellPrice($_POST['sellprice']);
        $this->mr->persist($product);
        $this->mr->flush();

        return $this->redirectToRoute('admin_gift2games_products');
    }

    /**
     * @Route("admin/gift2games/updateStatuscategory/{id}", name="admin_gift2games_update_canceled_cat")
     */
    public function canceledcat($id = null)
    {
        $product = $this->mr->getRepository(Categories::class)->findOneBy(['id'=>$id]);
        $product->getCanceled() == true ? $product->setCanceled(false) : $product->setCanceled(true) ;
        $this->mr->persist($product);
        $this->mr->flush();

        return $this->redirectToRoute('admin_gift2games_categories');
    }
}