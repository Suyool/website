<?php

namespace App\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use App\Entity\Shopify\ShopifyInstallation;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShopifyInstallationController extends AbstractController
{
    private $mr;
    public function __construct(ManagerRegistry $mr)
    {
        $this->mr=$mr->getManager('Shopify');
    }
    /**
     * @Route("/shopify-installation/", name="form_submit")
     */
    public function shopifyInstallation(Request $request): Response
    {
        $entity = new ShopifyInstallation();

        $form = $this->createFormBuilder($entity)
            ->add('domain')
            ->add('appKey')
            ->add('appPass')
            ->add('appSecret')
            ->add('shopCurrency')
            ->add('merchantId')
            ->add('certificateKey')
            ->add('integrationType')
            ->add('save', SubmitType::class, ['label' => 'Submit'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager('Shopify');
            $entityManager->persist($entity);
            $entityManager->flush();

            return $this->redirectToRoute('form_submit');
        }

        return $this->render('shopify/installation.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
