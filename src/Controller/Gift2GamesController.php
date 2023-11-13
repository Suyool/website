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

        $gamesService->getCategories();

        $result = [];
        return new JsonResponse([
            'status' => true,
            'data' => $result
        ], 200);
    }
}
