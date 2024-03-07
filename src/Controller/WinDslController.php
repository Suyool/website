<?php

namespace App\Controller;

use App\Entity\Windsl\Users;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class WinDslController extends AbstractController
{

    private $mr;
    
    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->mr=$managerRegistry->getManager("windsl");
    }

    /**
     * @Route("/windsl", name="app_windsl")
     */
    public function index(){
        $parameters = [];
        return $this->render('windsl/index.html.twig',[
            'parameters'=>$parameters
        ]);
    }

    /**
     * @Route("/windsl/login", name="app_windsl_login",methods={"POST"})
     */
    public function login(Request $request){
        
        $data=json_decode($request->getContent(false),true);
        if(isset($data['username']) && isset($data['password'])){
            $users = new Users();
            $users->setUsername($data['username'])
            ->setPassword(md5($data['password']));

            $this->mr->persist($users);
            $this->mr->flush();

            return new JsonResponse([
                'status'=>true,
                'userid'=>123
            ]);
        }

        return new JsonResponse([
            'status'=>false,
            'message'=>'Unauthorized'
        ],401);
    }
}
