<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Support;
use App\Form\SupportType;
use Symfony\Component\HttpFoundation\Request;

class SupportController extends AbstractController
{

    private $mr;

    public function __construct(ManagerRegistry $mr)
    {
        $this->mr = $mr->getManager('default');
    }

     /**
     * @Route("/contact_us", name="app_support")
     */
    public function index(Request $request): Response
    {
        $support = new Support();
        $form = $this->createForm(SupportType::class, $support);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($support);
                $entityManager->flush();
            }
        }

        return $this->render('support/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
