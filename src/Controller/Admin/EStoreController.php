<?php

namespace App\Controller\Admin;

use App\Entity\Estore\Company;
use App\Entity\Estore\Price;
use App\Entity\Estore\Product;

use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EStoreController extends AbstractController
{
    private $mr;


    public function __construct(ManagerRegistry $mr)
    {
        $this->mr = $mr->getManager('estore');

    }

    /**
     * @Route("dashadmin/estore/comapny", name="admin_estore_company")
     */
    public function getCompanies(Request $request,PaginatorInterface $paginator): Response
    {
        $emailSubscribersRepository = $this->mr->getRepository(Company::class);
        $allSubscribersQuery = $emailSubscribersRepository->createQueryBuilder('c')
            ->getQuery();

        $pagination = $paginator->paginate(
            $allSubscribersQuery,  // Query to paginate
            $request->get('page', 1),   // Current page number
            15              // Records per page
        );
        return $this->render('Admin/EStores/company.html.twig', [
            'companies' => $pagination,
        ]);
    }

    /**
     * @Route("dashadmin/estore/price", name="admin_estore_price")
     */
    public function getPrices(Request $request,PaginatorInterface $paginator): Response
    {
        $emailSubscribersRepository = $this->mr->getRepository(Price::class);
        $allSubscribersQuery = $emailSubscribersRepository->createQueryBuilder('p')
            ->getQuery();

        $pagination = $paginator->paginate(
            $allSubscribersQuery,  // Query to paginate
            $request->get('page', 1),   // Current page number
            15              // Records per page
        );
        return $this->render('Admin/EStores/price.html.twig', [
            'prices' => $pagination,
        ]);
    }

    /**
     * @Route("dashadmin/estore/products", name="admin_estore_products")
     */
    public function getProducts(Request $request,PaginatorInterface $paginator): Response
    {
        $emailSubscribersRepository = $this->mr->getRepository(Product::class);
        $allSubscribersQuery = $emailSubscribersRepository->createQueryBuilder('pt')
            ->getQuery();

        $pagination = $paginator->paginate(
            $allSubscribersQuery,  // Query to paginate
            $request->get('page', 1),   // Current page number
            15              // Records per page
        );
        return $this->render('Admin/EStores/products.html.twig', [
            'products' => $pagination,
        ]);
    }

}

