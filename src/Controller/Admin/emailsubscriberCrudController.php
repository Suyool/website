<?php

namespace App\Controller\Admin;

use App\Controller\Admin\ConfigureMenuItems\ConfigureMenuItems;
use App\Entity\emailsubscriber;
use Doctrine\Persistence\ManagerRegistry;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class emailsubscriberCrudController extends AbstractDashboardController
{
    private $mr;
    private $paginator;
    private $request;

    public function __construct(ManagerRegistry $mr, PaginatorInterface $paginator,RequestStack $request)
    {
        $this->mr = $mr->getManager('default');
        $this->paginator = $paginator;
        $this->request = $request->getCurrentRequest();

    }

    /**
     * @Route("/emailSubscribers", name="admin_email_subscribers")
     */
    public function index(): Response
    {
        $emailSubscribersRepository = $this->mr->getRepository(emailsubscriber::class)->findAll();
        $currentPage = $this->request->query->getInt('page', 1);

        $pagination = $this->paginator->paginate(
            $emailSubscribersRepository,  // Query to paginate
            $currentPage,   // Current page number
            15              // Records per page
        );

        return $this->render('Admin/EmailSubscribers/index.html.twig', [
            'subscribers' => $pagination,
        ]);
    }


    public function configureMenuItems(): iterable
    {
        $configureMenuItems = new ConfigureMenuItems();
        return $configureMenuItems->configureMenuItems();
    }
}

