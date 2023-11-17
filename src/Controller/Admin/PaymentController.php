<?php

namespace App\Controller\Admin;

use App\Entity\topup\orders;
use App\Form\SearchPaymentForm;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PaymentController extends AbstractController
{
    private $mr;
    private $paginationInterface;

    public function __construct(ManagerRegistry $mr,PaginatorInterface $paginationInterface)
    {
        $this->mr = $mr->getManager('topup');
        $this->paginationInterface=$paginationInterface;
    }

    /**
     * @Route("admin/payment/orders", name="admin_payment_orders")
     */
    public function paymentOrders(Request $request)
    {
        $current_page=$request->get('page',1);
        $orders=$this->mr->getRepository(orders::class)->fetchAllPaymentDetails(array('status'=>'','transId'=>''));
        // dd($orders);

        $form=$this->createForm(SearchPaymentForm::class);

        $formrender=$form->createView();

        $form->handleRequest($request);


        if ($form->isSubmitted()) {
            $searchQuery=$request->get('search_payment_form');
            $orders=$this->mr->getRepository(orders::class)->fetchAllPaymentDetails($searchQuery);
        }

        $pagination=$this->paginationInterface->paginate(
            $orders,
            $current_page,
            25
        );

        return $this->render('Admin/Payment/orders.html.twig', [
            'orders' => $pagination,
            'form'=>$formrender
        ]);


    }
}

