<?php

namespace App\Controller;

use App\Entity\emailsubscriber;
use App\Entity\Managers;
use App\Entity\Rates;
use App\Translation\translation;
use App\Utils\Helper;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use metaService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpKernel\EventListener\AbstractSessionListener;
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
     * @Cache(smaxage="120", public=true)
     *
     * @param Request $request
     * @param TranslatorInterface $translator
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function indexAction(Request $request, TranslatorInterface $translator, EntityManagerInterface $em, MailerInterface $mailer, TranslatorInterface $translatorInterface)
    {
        $trans = $this->trans->translation($request, $translator);
        $translatorInterface->setLocale("en");

        $title = "Suyool | First Digital Wallet with a Debit Card in Lebanon";
        $desc = "Suyool, the licensed financial app originating from Europe, 
        is designed to address your cash-handling challenges. Whether it’s seamlessly cashing out, 
        sending money to anyone in Lebanon, or making local and international payments with your Platinum Debit Card, 
        Suyool empowers you with full control over your finances.";
        $parameters = [
            'title' => $title,
            'desc' => $desc,
            'metaimage' => 'build/images/meta-image-website2.png',
            'descmeta' => $desc,
            'barBgColor' => 'barWhite'
        ];

        $content = $this->render('homepage/homepage.html.twig', $parameters);
        $content->headers->set(AbstractSessionListener::NO_AUTO_CACHE_CONTROL_HEADER, 'true');


        return $content;
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
        return $this->render('policies/privacy-policy-new.html.twig', ['barBgColor' => 'barWhite']);
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
        $title = "Suyool Debit Card | Suyool";
        $desc = "Start Enjoying Platinum Benefits Instantly, From Travel Discounts
        to Shopping Perks, and Elevate Your Lifestyle Beyond Imagination.";
        $cardData = [
            [
                'imagePath' => 'build/images/platinumMastercard/travel-pass-mastercard-details-suyool.png',
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
                    'Enjoy 10% discount using your Suyool Debit Card with Budget.',
                ],
                'learnMoreLink' => '/',
            ],
            [
                'imagePath' => 'build/images/platinumMastercard/seven.png',
                'title' => 'Car Rental discount with Rentalcars.com',
                'points' => [
                    'Get a 10% discount on your Rentalcars.com booking with your Suyool Debit Card.',
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
                    'Save 10% on Farfetch fashion with Suyool Debit Card on $200+ purchases.',
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
                    'Get 25% off your initial Fiit subscription payment with Suyool Debit Card.',
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

        $parameters = [
            'cardData' => $cardData,
            'lifeStyleData' => $lifeStyleData,
            'title' => $title,
            'desc' => $desc,
            'barBgColor' => 'barWhite'

        ];
        $parameters['hideLearnMore'] = "";

        return $this->render('homepage/mastercard.html.twig', $parameters);
    }

    public function show()
    {
        return $this->render('ExceptionHandling.html.twig');
    }

    /**
     * @Route("/corporate-terms", name="terms_and_conditions")
     */
    public function terms()
    {
        $title = "Suyool Terms & Conditions | Suyool";
        $desc = "Kindly read our terms and conditions carefully before using this site";

        $parameters = [
            'title' => $title,
            'desc' => $desc,
            'barBgColor' => 'barBlue'

        ];

        return $this->render('TermsAndConditions/index.html.twig', $parameters);
    }

    /**
     * @Route("/personal-terms", name="terms_and_conditions_personal")
     */
    public function personalterms()
    {
        $parameters = [
            'barBgColor' => 'barBlue'
        ];
        return $this->render('TermsAndConditions/personal.html.twig', $parameters);
    }

    /**
     * @Route("/download-pdf", name="download_pdf")
     */
    public function downloadPdf()
    {
        // Define the path to your PDF file
        $pdfFilePath = $this->getParameter('kernel.project_dir') . '/public/pdf/personal.pdf';

        $response = new BinaryFileResponse($pdfFilePath);
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'personal.pdf'
        );

        return $response;
    }

    /**
     * @Route("/personal-fees", name="presonal_fees")
     */
    public function fees()
    {
        $parameters = [
            'barBgColor' => 'barBlue'
        ];
        return $this->render('personal-fees/index.html.twig', $parameters);
    }

    /**
     * @Route("/middle-east", name="app_middle_east")
     */
    public function middleeast(Request $request)
    {
        $parameters=array();
        return $this->render('platinum-mastercard/middleeast.html.twig', $parameters);
    }

    /**
     * @Route("/send-receive-money", name="app_middle_east")
     */
    public function sendReceiveMoney(Request $request)
    {
        $parameters=array();
        $title = "Send money instantly to any Lebanese number";
        $desc = "Experience the ease and speed of sending money with Suyool. Simply enter a recipient’s Lebanese phone number, and transfer funds instantly.";
        $parameters = [
            'title' => $title,
            'desc' => $desc,
            'metaimage' => 'build/images/sendReceiveMETA.png',
            'descmeta' => $desc,
        ];
        $infoSection = [
            'title' => 'BENEFITS OF SENDING MONEY WITH SUYOOL',
            'items' => [
                [
                    'image' => 'build/images/sendReceiveMoney/24logo.svg',
                    'title' => 'Convenience',
                    'description' => 'Anytime in just a few taps',
                ],
                [
                    'image' => 'build/images/sendReceiveMoney/lbpdollars.svg',
                    'title' => 'Multi-Currency',
                    'description' => 'Both LBP or USD',
                ],
                [
                    'image' => 'build/images/sendReceiveMoney/instantPayments.svg',
                    'title' => 'Instant Payments',
                    'description' => 'Faster than any other',
                ],
                [
                    'image' => 'build/images/sendReceiveMoney/cost-effective.svg',
                    'title' => 'Cost-Effective',
                    'description' => 'Only 1.5% if non-Suyool',
                ],
            ],
        ];
        $parameters['infoSection']= $infoSection;

        $parameters['faq']=[
            "ONE"=>[
                "Title"=>"HOW_CAN_I_SEND_MONEY_WITH_SUYOOL",
                "Desc"=>"SUYOOL_USER_CAN_TRANSFER"
            ],
            "TWO"=>[
                "Title"=>"CAN_I_SEND_MONEY_TO_A_NON",
                "Desc"=>"YOU_CAN_SEND_MONEU_TO_ANY"
            ],
            "THREE"=>[
                "Title"=>"IS_THERE_A_FEE_FOR_TRANSFERRING",
                "Desc"=>"TRANSFERRING_MONEY_THROUGH_SUYOOL"
            ],
            "FOUR"=>[
                "Title"=>"CAN_I_SEND_MONEY_TO_A_PERSON_WITHOUT",
                "Desc"=>"YES_YOU_CAN_SEND_MONEY"
            ],
            "FIVE"=>[
                "Title"=>"WHAT_DO_ID_DO_IF_I",
                "Desc"=>"WEVE_ADDED_EXTRA_SECURITY"
            ],
        ];


        return $this->render('sendReceiveMoney/index.html.twig', $parameters);
    }

}