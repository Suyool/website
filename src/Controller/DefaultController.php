<?php

namespace App\Controller;

use App\Entity\emailsubscriber;
use App\Entity\Managers;
use App\Entity\Rates;
use App\Service\NotificationServices;
use App\Service\SuyoolServices;
use App\Translation\translation;
use App\Utils\Helper;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use metaService;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Cache\Adapter\AdapterInterface;
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
    private $memcachedCache;
    private $loggerInterface;

    private $paySuyoolFaq = [
        "ONE" => [
            "Title" => "Can anyone request the Suyool Visa platinum card?",
            "Desc" => "Once successfully registered on the Suyool app, anyone can request the Suyool Visa Platinum card and enjoy its privileges."
        ],
        "TWO" => [
            "Title" => "How do I request my Suyool Visa Platinum card?",
            "Desc" => "Once your information is validated & confirmed, you can directly request your Suyool Visa Platinum debit card from your app. Once your request is approved, your card will be delivered to your address for free."
        ],
        "THREE" => [
            "Title" => "What is the fee of requesting the Suyool Visa Platinum card?",
            "Desc" => "The fee for requesting your Suyool Debit Card is $12 to be paid annually."
        ],
        "FOUR" => [
            "Title" => "Can I use the card online?",
            "Desc" => "Yes, you can use the Suyool Visa Platinum card online."
        ],
        "FIVE" => [
            "Title" => "Can I use the card internationally",
            "Desc" => "Yes, you can use your Suyool Visa Platinum card anywhere Visa is accepted."
        ],
        "SIX" => [
            "Title" => "Is the Suyool Visa Platinum card an international card?",
            "Desc" => "Yes! The Suyool Visa Platinum card is an international fresh USD debit card."
        ],
        "SEVEN" => [
            "Title" => "What currencies does the Suyool Visa Card accept?",
            "Desc" => "You can pay in any currency with Suyool Visa card (including LBP)."
        ],
    ];

    public function __construct(translation $trans,AdapterInterface  $memcachedCache,LoggerInterface $loggerInterface)
    {
        $this->trans = $trans;
        $this->memcachedCache = $memcachedCache;
        $this->loggerInterface = $loggerInterface;
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
        $buyRate = '';
        $sellRate = '';
        $updatedTime = '';
        $cacheKey = 'exchangeRates';
        $cachedRates = $this->memcachedCache->getItem($cacheKey);
        $cachedRates = $cachedRates->get();

        if(!empty($cachedRates)){
            $buyRate =  $cachedRates['buyRate'];
            $sellRate = $cachedRates ['sellRate'];
            $updatedTime = $cachedRates ['date'];
        }
        $parameters = [
            'title' => $title,
            'desc' => $desc,
            'metaimage' => 'build/images/meta-image-website3.png',
            'descmeta' => $desc,
            'barBgColor' => 'barWhite',
            'chatbot' => true,
            'homepage' => true,
            'buyRate' => $buyRate,
            'sellRate' => $sellRate,
            'updatedTime' => $updatedTime
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

        // return $this->render('homepage/mastercard.html.twig', $parameters);
        return $this->redirectToRoute('app_visa');
    }

    public function show()
    {
        return $this->render('ExceptipnHandling404.html.twig');
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
        $parameters = array();
        return $this->render('platinum-mastercard/middleeast.html.twig', $parameters);
    }

    /**
     * @Route("/send-receive-money", name="app_send_receive")
     */
    public function sendReceiveMoney(Request $request)
    {
        $parameters = array();
        $title = "Send money instantly to any Lebanese number";
        $desc = "Experience the ease and speed of sending money with Suyool. Simply enter a recipient’s Lebanese phone number, and transfer funds instantly.";
        $parameters = [
            'title' => $title,
            'desc' => $desc,
            'metaimage' => 'build/images/sendReceiveMoney/send-money-meta.png',
            'descmeta' => $desc,
            'barBgColor' => 'barWhite'
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
        $parameters['infoSection'] = $infoSection;

        $parameters['faq'] = [
            "ONE" => [
                "Title" => "Can I send money to any phone number?",
                "Desc" => "Suyool users can transfer money instantly to any Lebanese mobile number from the comfort of their own home."
            ],
            "TWO" => [
                "Title" => "How long does it take for the money sent to arrive to the recipient?",
                "Desc" => "The recipient will receive the money instantly once the amount has been sent."
            ],
            "THREE" => [
                "Title" => "Can I send money to a non Suyool user?",
                "Desc" => "Yes, you can send money to a non Suyool user. They will receive an SMS with a link which will redirect them to a web page where they will have 2 options. They can either download the Suyool app and receive the amount on it or go to any BOB Finance cashpoint and get the amount in cash (1.5% fees apply in this case)"
            ],
            "FOUR" => [
                "Title" => "Is there a fee for transferring money in Suyool?",
                "Desc" => "Transferring money through Suyool to any Lebanese number is free of charge. However if they are not a Suyool user 1.5% fees be applied."
            ],
            "FIVE" => [
                "Title" => "Can I send money to a person without exchanging my personal details with them?",
                "Desc" => "Yes! You can send money to others by scanning their QR code featured on the app, without having to share your mobile number and personal details."
            ],
        ];


        return $this->render('sendReceiveMoney/index.html.twig', $parameters);
    }
    /** 
     * @Route("/visa", name="app_visa")
     */
    public function visa(Request $request)
    {
        $title = "Suyool Visa Card | Suyool";
        $desc = "Start Enjoying Platinum Benefits Instantly, From Travel Discounts
        to Shopping Perks, and Elevate Your Lifestyle Beyond Imagination.";
        $metaimage = "build/images/platinumMastercard/metavisa.png";
        $descmeta = "Start Enjoying Platinum Benefits Instantly, From Travel Discounts
        to Shopping Perks, and Elevate Your Lifestyle Beyond Imagination.";
        $visa = true;
        $cardData = [
            [
                'imagePath' => 'build/images/platinumMastercard/lounge.png',
                'title' => 'Airport lounge access with Lounge KEY',
                'points' => [
                    'Free access to 25+ airport lounges in multiple countries',
                    '6 Cardholder visits per calendar year',
                    'No registration required to activate the lounge benefit on the Visa Platinum card',
                ],
                'learnMoreLink' => '/',
            ],
            [
                'imagePath' => 'build/images/platinumMastercard/pass.png',
                'title' => 'Airport Dining Offers with DragonPass',
                'points' => [
                    '200+ restaurants globally, made up of key locations in both MENA home markets and travel corridors',
                    'Discounts vary by merchant, and are visible in the mobile app'
                ],
                'learnMoreLink' => '/',
            ],
            [
                'imagePath' => 'build/images/platinumMastercard/vq.png',
                'title' => 'Meet & Assist service partners with YQ',
                'points' => [
                    'Global network for airport assistance partners',
                    'Discount of up to 15% off retail rates for Visa Platinum cards at over 450 destinations globally',
                    'Cardholders can book services like limo transfers, visas on arrival, and baggage porters too.',
                ],
                'learnMoreLink' => '/',
            ],
            [
                'imagePath' => 'build/images/platinumMastercard/agoda.png',
                'title' => 'Hotel & vacation rentals offers with Agoda',
                'points' => [
                    'Access hotels and vacation rentals globally through Agoda',
                    'Use Platinum Visa card for a 12% discount',
                    'Applicable to `Promotion Eligible` properties; offer valid until June 14, 2024'
                ],
                'learnMoreLink' => '/',
            ],
            [
                'imagePath' => 'build/images/platinumMastercard/three.png',
                'title' => 'Hotel discount and cashback with IHG Hotels & Resorts',
                'points' => [
                    'Visa cardholders get 15% off at 100+ IHG Hotels & Resorts from October 5, 2022',
                    'Bookings until March 31, 2024, for stays until April 3, 2024',
                ],
                'learnMoreLink' => '/',
            ],
            //            [
            //                'imagePath' => 'build/images/platinumMastercard/visa.png',
            //                'title' => 'Visa Luxury Hotel Collection',
            //                'points' => [
            //                    'Best available rate guarantee',
            //                    'Automatic room upgrade, and VIP guest status',
            //                    'Offer includes complimentary Wi-Fi, daily continental breakfast, and $25 USD credit; valid until December 31, 2023'
            //                ],
            //                'learnMoreLink' => '/',
            //            ],
            [
                'imagePath' => 'build/images/platinumMastercard/sixt.png',
                'title' => 'Car Rental offers with SIXT',
                'points' => [
                    'Visa Platinum Cardholders receive up to 10% off car rentals worldwide with SIXT Gold Membership',
                    'Offer valid until October 25, 2025; subject to SIXT\'s rental terms and conditions'
                ],
                'learnMoreLink' => '/',
            ],
            [
                'imagePath' => 'build/images/platinumMastercard/five.png',
                'title' => 'Car Rental discount with Avis',
                'points' => [
                    'Up to 20% discount on Standard Rates',
                    'Up to 10% off on Retail Rates',
                    'Avis Preferred Plus Membership free of charge & enjoy additional services including free drivers'
                ],
                'learnMoreLink' => '/',
            ],
            [
                'imagePath' => 'build/images/platinumMastercard/six.png',
                'title' => 'Car Rental discount with Budget',
                'points' => [
                    'Avis Budget Group offers renowned car rentals globally',
                    'Visa Platinum cardholders receive a 10% discount on Budget rentals worldwide'
                ],
                'learnMoreLink' => '/',
            ],
            [
                'imagePath' => 'build/images/platinumMastercard/medical.png',
                'title' => 'Medical & travel assistance',
                'points' => [
                    'Visa Platinum Cardholders have access to comprehensive global assistance services',
                    'Services include medical advice, referrals, and essential medicine delivery',
                    'Also provides legal referrals and interpreter services'
                ],
                'learnMoreLink' => '/',
            ],
            [
                'imagePath' => 'build/images/platinumMastercard/cart.png',
                'title' => 'Global Customer Assistance Services',
                'points' => [
                    'GCAS provides essential support to Platinum Cardholders during travel',
                    'Core services include inquiry assistance, lost/stolen card reporting, and emergency replacements',
                    'Cash disbursement, file updates, and pre-enrollment via banks for Visa BINs'
                ],
                'learnMoreLink' => '/',
            ]
        ];

        $lifeStyleData = [
            [
                'imagePath' => 'build/images/platinumMastercard/JumairaVisa.png',
                'title' => 'Dining with Jumeirah F&B offers',
                'points' => [
                    'Visa offers up to 25% off dining at select Jumeirah Hotels & Resorts',
                    'Valid until September 30, 2024, subject to availability',
                ],
                'learnMoreLink' => '/',
            ],
            [
                'imagePath' => 'build/images/platinumMastercard/BookingVisa.png',
                'title' => 'Hotel discount and cashback with booking.com',
                'points' => [
                    'Visa Cardholders get up to 8% instant cashback on Booking.com in selected CEMEA markets',
                    'Use promocode ‘VISABKNG’ for discounts ranging from 6% to 8%<br>Use this exclusive Link:<br> <a href="https://www.booking.com/8visacemea">www.booking.com/8visacemea</a>',
                    'Offer valid until December 31, 2024'
                ],
                'learnMoreLink' => '/',
            ],
            //            [
            //                'imagePath' => 'build/images/platinumMastercard/GlobalBlueVisa.png',
            //                'title' => 'Shopping with Global Blue',
            //                'points' => [
            //                    'Visa Cardholders receive 20% Extra Refund (up to €500) with Global Blue tax-free shopping',
            //                    'Use promocode ‘VISABKNG’ for discounts ranging from 6% to 8%',
            //                    'Offer valid until December 31, 2024',
            //                ],
            //                'learnMoreLink' => '/',
            //            ],
            // [
            //     'imagePath' => 'build/images/platinumMastercard/GlobalLocalVisa.png',
            //     'title' => 'Shopping with Global Blue',
            //     'points' => [
            //         'Visa Cardholders receive 20% Extra Refund (up to €500) with Global Blue tax-free shopping',
            //         'Available in select markets: France, Germany, Italy, and Spain starting November 15, 2021',
            //         'Offer valid until December 31, 2023'
            //     ],
            //     'learnMoreLink' => '/',
            // ],
            [
                'imagePath' => 'build/images/platinumMastercard/GlobalLocalVisa.png',
                'title' => 'Shopping with Global & Local merchant offers',
                'points' => [
                    'Visa Platinum offers exclusive deals worldwide in fashion, homeware, and electronics',
                    'Cardholders get discounts on jewelry, fashion, and more in the region and abroad'
                ],
                'learnMoreLink' => '/',
            ],
            [
                'imagePath' => 'build/images/platinumMastercard/SpikesOnVisa.png',
                'title' => 'Experience with Visa Golf Access',
                'points' => [
                    'Visa Golf Access offers discounts on golf green fees for Visa Platinum Cardholders worldwide',
                    'Book in advance by calling or using the SpikesOn app for bookings and payments',
                    'Participating courses include Jebel Sifah, Yas Links, Royal Golf Club, and more; offer valid until September 2024'
                ],
                'learnMoreLink' => '/',
            ],
            [
                'imagePath' => 'build/images/platinumMastercard/spaVisa.png',
                'title' => 'Experience with Jumeirah Spa Offers',
                'points' => [
                    'Visa cardholders enjoy exclusive spa offers at various Jumeirah Hotels’ Talise Spas',
                    'Discounts range from 10% to 20% on treatments at different locations',
                    'Offers valid until September 30, 2024'

                ],
                'learnMoreLink' => '/',
            ],
        ];

        $protection = [
            [
                'imagePath' => 'build/images/platinumMastercard/buyers.png',
                'title' => 'Buyers Protection',
                'points' => [
                    'Visa Platinum offers Buyers Protection Insurance for eligible purchases, covering theft, accidental damage, or non-delivery.',
                    'Valid for items fully paid with the Visa Platinum card, new item purchases only, and lasts up to 365 days from purchase.',
                    'Cardholders can access insurance details and claim instructions on website',
                ],
                'learnMoreLink' => '/',
            ],
            [
                'imagePath' => 'build/images/platinumMastercard/visainsurance.png',
                'title' => 'Extended Warranty',
                'points' => [
                    'Visa Platinum offers Extended Warranty, doubling the repair period of the original manufacturer’s warranty for up to 1 year',
                    'Applicable to full payment with Visa Platinum card on new item purchases'
                ],
                'learnMoreLink' => '/',
            ],
            [
                'imagePath' => 'build/images/platinumMastercard/warrancty.png',
                'title' => 'Visa Insurances',
                'points' => [
                    'Visa introduced an online portal and claim tracker in 2016 for cardholders',
                    'Additionally, a bank portal launched in 2017 for bank staff to assist cardholders without a valid PAN',
                ],
                'learnMoreLink' => '/',
            ],
        ];

        $faq = [
            "ONE" => [
                "Title" => "How do I request my Suyool Visa Platinum card?",
                "Desc" => "Once your information is validated & confirmed, you can directly request your Suyool Visa Platinum debit card from your app. Once your request is approved, your card will be delivered to your address for free."
            ],
            "TWO" => [
                "Title" => "What is the fee of requesting the Suyool Visa Platinum card?",
                "Desc" => "The fee for requesting your Suyool Debit Card is $12 to be paid annually."
            ],
            "THREE" => [
                "Title" => "Can I use the card online?",
                "Desc" => "Yes, you can use the Suyool Visa Platinum card online."
            ],
            "FOUR" => [
                "Title" => "Can I use the card internationally",
                "Desc" => "Yes, you can use your Suyool Visa Platinum card anywhere Visa is accepted."
            ],
            "FIVE" => [
                "Title" => "Is the Suyool Visa Platinum card an international card?",
                "Desc" => "Yes! The Suyool Visa Platinum card is an international fresh USD debit card."
            ],
//            "SIX" => [
//                "Title" => "Can I withdraw cash from an ATM in Lebanon?",
//                "Desc" => "Yes, you can withdraw cash from specific ATMs (fresh usd ones) in Lebanon with a fee of 3.75$ + 0.5% of the amount withdrawn. Some banks might charge additional fees."
//            ],
        ];

        $parameters = [
            'cardData' => $cardData,
            'lifeStyleData' => $lifeStyleData,
            'protection' => $protection,
            'title' => $title,
            'desc' => $desc,
            'barBgColor' => 'barWhite',
            'metaimage' => $metaimage,
            'descmeta' => $descmeta,
            'visa' => $visa,
            'faq' => $faq,
            'bgColor' => 'bg-white',
            'btnBgColor' => 'bg-blue'
        ];
        $parameters['hideLearnMore'] = "";

        return $this->render('homepage/visa.html.twig', $parameters);
    }

    /**
     * @Route("/visaCard", name="app_visa_card")
     */
    public function visaCard(Request $request)
    {
        $canonical_url = 'https://suyool.com/visa';

        $title = "Suyool Visa Card | Suyool";
        $desc = "Start Enjoying Platinum Benefits Instantly, From Travel Discounts
        to Shopping Perks, and Elevate Your Lifestyle Beyond Imagination.";
        $metaimage = "build/images/platinumMastercard/metavisa.png";
        $descmeta = "Start Enjoying Platinum Benefits Instantly, From Travel Discounts
        to Shopping Perks, and Elevate Your Lifestyle Beyond Imagination.";
        $visa = true;
        $cardData = [
            [
                'imagePath' => 'build/images/platinumMastercard/lounge.png',
                'title' => 'Airport lounge access with Lounge KEY',
                'points' => [
                    'Free access to 25+ airport lounges in multiple countries',
                    '6 Cardholder visits per calendar year',
                    'No registration required to activate the lounge benefit on the Visa Platinum card',
                ],
                'learnMoreLink' => '/',
            ],
            [
                'imagePath' => 'build/images/platinumMastercard/pass.png',
                'title' => 'Airport Dining Offers with DragonPass',
                'points' => [
                    '200+ restaurants globally, made up of key locations in both MENA home markets and travel corridors',
                    'Discounts vary by merchant, and are visible in the mobile app'
                ],
                'learnMoreLink' => '/',
            ],
            [
                'imagePath' => 'build/images/platinumMastercard/vq.png',
                'title' => 'Meet & Assist service partners with YQ',
                'points' => [
                    'Global network for airport assistance partners',
                    'Discount of up to 15% off retail rates for Visa Platinum cards at over 450 destinations globally',
                    'Cardholders can book services like limo transfers, visas on arrival, and baggage porters too.',
                ],
                'learnMoreLink' => '/',
            ],
            [
                'imagePath' => 'build/images/platinumMastercard/agoda.png',
                'title' => 'Hotel & vacation rentals offers with Agoda',
                'points' => [
                    'Access hotels and vacation rentals globally through Agoda',
                    'Use Platinum Visa card for a 12% discount',
                    'Applicable to `Promotion Eligible` properties; offer valid until June 14, 2024'
                ],
                'learnMoreLink' => '/',
            ],
            [
                'imagePath' => 'build/images/platinumMastercard/three.png',
                'title' => 'Hotel discount and cashback with IHG Hotels & Resorts',
                'points' => [
                    'Visa cardholders get 15% off at 100+ IHG Hotels & Resorts from October 5, 2022',
                    'Bookings until March 31, 2024, for stays until April 3, 2024',
                ],
                'learnMoreLink' => '/',
            ],
            //            [
            //                'imagePath' => 'build/images/platinumMastercard/visa.png',
            //                'title' => 'Visa Luxury Hotel Collection',
            //                'points' => [
            //                    'Best available rate guarantee',
            //                    'Automatic room upgrade, and VIP guest status',
            //                    'Offer includes complimentary Wi-Fi, daily continental breakfast, and $25 USD credit; valid until December 31, 2023'
            //                ],
            //                'learnMoreLink' => '/',
            //            ],
            [
                'imagePath' => 'build/images/platinumMastercard/sixt.png',
                'title' => 'Car Rental offers with SIXT',
                'points' => [
                    'Visa Platinum Cardholders receive up to 10% off car rentals worldwide with SIXT Gold Membership',
                    'Offer valid until October 25, 2025; subject to SIXT\'s rental terms and conditions'
                ],
                'learnMoreLink' => '/',
            ],
            [
                'imagePath' => 'build/images/platinumMastercard/five.png',
                'title' => 'Car Rental discount with Avis',
                'points' => [
                    'Up to 20% discount on Standard Rates',
                    'Up to 10% off on Retail Rates',
                    'Avis Preferred Plus Membership free of charge & enjoy additional services including free drivers'
                ],
                'learnMoreLink' => '/',
            ],
            [
                'imagePath' => 'build/images/platinumMastercard/six.png',
                'title' => 'Car Rental discount with Budget',
                'points' => [
                    'Avis Budget Group offers renowned car rentals globally',
                    'Visa Platinum cardholders receive a 10% discount on Budget rentals worldwide'
                ],
                'learnMoreLink' => '/',
            ],
            [
                'imagePath' => 'build/images/platinumMastercard/medical.png',
                'title' => 'Medical & travel assistance',
                'points' => [
                    'Visa Platinum Cardholders have access to comprehensive global assistance services',
                    'Services include medical advice, referrals, and essential medicine delivery',
                    'Also provides legal referrals and interpreter services'
                ],
                'learnMoreLink' => '/',
            ],
            [
                'imagePath' => 'build/images/platinumMastercard/cart.png',
                'title' => 'Global Customer Assistance Services',
                'points' => [
                    'GCAS provides essential support to Platinum Cardholders during travel',
                    'Core services include inquiry assistance, lost/stolen card reporting, and emergency replacements',
                    'Cash disbursement, file updates, and pre-enrollment via banks for Visa BINs'
                ],
                'learnMoreLink' => '/',
            ]
        ];

        $lifeStyleData = [
            [
                'imagePath' => 'build/images/platinumMastercard/JumairaVisa.png',
                'title' => 'Dining with Jumeirah F&B offers',
                'points' => [
                    'Visa offers up to 25% off dining at select Jumeirah Hotels & Resorts',
                    'Valid until September 30, 2024, subject to availability',
                ],
                'learnMoreLink' => '/',
            ],
            [
                'imagePath' => 'build/images/platinumMastercard/BookingVisa.png',
                'title' => 'Hotel discount and cashback with booking.com',
                'points' => [
                    'Visa Cardholders get up to 8% instant cashback on Booking.com in selected CEMEA markets',
                    'Use promocode ‘VISABKNG’ for discounts ranging from 6% to 8%<br>Use this exclusive Link:<br> <a href="https://www.booking.com/8visacemea">www.booking.com/8visacemea</a>',
                    'Offer valid until December 31, 2024'
                ],
                'learnMoreLink' => '/',
            ],
            //            [
            //                'imagePath' => 'build/images/platinumMastercard/GlobalBlueVisa.png',
            //                'title' => 'Shopping with Global Blue',
            //                'points' => [
            //                    'Visa Cardholders receive 20% Extra Refund (up to €500) with Global Blue tax-free shopping',
            //                    'Use promocode ‘VISABKNG’ for discounts ranging from 6% to 8%',
            //                    'Offer valid until December 31, 2024',
            //                ],
            //                'learnMoreLink' => '/',
            //            ],
            // [
            //     'imagePath' => 'build/images/platinumMastercard/GlobalLocalVisa.png',
            //     'title' => 'Shopping with Global Blue',
            //     'points' => [
            //         'Visa Cardholders receive 20% Extra Refund (up to €500) with Global Blue tax-free shopping',
            //         'Available in select markets: France, Germany, Italy, and Spain starting November 15, 2021',
            //         'Offer valid until December 31, 2023'
            //     ],
            //     'learnMoreLink' => '/',
            // ],
            [
                'imagePath' => 'build/images/platinumMastercard/GlobalLocalVisa.png',
                'title' => 'Shopping with Global & Local merchant offers',
                'points' => [
                    'Visa Platinum offers exclusive deals worldwide in fashion, homeware, and electronics',
                    'Cardholders get discounts on jewelry, fashion, and more in the region and abroad'
                ],
                'learnMoreLink' => '/',
            ],
            [
                'imagePath' => 'build/images/platinumMastercard/SpikesOnVisa.png',
                'title' => 'Experience with Visa Golf Access',
                'points' => [
                    'Visa Golf Access offers discounts on golf green fees for Visa Platinum Cardholders worldwide',
                    'Book in advance by calling or using the SpikesOn app for bookings and payments',
                    'Participating courses include Jebel Sifah, Yas Links, Royal Golf Club, and more; offer valid until September 2024'
                ],
                'learnMoreLink' => '/',
            ],
            [
                'imagePath' => 'build/images/platinumMastercard/spaVisa.png',
                'title' => 'Experience with Jumeirah Spa Offers',
                'points' => [
                    'Visa cardholders enjoy exclusive spa offers at various Jumeirah Hotels’ Talise Spas',
                    'Discounts range from 10% to 20% on treatments at different locations',
                    'Offers valid until September 30, 2024'

                ],
                'learnMoreLink' => '/',
            ],
        ];

        $protection = [
            [
                'imagePath' => 'build/images/platinumMastercard/buyers.png',
                'title' => 'Buyers Protection',
                'points' => [
                    'Visa Platinum offers Buyers Protection Insurance for eligible purchases, covering theft, accidental damage, or non-delivery.',
                    'Valid for items fully paid with the Visa Platinum card, new item purchases only, and lasts up to 365 days from purchase.',
                    'Cardholders can access insurance details and claim instructions on website',
                ],
                'learnMoreLink' => '/',
            ],
            [
                'imagePath' => 'build/images/platinumMastercard/visainsurance.png',
                'title' => 'Extended Warranty',
                'points' => [
                    'Visa Platinum offers Extended Warranty, doubling the repair period of the original manufacturer’s warranty for up to 1 year',
                    'Applicable to full payment with Visa Platinum card on new item purchases'
                ],
                'learnMoreLink' => '/',
            ],
            [
                'imagePath' => 'build/images/platinumMastercard/warrancty.png',
                'title' => 'Visa Insurances',
                'points' => [
                    'Visa introduced an online portal and claim tracker in 2016 for cardholders',
                    'Additionally, a bank portal launched in 2017 for bank staff to assist cardholders without a valid PAN',
                ],
                'learnMoreLink' => '/',
            ],
        ];

        $faq = [
            "ONE" => [
                "Title" => "How do I request my Suyool Visa Platinum card?",
                "Desc" => "Once your information is validated & confirmed, you can directly request your Suyool Visa Platinum debit card from your app. Once your request is approved, your card will be delivered to your address for free."
            ],
            "TWO" => [
                "Title" => "What is the fee of requesting the Suyool Visa Platinum card?",
                "Desc" => "The fee for requesting your Suyool Debit Card is $12 to be paid annually."
            ],
            "THREE" => [
                "Title" => "Can I use the card online?",
                "Desc" => "Yes, you can use the Suyool Visa Platinum card online."
            ],
            "FOUR" => [
                "Title" => "Can I use the card internationally",
                "Desc" => "Yes, you can use your Suyool Visa Platinum card anywhere Visa is accepted."
            ],
            "FIVE" => [
                "Title" => "Is the Suyool Visa Platinum card an international card?",
                "Desc" => "Yes! The Suyool Visa Platinum card is an international fresh USD debit card."
            ],
//            "SIX" => [
//                "Title" => "Can I withdraw cash from an ATM in Lebanon?",
//                "Desc" => "Yes, you can withdraw cash from specific ATMs (fresh usd ones) in Lebanon with a fee of 3.75$ + 0.5% of the amount withdrawn. Some banks might charge additional fees."
//            ],
        ];

        $parameters = [
            'cardData' => $cardData,
            'lifeStyleData' => $lifeStyleData,
            'protection' => $protection,
            'title' => $title,
            'desc' => $desc,
            'barBgColor' => 'barWhite',
            'metaimage' => $metaimage,
            'descmeta' => $descmeta,
            'visa' => $visa,
            'faq' => $faq,
            'canonical_url' => $canonical_url,
            'bgColor' => 'bg-white',
            'btnBgColor' => 'bg-blue'
        ];
        $parameters['hideLearnMore'] = "";

        return $this->render('homepage/visa.html.twig', $parameters);
    }

    /** 
     * @Route("/spinneys-promotions", name="app_spinneys")
     */
    public function spinneysPromotion()
    {
        $faq = [
            "ONE" => [
                "Title" => "How can I pay with Suyool at Spinneys?",
                "Desc" => "There are three ways to pay at Spinneys with Suyool: Use your Suyool Visa Platinum Card in USD, utilize the Suyool QR payment tool, or provide your phone number to receive a payment request and accept it directly on your phone."
            ],
            "TWO" => [
                "Title" => "How do I double my Spinneys points with Suyool?",
                "Desc" => "To double your Spinneys points, use the Suyool QR payment tool. This offer is valid from December 1 to January 6."
            ],
            "THREE" => [
                "Title" => "What is Suyool QR?",
                "Desc" => "Suyool QR is a secure and convenient payment tool offered by Suyool. It’s a QR code-based payment method that allows users to make transactions by scanning a QR code at participating merchants."
            ],
            "FOUR" => [
                "Title" => "How to find merchants that have Suyool as a payment method?",
                "Desc" => "Discover merchants, like Spinneys, accepting Suyool QR directly on the app’s Discovery tab."
            ],
            "FIVE" => [
                "Title" => "What is the auto-conversion feature?",
                "Desc" => "The auto-conversion feature automatically exchanges between the wallets (LBP & USD) while paying in person using the QR code in case the amount is not enough. It only exchanges the needed amount to execute the operation."
            ]
        ];

        $howToGetTitle = "HOW TO GET SUYOOL?";
        $howToGetDesc = "3 Easy Steps";
        $howToGetText = "";
        $howToGet = [
            [
                'title' => 'Open an account in minutes from the comfort of your home',
                'description' => 'All you need is your smartphone, a Lebanese number & a legal identification document (ID or Passport).',
            ],
            [
                'title' => 'Add Money To Your Account',
                'description' => 'Via cash deposit at more than 700+ BOB Finance outlets or online with any debit or credit card (USD/LBP).',
            ],
            [
                'title' => 'Double Your Points When Paying at Spinneys',
                'description' => 'Pay by scanning your Suyool QR at checkout & instantly Double your Spinneys Points.',
            ],
        ];
        $topButton = "Open Suyool Account";

        $parameters = [
            'faq' => $faq,
            'bgColor' => 'bg-white',
            'btnBgColor' => 'bg-blue',
            'metaimage' => 'build/images/spinneys/meta-imagespinneys-min.png',
            'descmeta' => 'Double your Spinneys points when you pay with Suyool app',
            'howToGetTitle' => $howToGetTitle,
            'howToGetDesc' => $howToGetDesc,
            'howToGetText' => $howToGetText,
            'howToGet' => $howToGet,
            'topButton' => $topButton,
        ];
        return $this->render('spinneys/index.html.twig', $parameters);
    }

    /**
     * @Route("/spinneys-offer-suyoolers", name="app_spinneys_suyoolers")
     */
    public function spinneysPromotionSuyoolers()
    {
        $faq = [
            "ONE" => [
                "Title" => "How can I pay with Suyool at Spinneys?",
                "Desc" => "There are three ways to pay at Spinneys with Suyool: Use your Suyool Visa Platinum Card in USD, utilize the Suyool QR payment tool, or provide your phone number to receive a payment request and accept it directly on your phone."
            ],
            "TWO" => [
                "Title" => "How do I double my Spinneys points with Suyool?",
                "Desc" => "To double your Spinneys points, use the Suyool QR payment tool. This offer is valid from December 1 to January 6."
            ],
            "THREE" => [
                "Title" => "What is Suyool QR?",
                "Desc" => "Suyool QR is a secure and convenient payment tool offered by Suyool. It’s a QR code-based payment method that allows users to make transactions by scanning a QR code at participating merchants."
            ],
            "FOUR" => [
                "Title" => "How to find merchants that have Suyool as a payment method?",
                "Desc" => "Discover merchants, like Spinneys, accepting Suyool QR directly on the app’s Discovery tab."
            ],
            "FIVE" => [
                "Title" => "What is the auto-conversion feature?",
                "Desc" => "The auto-conversion feature automatically exchanges between the wallets (LBP & USD) while paying in person using the QR code in case the amount is not enough. It only exchanges the needed amount to execute the operation."
            ]
        ];

        $howToGetTitle = "HOW TO GET SUYOOL?";
        $howToGetDesc = "3 Easy Steps To Pay at Spinneys with Suyool QR";
        $howToGetText = "Double your Spinneys points when paying with Suyool QR from December 1st to January 6th";
        $howToGet = [
            [
                'title' => 'Shop At Spinneys',
                'description' => 'Indulge in your Spinneys shopping experience and fill your cart with all your favorite items.',
            ],
            [
                'title' => 'Scan Suyool QR At Checkout',
                'description' => 'In the ’S tab,’ slide the slider from the top, then tap to activate your QR Code with your biometrics. Present this code to the cashier to complete payment.',
            ],
            [
                'title' => 'Double Your Spinneys Points Effortlessly',
                'description' => 'Scan your Suyool QR at Spinneys between December 1st and January 6th to double your Spinneys Points with every purchase.',
            ],
        ];

        $topButton = "Pay With Suyool App";

        $suyoolerPopup = [
            'title' => 'Pay With Suyool App',
            'description' => 'Scan to open the app & pay with Suyool QR at partner merchants!',
        ];

        $parameters = [
            'faq' => $faq,
            'bgColor' => 'bg-white',
            'btnBgColor' => 'bg-blue',
            'metaimage' => 'build/images/spinneys/meta-imagespinneys-min.png',
            'descmeta' => 'Double your Spinneys points when you pay with Suyool app',
            'howToGetTitle' => $howToGetTitle,
            'howToGetDesc' => $howToGetDesc,
            'howToGetText' => $howToGetText,
            'howToGet' => $howToGet,
            'topButton' => $topButton,
            'suyoolerPopup' => $suyoolerPopup,
        ];
        return $this->render('spinneys/index.html.twig', $parameters);
    }

    /**
     * @Route("/2xPoints", name="app_doubleyourpoints", requirements={"_lowercase_path"=true})
     * @Route("/2xpoints", name="app_doubleyourpoints_case_insensitive", requirements={"_lowercase_path"=true})
     */
    public function doublePoints()
    {
        return $this->redirectToRoute("app_spinneys");
    }

    /**
     * @Route("/medco-suyoolpayment", name="app_medco_suyoolpayment")
     */
    public function medcoSuyoolPayment()
    {
        $faq = [
            "ONE" => [
                "Title" => "How can I pay with Suyool at Medco?",
                "Desc" => "There are three ways to pay at Medco with Suyool: Use your Suyool Visa Platinum International Card in USD, utilize the Suyool QR payment tool, or provide your phone number to receive a payment request and accept it directly on your phone."
            ],
            "TWO" => [
                "Title" => "What is Suyool QR?",
                "Desc" => "Suyool QR is a secure and convenient payment tool offered by Suyool. It’s a QR code-based payment method that allows users to make transactions by scanning a QR code at participating merchants."
            ],
            "THREE" => [
                "Title" => "How to find merchants that have Suyool as a payment method?",
                "Desc" => "Discover merchants, like Medco, accepting Suyool QR directly on the app’s Discovery tab."
            ],
            "FOUR" => [
                "Title" => "What is the auto-conversion feature?",
                "Desc" => "The auto-conversion feature automatically exchanges between the wallets (LBP & USD) while paying in person using the QR code in case the amount is not enough. It only exchanges the needed amount to execute the operation."
            ],
        ];

        $howToGetTitle = "HOW TO GET SUYOOL?";
        $howToGetDesc = "3 Easy Steps";
        $howToGetText = "";
        $howToGet = [
            [
                'title' => 'Open an account in minutes from the comfort of your home',
                'description' => 'All you need is your smartphone, a Lebanese number & a legal identification document (ID or Passport).',
            ],
            [
                'title' => 'Add Money To Your Account',
                'description' => 'Via cash deposit at more than 700+ BOB Finance outlets or online with any debit or credit card (USD/LBP).',
            ],
            [
                'title' => 'Double Your Points When Paying at Spinneys',
                'description' => 'Pay by scanning your Suyool QR at checkout & instantly Double your Spinneys Points.',
            ],
        ];
        $topButton = "Open Suyool Account";

        $remoteTitle = "REMOTE PAYMENTS POSSIBLE";
        $remoteSubTitle = "How to Make Remote Payments Using Request To Pay (RTP)?";
        $remoteDesc = "If you’re short on funds to pay at merchants, request payment by providing your friend’s number for them to cover the expense.";
        $remoteImg = "build/images/medco-payment/remote-payment.jpg";

        $parameters = [
            'faq' => $faq,
            'bgColor' => 'bg-white',
            'btnBgColor' => 'bg-blue',
            'metaimage' => 'build/images/spinneys/meta-imagespinneys-min.png',
            'descmeta' => 'Double your Spinneys points when you pay with Suyool app',
            'howToGetTitle' => $howToGetTitle,
            'howToGetDesc' => $howToGetDesc,
            'howToGetText' => $howToGetText,
            'howToGet' => $howToGet,
            'topButton' => $topButton,
            'remoteTitle' => $remoteTitle,
            'remoteSubTitle' => $remoteSubTitle,
            'remoteDesc' => $remoteDesc,
            'remoteImg' => $remoteImg,

        ];
        return $this->render('medco-payment/index.html.twig', $parameters);
    }


    /**
     * @Route("/use-visa-card", name="ma_tBank_card")
     */
    public function MatBankCard()
    {
        $faq = [
            "ONE" => [
                "Title" => "Can anyone request the Suyool Visa platinum card?",
                "Desc" => "Once successfully registered on the Suyool app, anyone can request the Suyool Visa Platinum card and enjoy its privileges."
            ],
            "TWO" => [
                "Title" => "How do I request my Suyool Visa Platinum card?",
                "Desc" => "Once your information is validated & confirmed, you can directly request your Suyool Visa Platinum debit card from your app. Once your request is approved, your card will be delivered to your address for free."
            ],
            "THREE" => [
                "Title" => "What is the fee of requesting the Suyool Visa Platinum card?",
                "Desc" => "The fee for requesting your Suyool Debit Card is $12 to be paid annually."
            ],
            "FOUR" => [
                "Title" => "Can I use the card online?",
                "Desc" => "Yes, you can use the Suyool Visa Platinum card online."
            ],
            "FIVE" => [
                "Title" => "Can I use the card internationally",
                "Desc" => "Yes, you can use your Suyool Visa Platinum card anywhere Visa is accepted."
            ],
            "SIX" => [
                "Title" => "Is the Suyool Visa Platinum card an international card?",
                "Desc" => "Yes! The Suyool Visa Platinum card is an international fresh USD debit card."
            ],
           "SEVEN" => [
               "Title" => "What currencies does the Suyool Visa Card accept?",
               "Desc" => "You can pay in any currency with Suyool Visa card (including LBP)."
           ],
        ];


        $parameters = [
            'barBgColor' => 'barWhite',
            'visa' => true,
            'faq' => $faq,
            'title' => 'Use Suyool Visa Card',
            'desc' => 'In Lebanon & abroad, in-store & online',
            'descmeta' => 'In Lebanon & abroad, in-store & online',
            'matBank' => true,
            'metaimage' => 'build/images/ma-tbank/ma-tBank-metaimage2-min.png',
        ];
        return $this->render('ma-tBank-card/maTbanikCard.html.twig', $parameters);
    }

    /**
     * @Route("/money-in-safe-hands-no-bank", name="app_ma_tBank")
     */
    public function matBank()
    {

        $parameters = [
            'barBgColor' => 'barWhite',
            'bgColor' => 'bg-white',
            'btnBgColor' => 'bg-blue',
            'homepage' => true,
            'matBank' => true,
            'title' => 'Your Money is in Safe Hands, Yours!',
            'descmeta' => 'Suyool Visa: Accepted worldwide, make transactions, transfer money to any Lebanese number, and enjoy various features—all in one app.',
            'metaimage' => 'build/images/ma-tbank/ma-tBank-metaimage-min.png',
        ];

        return $this->render('MatBank/index.html.twig', $parameters);
    }

    /**
     * @Route("/SOA", name="app_soa")
     */
    public function soa(Request $request, SuyoolServices $suyoolServices, NotificationServices $notificationServices)
    {
        if (isset($_POST['infoString'])) {
            $decrypted_string = SuyoolServices::decrypt($_POST['infoString']);
            $suyoolUserInfo = explode("!#!", $decrypted_string);
            $devicetype = stripos($_SERVER['HTTP_USER_AGENT'], $suyoolUserInfo[1]);
            if ($notificationServices->checkUser($suyoolUserInfo[0], $suyoolUserInfo[2]) && $devicetype) {
                $soaids = $request->query->get("Id") ?? $request->query->get("id") ?? null;
                $data = $suyoolServices->getUsersSoa($soaids);
                $parameters = [
                    'data' => @$data,
                    'device'=>$suyoolUserInfo[1]
                ];
                return $this->render('soa/soa.html.twig', $parameters);
            }else{
                return $this->redirectToRoute("app_ToTheAPP");
            }
        } else return $this->render('ExceptionHandling.html.twig');
    }
    /**
     * @Route("/api/exchange-rates", name="api_exchange_rates")
     */
    public function exchangeRates(Request $request): Response
    {
        try{
            $data = json_decode($request->getContent());
            if (empty($data)) {
                // If the data is empty, return an empty data response
                $this->loggerInterface->error('Empty body');
                return new JsonResponse(['message' => 'Empty data'], Response::HTTP_BAD_REQUEST);
            }
            $this->loggerInterface->info(json_encode($data));
            $buyRate = $data->buyRate;
            $sellRate = $data->sellRate;
            $date =  $data->date;
            $concat = $buyRate . $sellRate . $_ENV['CERTIFICATE'] ;
            $secureHash = base64_encode(hash('sha512', $concat, true));
            $this->loggerInterface->info("The secure hash from our side is : {$secureHash}");
            if($secureHash ==  $data->secureHash) {
    
                $serverTimeZone = new \DateTimeZone('UTC');  // Replace with your server's time zone
                $currentTimestamp = time() + $serverTimeZone->getOffset(new DateTime());
                $lastUpdate = strtotime($date);
                $timeDifference = $currentTimestamp - $lastUpdate;
    
                $seconds = $timeDifference % 60;
                $minutes = floor(($timeDifference % 3600) / 60);
                $hours = floor(($timeDifference % (60 * 60 * 24)) / (60 * 60));
                $days = floor($timeDifference / (60 * 60 * 24));
    
                if ($days > 0) {
                    $timeDifferenceString = "Updated $days day" . ($days > 1 ? 's' : '') . " ago";
                } elseif ($hours > 0) {
                    $timeDifferenceString = "Updated $hours hr" . ($hours > 1 ? 's' : '') . " ago";
                } elseif ($minutes > 0) {
                    $timeDifferenceString = "Updated $minutes min ago";
                } else {
                    $timeDifferenceString = "Updated $seconds sec ago";
                }
    
                $responseData = [
                    'buyRate' => $buyRate,
                    'sellRate' => $sellRate,
                    'date' => $timeDifferenceString
                ];
    
                $cacheKey = 'exchangeRates';
                $cacheItem = $this->memcachedCache->getItem($cacheKey);
                $cacheItem->set($responseData);
                $this->memcachedCache->save($cacheItem);
                $this->loggerInterface->info('Success');
                return new JsonResponse(['message' => 'Success: Data updated'], Response::HTTP_OK);
            }else{
                $this->loggerInterface->info('Forbidden incorrect secureHash');
                return new JsonResponse(['message' => 'Forbidden incorrect secureHash'], Response::HTTP_BAD_REQUEST);
            }
        }catch(Exception $e)
        {
            $this->loggerInterface->error($e->getMessage());
            return new JsonResponse([
                'status'=>false,
                'message'=>$e->getMessage()
            ],500);
        }
    }

    /**
     * @Route("/pay-abroad-1", name="app_payAbroad_1")
     */
    public function payAbroad_1()
    {
        $parameters = [
            'title'=> "<span>Pay Abroad</span> With Suyool Card",
            'desc'=> "AKID YOU CAN! The Suyool Visa Platinum Card accepts all currencies and can be used worldwide wherever Visa is accepted.",
            'className' => 'payAbroad1',
            'btnColor' => 'btn-white',
            'faq'=> $this->paySuyoolFaq,
            'visa' => true,
        ];

        return $this->render('pay-suyool/index.html.twig', $parameters);
    }

    /**
     * @Route("/pay-abroad-2", name="app_payAbroad_2")
     */
    public function payAbroad_2()
    {
        $parameters = [
            'title' => "<span>Pay Abroad</span> With Suyool Card",
            'desc' => "AKID YOU CAN! The Suyool Visa Platinum Card accepts all currencies and can be used worldwide wherever Visa is accepted.",
            'className' => 'payAbroad2',
            'btnColor' => 'btn-blue',
            'faq'=> $this->paySuyoolFaq,
            'visa' => true,
            'greyBack' => true,
            'barBgColor' => 'barBlue'
        ];

        return $this->render('pay-suyool/index.html.twig', $parameters);
    }

    /**
     * @Route("/pay-abroad-3", name="app_payAbroad_3")
     */
    public function payAbroad_3()
    {
        $parameters = [
            'title' => "<span>Pay Abroad</span> With Suyool Card",
            'desc' => "AKID YOU CAN! The Suyool Visa Platinum Card accepts all currencies and can be used worldwide wherever Visa is accepted.",
            'className' => 'payAbroad3',
            'btnColor' => 'btn-white',
            'faq'=> $this->paySuyoolFaq,
            'barBgColor' => 'barWhite',
            'visa' => true,
        ];

        return $this->render('pay-suyool/index.html.twig', $parameters);
    }

    /**
     * @Route("/pay-abroad-4", name="app_payAbroad_4")
     */
    public function payAbroad_4()
    {
        $parameters = [
            'title' => "<span>Pay Abroad</span> With Suyool Card",
            'desc' => "AKID YOU CAN! The Suyool Visa Platinum Card accepts all currencies and can be used worldwide wherever Visa is accepted.",
            'className' => 'payAbroad4',
            'btnColor' => 'btn-white',
            'faq'=> $this->paySuyoolFaq,
            'barBgColor' => 'barWhite',
            'visa' => true,
        ];

        return $this->render('pay-suyool/index.html.twig', $parameters);
    }

    /**
     * @Route("/pay-online-1", name="app_payAbroad_5")
     */
    public function payAbroad_5()
    {
        $parameters = [
            'title' => "<span>Pay Online</span> With Suyool Card",
            'desc' => "AKID YOU CAN! The Suyool Visa Platinum Card is your ticket to seamless online payments. Whether it’s a Netflix subscription, online shopping, ordering food online, or booking flights, we’ve got you covered!",
            'className' => 'payOnline1',
            'btnColor' => 'btn-white',
            'faq'=> $this->paySuyoolFaq,
            'barBgColor' => 'barWhite',
            'visa' => true,
        ];

        return $this->render('pay-suyool/index.html.twig', $parameters);
    }

    /**
     * @Route("/pay-online-2", name="app_payAbroad_6")
     */
    public function payAbroad_6()
    {
        $parameters = [
            'title' => "<span>Pay Online</span> With Suyool Card",
            'desc' => "AKID YOU CAN! The Suyool Visa Platinum Card is your ticket to seamless online payments. Whether it’s a Netflix subscription, online shopping, ordering food online, or booking flights, we’ve got you covered!",
            'className' => 'payOnline2',
            'btnColor' => 'btn-white',
            'faq'=> $this->paySuyoolFaq,
            'barBgColor' => 'barWhite',
            'visa' => true,
        ];

        return $this->render('pay-suyool/index.html.twig', $parameters);
    }

    /**
     * @Route("/pay-online-3", name="app_payAbroad_7")
     */
    public function payAbroad_7()
    {
        $parameters = [
            'title' => "<span>Pay Online</span> With Suyool Card",
            'desc' => "AKID YOU CAN! The Suyool Visa Platinum Card is your ticket to seamless online payments. Whether it’s a Netflix subscription, online shopping, ordering food online, or booking flights, we’ve got you covered!",
            'className' => 'payOnline3',
            'btnColor' => 'btn-white',
            'faq'=> $this->paySuyoolFaq,
            'barBgColor' => 'barWhite',
            'visa' => true,
        ];

        return $this->render('pay-suyool/index.html.twig', $parameters);
    }

    /**
     * @Route("/pay-even-lbp-1", name="app_payAbroad_8")
     */
    public function payAbroad_8()
    {
        $parameters = [
            'title' => "<span>Pay In LBP</span> With Suyool Card",
            'desc' => "AKID YOU CAN! The Suyool Visa Platinum Card works in all currencies even in LBP! Feel free to select your preferred currency when making payments.",
            'className' => 'payLbp1',
            'btnColor' => 'btn-white',
            'faq'=> $this->paySuyoolFaq,
            'barBgColor' => 'barWhite',
            'visa' => true,
        ];

        return $this->render('pay-suyool/index.html.twig', $parameters);
    }

    /**
     * @Route("/pay-even-lbp-2", name="app_payAbroad_9")
     */
    public function payAbroad_9()
    {
        $parameters = [
            'title' => "<span>Pay In LBP</span> With Suyool Card",
            'desc' => "AKID YOU CAN! The Suyool Visa Platinum Card works in all currencies even in LBP! Feel free to select your preferred currency when making payments.",
            'className' => 'payLbp2',
            'btnColor' => 'btn-white',
            'faq'=> $this->paySuyoolFaq,
            'barBgColor' => 'barWhite',
            'visa' => true,
        ];

        return $this->render('pay-suyool/index.html.twig', $parameters);
    }

    /**
     * @Route("/access-airport-lounge-1", name="app_payAbroad_10")
     */
    public function payAbroad_10()
    {
        $parameters = [
            'title' => "Enjoy <span>Free Lounge Access</span> With Suyool Card",
            'desc' => "Access over 25+ airport lounges worldwide simply by presenting your boarding pass and Suyool Visa Platinum card to the lounge attendant!",
            'className' => 'payLounge1',
            'btnColor' => 'btn-white',
            'faq'=> $this->paySuyoolFaq,
            'barBgColor' => 'barWhite',
            'visa' => true,
        ];

        return $this->render('pay-suyool/index.html.twig', $parameters);
    }

    /**
     * @Route("/access-airport-lounge-2", name="app_payAbroad_11")
     */
    public function payAbroad_11()
    {
        $parameters = [
            'title' => "Enjoy <span>Free Lounge Access</span> With Suyool Card",
            'desc' => "Access over 25+ airport lounges worldwide simply by presenting your boarding pass and Suyool Visa Platinum card to the lounge attendant!",
            'className' => 'payLounge2',
            'btnColor' => 'btn-white',
            'faq'=> $this->paySuyoolFaq,
            'barBgColor' => 'barWhite',
            'visa' => true,
        ];

        return $this->render('pay-suyool/index.html.twig', $parameters);
    }

    /**
     * @Route("/access-airport-lounge-3", name="app_payAbroad_12")
     */
    public function payAbroad_12()
    {
        $parameters = [
            'title' => "Enjoy <span>Free Lounge Access</span> With Suyool Card",
            'desc' => "Access over 25+ airport lounges worldwide simply by presenting your boarding pass and Suyool Visa Platinum card to the lounge attendant!",
            'className' => 'payLounge3',
            'btnColor' => 'btn-white',
            'faq'=> $this->paySuyoolFaq,
            'barBgColor' => 'barWhite',
            'visa' => true,
        ];

        return $this->render('pay-suyool/index.html.twig', $parameters);
    }

}
