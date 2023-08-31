<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Support;
use Symfony\Component\HttpFoundation\Request;

class SupportController extends AbstractController
{

    private $mr;

    public function __construct(ManagerRegistry $mr)
    {
        $this->mr = $mr->getManager('default');
    }

    // /**
    //  * @Route("/support", name="app_support")
    //  */
    // public function index(): Response
    // {
    
    //     // $support = new Support;
    //     // $support
    //     //     ->setname("elie")
    //     //     ->setmail("yammouni@hotmail.com")
    //     //     ->setsubject("tst Db")
    //     //     ->setmessage("Testing Testing Testing");
    //     // $this->mr->persist($support);
    //     // $this->mr->flush();

    //     return $this->render('support/index.html.twig');
    // }

    /**
     * @Route("/support", name="app_support")
     */
    public function index(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $data = $request->request->all();

            $support = new Support();
            $support
                ->setName($data['name'])
                ->setMail($data['email'])
                ->setSubject($data['subject'])
                ->setMessage($data['message']);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($support);
            $entityManager->flush();
        }

        return $this->render('support/index.html.twig');
    }
}
