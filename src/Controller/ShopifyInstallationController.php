<?php

namespace App\Controller;

use App\Entity\Shopify\ShopifyInstallation;
use App\Form\ShopifyInstallationType;
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
        $this->mr = $mr->getManager('Shopify');
    }

    /**
     * @Route("/shopify-installation/", name="form_submit")
     */
    public function shopifyInstallation(Request $request): Response
    {
        $formData = new ShopifyInstallation();
        $form = $this->createForm(ShopifyInstallationType::class, $formData);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $existingEntity = $this->mr->getRepository(ShopifyInstallation::class)
                ->findOneBy(['domain' => $formData->getDomain()]);

            if ($existingEntity === null) {
                $this->mr->persist($formData);
            } else {
                $existingEntity->setAppKey($formData->getAppKey());
                $existingEntity->setAppPass($formData->getAppPass());
                $existingEntity->setAppSecret($formData->getAppSecret());
                $existingEntity->setShopCurrency($formData->getShopCurrency());
                $existingEntity->setMerchantId($formData->getMerchantId());
                $existingEntity->setCertificateKey($formData->getCertificateKey());
                $existingEntity->setIntegrationType($formData->getIntegrationType());
            }

            $this->mr->flush();

            return $this->redirectToRoute('form_submit');
        }

        return $this->render('shopify/installation.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

