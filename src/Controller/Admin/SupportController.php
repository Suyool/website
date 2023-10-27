<?php

namespace App\Controller\Admin;

use App\Entity\Support;
use App\Service\sendEmail;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
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
    public function index(Request $request,PaginatorInterface $paginator,MailerInterface $mailerInterface)
    {
        $sendEmail=new sendEmail($mailerInterface);
        if(isset($_POST['submit'])){
            $support=$this->mr->getRepository(Support::class)->findOneBy(['id'=>$_POST['id']]);
            $sendEmail->sendEmail('contact@suyool.com',$_POST['email'],'anthony.saliban@gmail.com','From Suyool',$_POST['answer']);
            $support->setreplied(1);
            $this->mr->persist($support);
            $this->mr->flush();
        }
        $support=$this->mr->getRepository(Support::class)->findBy([],['replied'=>'desc','id'=>'desc']);


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

