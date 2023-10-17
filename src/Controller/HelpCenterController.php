<?php

namespace App\Controller;

use App\Entity\Question;
use App\Entity\QuestionsCategory;
use App\Repository\QuestionRepository;
use App\Repository\QuestionsCategoryRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HelpCenterController extends AbstractController
{
    private $request;

    public function __construct(ManagerRegistry $managerRegistry, RequestStack $request)
    {
        $this->request = $request->getCurrentRequest();
    }

    /**
     * @Route("/help-center", name="help_center")
     */
    public function index(QuestionsCategoryRepository $categoryRepository)
    {
        $type = $this->request->query->get('type-id', 1);
        $title="Help Center";
        $desc="How can we help you?";
        $categories = $categoryRepository->findQuestionsByCategories($type);
        return $this->render('helpCenter/index.html.twig', [
            'categories' => $categories,
            'type' => $type,
            'desc'=>$desc,
            'title'=>$title
        ]);
    }

    /**
     * @Route("/category/{id}/{questionId}/{question}", name="category_show")
     */
    public function showCategory($id, $questionId,$question, QuestionsCategoryRepository $categoryRepository, QuestionRepository $questionsRepository)
    {
        // Fetch the category by ID
        $category = $this->getDoctrine()
            ->getRepository(QuestionsCategory::class)
            ->find($id);

        if (!$category) {
            throw $this->createNotFoundException('Category not found');
        }
        $type = $this->request->query->get('type-id', 1);

        // Fetch the questions for the category
        $questions = $category->getQuestions();
        $nextCategories = $categoryRepository->getNextCategories($id,$type);

        $selectedQuestion = $questionsRepository->selectQuestionById($questionId);

        $questionsForNextCategories = [];
        foreach ($nextCategories as $nextCategory) {
            $questionsForNextCategory = $questionsRepository->getQuestionsForNextCategory($nextCategory);
            $questionsForNextCategories[$nextCategory->getId()] = $questionsForNextCategory;
        }

        return $this->render('helpCenter/show.html.twig', [
            'category' => $category,
            'question' => $selectedQuestion,
            'questions' => $questions,
            'nextCategories' => $nextCategories,
            'questionsForNextCategories' => $questionsForNextCategories,
            'type' => $category->getType(),
        ]);
    }

    /**
     * @Route("/questions/search", name="category_search", methods={"post", "get"})
     */
    public function searchCategory(QuestionRepository $questionsRepository, QuestionsCategoryRepository $categoryRepository, Request $request)
    {
        $query = $request->request->get('query');
        if($query){
            $results = $questionsRepository->searchQuestions($query, true);

            return $this->render('helpCenter/search-results.html.twig', [
                'results' => $results,
                'type' => 1
            ]);
        }else{
            $searchQuery = $this->request->query->get('query', "");
            $results = $questionsRepository->searchQuestions($searchQuery, false);

            $type = $this->request->query->get('type-id', 1);

            $categories = $categoryRepository->findQuestionsByCategories($type);

            return $this->render('helpCenter/search.html.twig', [
                'results' => $results,
                'type' => 1,
                'searchQuery' => $searchQuery,
                'categories' => $categories
            ]);
        }
    }

}