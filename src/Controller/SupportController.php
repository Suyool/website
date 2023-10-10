<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Support;
use App\Form\SupportType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class SupportController extends AbstractController
{

    private $mr;

    public function __construct(ManagerRegistry $mr)
    {
        $this->mr = $mr->getManager('default');
    }

    /**
     * @Route("/contact-us", name="app_contact_us")
     */
    public function index(Request $request): Response
    {
        $support = new Support();
        $form = $this->createForm(SupportType::class, $support, array(
            'attr' => array(
                'id' => 'contactusForm'
            )
        ));

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($support);
                $entityManager->flush();
                return new JsonResponse([
                    'success' => true
                ]);
            }
        }
        $title = "Contact Us | Suyool";
        $desc = "Suyool team is available 24/7 ready to assist you.";
        $removeSection = '';

        return $this->render('support/index.html.twig', [
            'form' => $form->createView(),
            'title' => $title,
            'desc' => $desc,
            'removeSection' => $removeSection
        ]);
    }

    //    /**
    //     * @Route("/support", name="app_support")
    //     */
    public function support(Request $request): Response
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
