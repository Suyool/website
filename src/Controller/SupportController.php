<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Support;
use App\Form\SupportType;
use App\Service\sendEmail;
use App\Service\SuyoolServices;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;

class SupportController extends AbstractController
{

    private $mr;
    private $mailerInterface;

    public function __construct(ManagerRegistry $mr, MailerInterface $mailerInterface)
    {
        $this->mr = $mr->getManager('default');
        $this->mailerInterface = $mailerInterface;
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
                // $sendEmail = new sendEmail($this->mailerInterface);
                // $sendEmail->sendEmail($support->getmail(),'contact@suyool.com','anthony.saliban@gmail.com',$support->getname() . " " . $support->getPhoneNumber(),$support->getmessage());

                $htmlContent = <<<HTML
                            <!DOCTYPE html>
                            <html>
                            <head>
                                <title>Contact Us</title>
                            </head>
                            <body>
                                <div style="background-color: #f5f5f5; padding: 20px; font-family: Arial, sans-serif;">
                                    <div style="background-color: #ffffff; padding: 20px; border-radius: 5px; box-shadow: 0px 2px 5px 0px #ccc;">
                                        <h2>Contact Us</h2>
                                        <p><strong>Email:</strong> <a href="mailto:{$support->getmail()}">{$support->getmail()}</a></p>
                                        <p><strong>Phone Number:</strong> {$support->getPhoneNumber()}</p>
                                        <p><strong>Message:</strong> {$support->getmessage()}</p>
                                    </div>
                                </div>
                            </body>
                            </html>
                            HTML;

                $subject = "Support";
                $to="Contact@suyool.com";
                // $to = "eyammouny@gmail.com";
                $plainTextContent = $htmlContent;
                $attachmentName = "";
                $attachmentsBase64 = "";
                $fromEmail = $support->getmail();
                $fromName = $support->getname();
                $flag = 1;
                $channelID = 0;

                $suyoolServices = new SuyoolServices();
                $response = $suyoolServices->sendDotNetEmail($subject, $to, $plainTextContent, $attachmentName, $attachmentsBase64, $fromEmail, $fromName, $flag, $channelID);
                if ($response == true) {
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($support);
                    $entityManager->flush();
                }else{
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($support);
                    $entityManager->flush();
                    // die("no db");
                }

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
