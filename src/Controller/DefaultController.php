<?php

namespace App\Controller;

use App\Entity\emailsubscriber;
use App\Entity\Managers;
use App\Entity\Rates;
use App\Translation\translation;
use App\Utils\Helper;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class DefaultController extends AbstractController
{
    private $trans;
    public function __construct(translation $trans)
    {
        $this->trans = $trans;
    }
    /**
     * @Route("/", name="homepage")
     *
     * @param Request $request
     * @param TranslatorInterface $translator
     * @return Response
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function indexAction(Request $request, TranslatorInterface $translator, EntityManagerInterface $em, MailerInterface $mailer)
    {
        $submittedToken = $request->request->get('token');

        $trans = $this->trans->translation($request, $translator);
        $message = '';
        if (isset($_POST['email'])) {

            if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                $message = "email invalid";
                return new JsonResponse(['success' => "Invalid Email", 'message' => $message]);
            } else {
                if ($_SERVER['REQUEST_METHOD'] == "POST" && $_POST['email'] != null && !$em->getRepository(emailsubscriber::class)->findOneBy(['email' => $_POST['email']])) {
                    $emailSubcriber = new emailsubscriber;
                    $emailSubcriber->setEmail($_POST['email']);
                    $em->persist($emailSubcriber);
                    $em->flush();
                    $message = "Email Added";
                    $email = (new TemplatedEmail())
                        ->from('contact@suyool.com')
                        ->to($_POST['email'])
                        ->subject('You are on the list and we\'re so excited! ' . "\u{1F60D}")
                        ->htmlTemplate('email/email.html.twig');
                    $mailer->send($email);
                    return new JsonResponse(['success' => true, 'message' => $message]);
                } else {
                    $message = "Email already exist";
                    return new JsonResponse(['success' => false, 'message' => $message]);
                }
            }
        }
        // return $this->render('homepage/CommingSoon.html.twig', [
        //     'message' => $message
        // ]);
        return $this->render('homepage/index.html.twig');
    }

    /**
     * @Route("/privacy", name="app_privacy")
     */
    public function privacy(Request $request)
    {
        $pdfPath = __DIR__ . '/../../Suyoolprivacypolicy.pdf';
        $response = new BinaryFileResponse($pdfPath);

        return $response;
    }

    /**
     * @Route("/privacy_policy", name="app_privacy_policy")
     */
    public function privacy_policy(Request $request)
    {
        return $this->render('policies/privacyPolicy.html.twig');
    }

    /**
     * @Route("/terms_and_conditions", name="app_terms_and_conditions")
     */
    public function terms_and_conditions(Request $request)
    {
        return $this->render('policies/termsConditions.html.twig');
    }

    /**
     * @Route("/mastercard", name="app_mastercard")
     */
    public function mastercard(Request $request)
    {
        $cardData = [
            [
                'imagePath' => 'build/images/invitation/top-section-invitation-image.png',
                'title' => 'Middle East & Levant Platinum Lounge Program',
                'points' => [
                    'Free access to 25+ airport lounges in multiple countries.',
                    'Business amenities like email, internet, phones, and more.*',
                    'Complimentary refreshments and snacks.',
                ],
                'learnMoreLink' => '/',
            ],
            [
                'imagePath' => 'build/images/invitation/top-section-invitation-image.png',
                'title' => 'Test OneTest OneTest OneTest OneTest OneTest One',
                'points' => [
                    'Free access to 25+ airport lounges in multiple countries.',
                    'Business amenities like email, internet, phones, and more.*',
                    'Complimentary refreshments and snacks.',
                ],
                'learnMoreLink' => '/',
            ],
        ];

        $lifeStyleData = [
            [
                'imagePath' => 'build/images/invitation/top-section-invitation-image.png',
                'title' => 'Middle East & Levant Platinum Lounge Program',
                'points' => [
                    'Free access to 25+ airport lounges in multiple countries.',
                    'Business amenities like email, internet, phones, and more.*',
                    'Complimentary refreshments and snacks.',
                ],
                'learnMoreLink' => '/',
            ],
            [
                'imagePath' => 'build/images/invitation/top-section-invitation-image.png',
                'title' => 'Test OneTest OneTest OneTest OneTest OneTest One',
                'points' => [
                    'Free access to 25+ airport lounges in multiple countries.',
                    'Business amenities like email, internet, phones, and more.*',
                    'Complimentary refreshments and snacks.',
                ],
                'learnMoreLink' => '/',
            ],
        ];

        return $this->render('homepage/mastercard.html.twig', [
            'cardData' => $cardData,
            'lifeStyleData' => $lifeStyleData,
        ]);
    }

    public function show()
    {
        return $this->render('ExceptionHandling.html.twig');
    }
}