<?php
namespace App\Controller\Admin;

// src/Controller/QuestionsCategoriesController.php

use App\Entity\QuestionsCategory;
use App\Form\QuestionsCategoryType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\QuestionsCategoryRepository;


class QuestionsCategoriesController extends AbstractController
{
    /**
     * @Route("dashadmin/Questionscategories", name="admin_questions_categories")
     */
    public function index(QuestionsCategoryRepository $categoryRepository, Request $request, PaginatorInterface $paginator): Response
    {
        // Get all categories
        $queryBuilder = $categoryRepository->createQueryBuilder('c');
        $query = $queryBuilder->getQuery();

        // Paginate the results
        $categories = $paginator->paginate(
            $query,                      // Query to paginate
            $request->query->getInt('page', 1), // Get the current page from the request
            10                           // Number of items per page
        );

        return $this->render('Admin/faq/questionsCategories.html.twig', [
            'categories' => $categories,
        ]);
    }

    /**
     * @Route("dashadmin/categories/new", name="admin_categories_new")
     * @Route("dashadmin/categories/edit/{id}", name="admin_categories_edit", requirements={"id"="\d+"}, defaults={"id"=null})
     */
    public function create(Request $request, QuestionsCategory $category = null): Response
    {
        $isNewCategory = ($category === null);

        if ($isNewCategory) {
            $category = new QuestionsCategory();
        }

        $form = $this->createForm(QuestionsCategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($category);
            $entityManager->flush();

            return $this->redirectToRoute('admin_questions_categories');
        }

        return $this->render('Admin/faq/create_questions_category.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
            'isNewCategory' => $isNewCategory,
        ]);
    }
}