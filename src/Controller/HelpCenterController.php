<?php

namespace App\Controller;

use App\Repository\QuestionRepository;
use App\Repository\QuestionsCategoryRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;

class HelpCenterController extends AbstractController
{
    private $request;
    public function __construct(ManagerRegistry $managerRegistry, RequestStack $request){
        $this->request=$request->getCurrentRequest();
    }

    /**
     * @Route("/help-center", name="help_center")
     */
    public function index(QuestionsCategoryRepository $categoryRepository){
        $type = $this->request->query->get('type-id', 1);

        //add the type later
        $categories = $categoryRepository->findQuestionsByCategories();
        //dd($categories);
        return $this->render('helpCenter/index.html.twig', [
            'categories' => $categories,
            'type' => $type
        ]);
    }

}