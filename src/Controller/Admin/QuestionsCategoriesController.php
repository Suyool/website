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
use Symfony\Component\String\Slugger\SluggerInterface;


class QuestionsCategoriesController extends AbstractController
{
    /**
     * @Route("admin/Questionscategories", name="admin_questions_categories")
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
     * @Route("admin/categories/new", name="admin_categories_new")
     * @Route("admin/categories/edit/{id}", name="admin_categories_edit", requirements={"id"="\d+"}, defaults={"id"=null})
     */
    public function create(Request $request, QuestionsCategory $category = null, SluggerInterface $slugger): Response
    {
        $isNewCategory = ($category === null);
        $existingImage = null;

        if ($isNewCategory) {
            $category = new QuestionsCategory();
        } else {
            // If editing, store the existing image to set it back if no new image is uploaded
            $existingImage = $category->getImage();
        }

        $form = $this->createForm(QuestionsCategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                // Handle file upload here (e.g., move the uploaded file to the desired location).
                // You can use the $slugger to generate a unique filename.
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                // Move the uploaded file to the desired directory
                try {
                    $imageFile->move(
                        $this->getParameter('kernel.project_dir') . '/public/images/questionsCategories',
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Handle the exception if necessary
                }

                // Set the image property to the new filename
                $category->setImage($newFilename);
            }elseif (!$isNewCategory && !$imageFile) {
                // When editing, if no new image is uploaded, set the existing image back
                $category->setImage($existingImage);
            }

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