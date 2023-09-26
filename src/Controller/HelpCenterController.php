<?php

namespace App\Controller;

use App\Entity\Question;
use App\Entity\QuestionsCategory;
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

    /**
     * @Route("/category/{id}", name="category_show")
     */
    public function showCategory($id)
    {
        $type = $this->request->query->get('type-id', 1);

        // Fetch the category by ID
        $category = $this->getDoctrine()
            ->getRepository(QuestionsCategory::class)
            ->find($id);

        if (!$category) {
            throw $this->createNotFoundException('Category not found');
        }

        // Fetch the questions for the category
        $questions = $category->getQuestions();

        // Fetch the three categories after the current one
        $categoriesRepository = $this->getDoctrine()->getRepository(QuestionsCategory::class);
        $nextCategories = $categoriesRepository->createQueryBuilder('c')
            ->where('c.id > :categoryId') // Select categories with IDs greater than the current category's ID
            ->setParameter('categoryId', $id)
            ->orderBy('c.id', 'ASC') // Order by category ID in ascending order
            ->setMaxResults(3) // Limit to 3 categories
            ->getQuery()
            ->getResult();

        // Fetch the first three questions for each of the three categories
        $questionsRepository = $this->getDoctrine()->getRepository(Question::class);
        $questionsForNextCategories = [];
        foreach ($nextCategories as $nextCategory) {
            $questionsForNextCategory = $questionsRepository->createQueryBuilder('q')
                ->where('q.questionsCategory = :category')
                ->setParameter('category', $nextCategory)
                ->setMaxResults(3) // Limit to 3 questions
                ->getQuery()
                ->getResult();
            $questionsForNextCategories[$nextCategory->getId()] = $questionsForNextCategory;
        }

        return $this->render('helpCenter/show.html.twig', [
            'category' => $category,
            'questions' => $questions,
            'nextCategories' => $nextCategories,
            'questionsForNextCategories' => $questionsForNextCategories,
            'type' => $type,
        ]);
    }

}