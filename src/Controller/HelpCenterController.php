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
use Symfony\Component\HttpFoundation\Session\SessionInterface;
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
    public function index(QuestionsCategoryRepository $categoryRepository,SessionInterface $session)
    {
        $type = $this->request->query->get('type-id', 1);
        if ($type == 1) {
            $session->set('user_type', 1);
            $infoSections = [
                [
                    'image' => 'build/images/helpCenter/how-to-personal.svg',
                    'title' => 'How-To Guides',
                    'text' => 'Master your personal Suyool account with step-by-step how-to guides.',
                ],
                [
                    'image' => 'build/images/helpCenter/what-if-personal.svg',
                    'title' => 'What If’s',
                    'text' => 'Find answers to common scenarios and questions in our "What If" section.',
                ],
                [
                    'image' => 'build/images/helpCenter/policies-personal.svg',
                    'title' => 'Policies',
                    'text' => "Understand Suyool's policies for a secure and compliant experience.",
                ],
                [
                    'image' => 'build/images/helpCenter/tables-of-fees-personal.svg',
                    'title' => 'Table of Fees',
                    'text' => "Get clear cost insights with Suyool's Table of Fees.",
                ],
            ];
        }elseif ($type == 2) {
            $session->set('user_type', 2);
            $infoSections = [
                [
                    'image' => 'build/images/helpCenter/how-to-corporate.svg',
                    'title' => 'How-To Guides',
                    'text' => 'Streamline your corporate Suyool experience with our comprehensive how-to guides.',
                ],
                [
                    'image' => 'build/images/helpCenter/what-if-corporate.svg',
                    'title' => 'What If’s',
                    'text' => 'Explore tailored solutions for corporate scenarios and questions in our "What If" section.',
                ],
                [
                    'image' => 'build/images/helpCenter/policies-corporate.svg',
                    'title' => 'Policies',
                    'text' => "Gain insight into Suyool's corporate policies for a secure and compliant business environment.",
                ],
                [
                    'image' => 'build/images/helpCenter/tables-of-fees-corporate.svg',
                    'title' => 'Table of Fees',
                    'text' => "Access a breakdown of corporate fees and costs with Suyool's Table of Fees.",
                ],
            ];
        }

        $title="Help Center";
        $desc="How can we help you?";
        $categories = $categoryRepository->findQuestionsByCategories($type);

        return $this->render('helpCenter/index.html.twig', [
            'categories' => $categories,
            'type' => $type,
            'desc'=>$desc,
            'title'=>$title,
            'infoSections' => $infoSections, // Pass the dynamic section data to the template

        ]);
    }

    /**
     * @Route("/category/{id}/{questionId}/{question}", name="category_show")
     */
    public function showCategory($id, $questionId,$question, QuestionsCategoryRepository $categoryRepository, QuestionRepository $questionsRepository,SessionInterface $session)
    {
        $type = $session->get('user_type',1);
        // Fetch the category by ID
        $category = $this->getDoctrine()
            ->getRepository(QuestionsCategory::class)
            ->find($id);

        if (!$category) {
            throw $this->createNotFoundException('Category not found');
        }
//        $type = $this->request->query->get('type-id', 1);
        $type = $session->get('user_type',1);

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
    public function searchCategory(QuestionRepository $questionsRepository, QuestionsCategoryRepository $categoryRepository, Request $request,SessionInterface $session)
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

            $type = $session->get('user_type',1);

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