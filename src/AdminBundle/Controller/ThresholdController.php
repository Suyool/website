<?php

namespace App\AdminBundle\Controller;

use App\AdminBundle\Form\Type\ManagerType;
use App\Entity\Managers;
use App\Utils\Helper;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\AdminBundle\Form\Type\ThresholdType;
use App\Entity\Thresholds;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ThresholdController extends AbstractController
{
    /**
     * @Route("/thresholds", name="thresholds")
     *
     * @param Request $request
     * @param TranslatorInterface $translator
     * @return Response
     * @throws \Psr\Cache\InvalidArgumentException
     */

    public function indexAction(Request $request)
    {
        $thresholdHistory = $this->getDoctrine()
            ->getRepository(Thresholds::class)
            ->getThresholdHistory();

        $threshold = new Thresholds();
        if(!empty($thresholdHistory)) {
            $currentThreshold = $thresholdHistory[0];
            $threshold->setThreshold($currentThreshold->getThreshold());
        }else{
            $currentThreshold = false;
        }
        /* if(isset($id)) {
             $rate = $this->getDoctrine()
                 ->getRepository(Categories::class)
                 ->find($id);
         }*/

        $form = $this->createForm(ThresholdType::class, $threshold);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if(!$currentThreshold ||
                $threshold->getThreshold() != $currentThreshold->getThreshold()) {
                $rate = $form->getData();
                $rate->setDirection((!$currentThreshold || $threshold->getThreshold() > $currentThreshold->getThreshold()) ? 1 : 2);

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($rate);
                $entityManager->flush();

                array_unshift($thresholdHistory, $rate);
            }
            //return $this->redirectToRoute("list_categories");
        }

        return $this->render('@Admin/thresholds/index.html.twig', [
            'form' => $form->createView(),
            'thresholdHistory'=>$thresholdHistory,
            'currentThreshold'=>$currentThreshold
        ]);
    }
}