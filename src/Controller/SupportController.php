<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Support;
use App\Form\SupportType;
use App\Service\sendEmail;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;

class SupportController extends AbstractController
{

    private $mr;
    private $mailerInterface;

    public function __construct(ManagerRegistry $mr,MailerInterface $mailerInterface)
    {
        $this->mr = $mr->getManager('default');
        $this->mailerInterface=$mailerInterface;
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
                $sendEmail = new sendEmail($this->mailerInterface);
                $sendEmail->sendEmail($support->getmail(),'contact@suyool.com','anthony.saliban@gmail.com',$support->getname() . " " . $support->getPhoneNumber(),$support->getmessage());
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
        return $this->render('support/index.html.twig', [
            'form' => $form->createView(),
            'title' => $title,
            'desc' => $desc,
            'barBgColor' => 'barBlue'
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
