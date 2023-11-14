<?php

namespace App\Controller;

use App\Service\Gift2GamesService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class Gift2GamesController extends AbstractController
{
    /**
     * @Route("/gift2games", name="app_gift2_games")
     */
    public function index(): Response
    {
        $parameters['deviceType'] ="Android";
        return $this->render('gift2_games/index.html.twig', [
            'parameters' => $parameters
        ]);
//        return $this->render('gift2_games/index.html.twig', [
//            'controller_name' => 'Gift2GamesController',
//        ]);
    }

    /**
     * @Route("/gift2games/categories", name="app_g2g_categories")
     */
    public function getCategories(Gift2GamesService $gamesService)
    {
//        $SuyoolUserId = $this->session->get('suyoolUserId');
        $SuyoolUserId = 155;

        $results = $gamesService->getCategories();

        return new JsonResponse([
            'status' => $results['status'],
            'Payload' => $results['data'],
        ], 200);
    }

    /**
     * @Route("/gift2games/products/{categoryId}", name="app_g2g_products")
     */
    public function getProducts($categoryId, Gift2GamesService $gamesService)
    {
//        $SuyoolUserId = $this->session->get('suyoolUserId');
        $SuyoolUserId = 155;

        $results = $gamesService->getProducts($categoryId);

        return new JsonResponse([
            'status' => $results['status'],
            'Payload' => $results['data'],
        ], 200);
    }


}
