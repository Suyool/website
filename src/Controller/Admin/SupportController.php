<?php

namespace App\Controller\Admin;

use App\Entity\Support;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SupportController extends AbstractController
{
    private $mr;

    public function __construct(ManagerRegistry $mr)
    {
        $this->mr = $mr->getManager('default');

    }

    /**
     * @Route("admin/support", name="admin_support")
     */
    public function index(Request $request,PaginatorInterface $paginator)
    {
        $support=$this->mr->getRepository(Support::class)->findAll();

        $pagination=$paginator->paginate(
            $support,
            $request->get('page', 1),
            10
        );

        return $this->render('Admin/support.html.twig',[
            'pagination'=>$pagination
        ]);

    }
}

