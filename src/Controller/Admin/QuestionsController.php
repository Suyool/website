<?php
namespace App\Controller\Admin;

// src/Controller/Admin/QuestionsController.php
use App\Entity\Question;
use App\Entity\QuestionsPhoto;
use App\Form\QuestionFilterType;
use App\Form\QuestionsPhotoType;
use App\Form\QuestionType;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\String\Slugger\SluggerInterface;


class QuestionsController extends AbstractController
{
    /**
     * @Route("admin/questions", name="admin_questions")
     */
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        // Create a form instance for the category filter
        $filterForm = $this->createForm(QuestionFilterType::class);
        $filterForm->handleRequest($request);

        $queryBuilder = $this->getDoctrine()
            ->getRepository(Question::class)
            ->createQueryBuilder('q')
            ->select('q.id', 'q.question', 'q.answer', 'category.name AS categoryName,q.status')
            ->leftJoin('q.questionsCategory', 'category')
            ->OrderBy('q.id', 'ASC');

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
     * @Route("admin/questions/new", name="admin_questions_new", methods={"GET","POST"})
     * @Route("admin/questions/edit/{id}", name="admin_questions_edit", requirements={"id"="\d+"}, defaults={"id"=null})
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
            $question->setAnswer(htmlspecialchars_decode($question->getAnswer()));
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
    /**
     * @Route("admin/show_questions_photo", name="admin_show_questions_photo")
     */
    public function showQuestionPhoto(): Response
    {
        $em = $this->getDoctrine()->getManager();
        $photos = $em->getRepository(QuestionsPhoto::class)->findAll();
        return $this->render('Admin/faq/questionPhoto.html.twig', [
            'photos' => $photos,
        ]);
    }
    /**
     * @Route("admin/upload_questions_photo", name="admin_upload_questions_photo")
     */
    public function uploadPhoto(Request $request, QuestionsPhoto $question = null,SluggerInterface $slugger): Response
    {
        
        $isNewQuestion = ($question === null);

        if ($isNewQuestion) {
            $question = new QuestionsPhoto();
        }

        $form = $this->createForm(QuestionsPhotoType::class, $question);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                // Handle file upload here (e.g., move the uploaded file to the desired location).
                // You can use the $slugger to generate a unique filename.
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename=strtolower($originalFilename).".".$imageFile->guessExtension();
                // dd($safeFilename);
                // $safeFilename = $slugger->slug($safeFilename);
                    // dd($safeFilename);
                // Move the uploaded file to the desired directory
                try {
                    $imageFile->move(
                        $this->getParameter('kernel.project_dir') . '/public/images/questionsImages',
                        $safeFilename
                    );
                } catch (Exception $e) {
                    // Handle the exception if necessary
                }

                // Set the image property to the new filename
                $question->setImage($safeFilename);
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($question);
            $entityManager->flush();

            return $this->redirectToRoute('admin_show_questions_photo');
        }

        return $this->render('Admin/faq/upload_question_images.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}
