<?php

namespace App\Controller;

use App\AdminBundle\Form\Type\RateType;
use App\Entity\Rates;
use App\Utils\Helper;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     *
     * @param Request $request
     * @param TranslatorInterface $translator
     * @return Response
     * @throws \Psr\Cache\InvalidArgumentException
     */

    public function indexAction(Request $request, TranslatorInterface $translator)
    {
        $rateHistory = $this->getDoctrine()
            ->getRepository(Rates::class)
            ->getRateHistory();

        $rate = new Rates();

        if(!empty($rateHistory)) {
            $currentRate = $rateHistory[0];
            $rate->setSellRate($currentRate->getSellRate());
            $rate->setBuyRate($currentRate->getBuyRate());
        }else{
            $currentRate = false;
        }

        /* if(isset($id)) {
             $rate = $this->getDoctrine()
                 ->getRepository(Categories::class)
                 ->find($id);
         }*/

        $form = $this->createForm(RateType::class, $rate);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if(!$currentRate ||
                $rate->getSellRate() != $currentRate->getSellRate() ||
                $rate->getBuyRate() != $currentRate->getBuyRate()) {
                $rate = $form->getData();
                $rate->setDirection((!$currentRate || $rate->getSellRate() > $currentRate->getSellRate()) ? 1 : 2);

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($rate);
                $entityManager->flush();

                array_unshift($rateHistory, $rate);
            }
            //return $this->redirectToRoute("list_categories");
        }

        return $this->render('default/index.html.twig', [
            'form' => $form->createView(),
            'rateHistory'=>$rateHistory,
            'currentRate'=>$currentRate
        ]);
    }
}