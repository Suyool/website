<?php
namespace App\Controller\Admin;

// src/Controller/Admin/QuestionsController.php
use App\Entity\Question;
use App\Form\QuestionFilterType;
use App\Form\QuestionType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class QuestionsController extends AbstractController
{
    /**
     * @Route("dashadmin/questions", name="admin_questions")
     */
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        // Create a form instance for the category filter
        $filterForm = $this->createForm(QuestionFilterType::class);
        $filterForm->handleRequest($request);

        $queryBuilder = $this->getDoctrine()
            ->getRepository(Question::class)
            ->createQueryBuilder('q')
            ->select('q.id', 'q.question', 'SUBSTRING(q.answer, 1, 150) AS answer', 'category.name AS categoryName')
            ->leftJoin('q.questionsCategory', 'category')
            ->orderBy('category.name', 'ASC')
            ->addOrderBy('q.id', 'ASC');

        // Get the category filter value from the form
        $categoryFilter = $filterForm->get('category')->getData();

        if ($categoryFilter) {
            // Apply the category filter to the query
            $queryBuilder
                ->andWhere('category = :categoryFilter')
                ->setParameter('categoryFilter', $categoryFilter);
        }

        $query = $queryBuilder->getQuery();

        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1), // Get the current page from the request
            10 // Number of items per page
        );

        return $this->render('Admin/faq/questions.html.twig', [
            'pagination' => $pagination,
            'filterForm' => $filterForm->createView(), // Pass the filter form to the template
        ]);
    }

    /**
     * @Route("dashadmin/questions/new", name="admin_questions_new", methods={"GET","POST"})
     * @Route("dashadmin/questions/edit/{id}", name="admin_questions_edit", requirements={"id"="\d+"}, defaults={"id"=null})
     */
    public function create(Request $request, Question $question = null): Response
    {
        $isNewQuestion = ($question === null);

        if ($isNewQuestion) {
            $question = new Question();
        }

        $form = $this->createForm(QuestionType::class, $question);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($question);
            $entityManager->flush();

            return $this->redirectToRoute('admin_questions');
        }

        return $this->render('Admin/faq/create_question.html.twig', [
            'question' => $question,
            'form' => $form->createView(),
            'isNewQuestion' => $isNewQuestion,
        ]);
    }
}
