<?php

namespace App\Controller\Admin;

use App\Entity\emailsubscriber;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EmailsubscriberController extends AbstractController
{
    private $mr;


    public function __construct(ManagerRegistry $mr)
    {
        $this->mr = $mr->getManager('default');

    }

    /**
     * @Route("dashadmin/emailSubscribers", name="admin_email_subscribers")
     */
    public function index(Request $request,PaginatorInterface $paginator): Response
    {
        $emailSubscribersRepository = $this->mr->getRepository(emailsubscriber::class);
        $allSubscribersQuery = $emailSubscribersRepository->createQueryBuilder('s')
            ->getQuery();

        $pagination = $paginator->paginate(
            $allSubscribersQuery,  // Query to paginate
            $request->get('page', 1),   // Current page number
            15              // Records per page
        );
        return $this->render('Admin/EmailSubscribers/index.html.twig', [
            'subscribers' => $pagination,
        ]);
    }
}

