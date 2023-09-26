<?php

namespace App\Controller;

use App\Repository\QuestionRepository;
use App\Repository\QuestionsCategoryRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HelpCenterController extends AbstractController
{
    public function __construct(ManagerRegistry $managerRegistry){

    }

    /**
     * @Route("/help-center", name="help_center")
     */
    public function index(QuestionsCategoryRepository $categoryRepository){
        $categories = $categoryRepository->findQuestionsByCategories();
//        dd($categories);
        return $this->render('helpCenter/index.html.twig', [
            'categories' => $categories,
        ]);
    }

}