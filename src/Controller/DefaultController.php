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
                'imagePath' => 'build/images/platinumMastercard/one.png',
                'title' => 'Middle East & Levant Platinum Lounge Program',
                'points' => [
                    'Free access to 25+ airport lounges in multiple countries.',
                    'Business amenities like email, internet, phones, and more.*',
                    'Complimentary refreshments and snacks.',
                ],
                'learnMoreLink' => '/',
            ],
            [
                'imagePath' => 'build/images/platinumMastercard/two.png',
                'title' => 'Airline and Travel discount with Cleartrip.com',
                'points' => [
                    'Get 8% off when you book your flight or hotel on Cleartrip using your Platinum Mastercard card.',
                ],
                'learnMoreLink' => '/',
            ],
            [
                'imagePath' => 'build/images/platinumMastercard/three.png',
                'title' => 'Hotel discount and cashback with IHG',
                'points' => [
                    '15% discount on ‘Best Available Rate’',
                    'Free welcome drink upon arrival*',
                    'Complimentary late checkout (by 4:00 PM)*',
                ],
                'learnMoreLink' => '/',
            ],
            [
                'imagePath' => 'build/images/platinumMastercard/foor.png',
                'title' => 'Hotel discount and cashback with booking.com',
                'points' => [
                    'Book your next holiday on booking.com with Mastercard for up to 10% money back.',
                ],
                'learnMoreLink' => '/',
            ],
            [
                'imagePath' => 'build/images/platinumMastercard/five.png',
                'title' => 'Car Rental discount with Avis',
                'points' => [
                    'Up to 20% off your next car rental.',
                    'A complimentary upgrade with every rental.',
                ],
                'learnMoreLink' => '/',
            ],
            [
                'imagePath' => 'build/images/platinumMastercard/six.png',
                'title' => 'Car Rental discount with Budget',
                'points' => [
                    'Enjoy 10% discount using your Suyool Platinum Mastercard with Budget.',
                ],
                'learnMoreLink' => '/',
            ],
            [
                'imagePath' => 'build/images/platinumMastercard/seven.png',
                'title' => 'Car Rental discount with Rentalcars.com',
                'points' => [
                    'Get a 10% discount on your Rentalcars.com booking with your Suyool Platinum Mastercard.',
                ],
                'learnMoreLink' => '/',
            ],
            [
                'imagePath' => 'build/images/platinumMastercard/eight.png',
                'title' => 'Priceless cities for experiences in the cities you live & travel',
                'points' => [
                    'Experience worldwide dining, travel, and entertainment exclusives with Mastercard’s Priceless Cities offers.',
                ],
                'learnMoreLink' => '/',
            ],
        ];

        $lifeStyleData = [
            [
                'imagePath' => 'build/images/platinumMastercard/un.png',
                'title' => 'Shopping discount at Bicester Village Shopping Collection',
                'points' => [
                    'VIP invitation with additional 10% discount.*',
                    '15% discount on shopping packages, chauffeur drive experiences & Shopping Express.',
                    'Access to VIP lounges.*',
                ],
                'learnMoreLink' => '/',
            ],
            [
                'imagePath' => 'build/images/platinumMastercard/deux.png',
                'title' => 'Shopping discount on Farfetch.com',
                'points' => [
                    'Save 10% on Farfetch fashion with Suyool Platinum Mastercard on $200+ purchases.',
                ],
                'learnMoreLink' => '/',
            ],
            [
                'imagePath' => 'build/images/platinumMastercard/trois.png',
                'title' => 'MyUS shipping premium membership',
                'points' => [
                    'Shop over 100,000 MyUS retailers with Free Premium Membership',
                    '30% off shipping to anywhere first month.',
                    '20% off shipping for remainder of 2-year membership.',
                ],
                'learnMoreLink' => '/',
            ],
            [
                'imagePath' => 'build/images/platinumMastercard/quatre.png',
                'title' => 'Dining discounts through Priceless specials',
                'points' => [
                    'Book your next holiday on booking.com with Mastercard for up to 10% money back.',
                ],
                'learnMoreLink' => '/',
            ],
            [
                'imagePath' => 'build/images/platinumMastercard/cinq.png',
                'title' => 'Fitness discount through Fiit',
                'points' => [
                    'Get 25% off your initial Fiit subscription payment with Suyool Platinum Mastercard.',
                ],
                'learnMoreLink' => '/',
            ],
            [
                'imagePath' => 'build/images/platinumMastercard/sise.png',
                'title' => 'Learning discount on LingoKids',
                'points' => [
                    '2 months of Lingokids for free for new users.',
                    '30% off monthly subscriptions for new & existing users',
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