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
     * @Route("/send-receive-money", name="app_send_receive")
     */
    public function sendReceiveMoney(Request $request)
    {
        $parameters=array();
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
        $parameters['infoSection']= $infoSection;

        $parameters['faq']=[
            "ONE"=>[
                "Title"=>"Can I send money to any phone number?",
                "Desc"=>"Suyool users can transfer money instantly to any Lebanese mobile number from the comfort of their own home."
            ],
            "TWO"=>[
                "Title"=>"How long does it take for the money sent to arrive to the recipient?",
                "Desc"=>"The recipient will receive the money instantly once the amount has been sent."
            ],
            "THREE"=>[
                "Title"=>"Can I send money to a non Suyool user?",
                "Desc"=>"Yes, you can send money to a non Suyool user. They will receive an SMS with a link which will redirect them to a web page where they will have 2 options. They can either download the Suyool app and receive the amount on it or go to any BOB Finance cashpoint and get the amount in cash (1.5% fees apply in this case)"
            ],
            "FOUR"=>[
                "Title"=>"Is there a fee for transferring money in Suyool?",
                "Desc"=>"Transferring money through Suyool to any Lebanese number is free of charge. However if they are not a Suyool user 1.5% fees be applied."
            ],
            "FIVE"=>[
                "Title"=>"Can I send money to a person without exchanging my personal details with them?",
                "Desc"=>"Yes! You can send money to others by scanning their QR code featured on the app, without having to share your mobile number and personal details."
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
        $metaimage="build/images/platinumMastercard/metavisa.png";
        $descmeta="Start Enjoying Platinum Benefits Instantly, From Travel Discounts
        to Shopping Perks, and Elevate Your Lifestyle Beyond Imagination.";
        $visa=true;
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
            [
                'imagePath' => 'build/images/platinumMastercard/visa.png',
                'title' => 'Visa Luxury Hotel Collection',
                'points' => [
                    'Best available rate guarantee',
                    'Automatic room upgrade, and VIP guest status',
                    'Offer includes complimentary Wi-Fi, daily continental breakfast, and $25 USD credit; valid until December 31, 2023'
                ],
                'learnMoreLink' => '/',
            ],
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
                'points'=>[
                    'Avis Budget Group offers renowned car rentals globally',
                    'Visa Platinum cardholders receive a 10% discount on Budget rentals worldwide'
                ],
                'learnMoreLink' => '/',
            ],
            [
                'imagePath' => 'build/images/platinumMastercard/medical.png',
                'title' => 'Medical & travel assistance',
                'points'=>[
                    'Visa Platinum Cardholders have access to comprehensive global assistance services',
                    'Services include medical advice, referrals, and essential medicine delivery',
                    'Also provides legal referrals and interpreter services'
                ],
                'learnMoreLink' => '/',
            ],
            [
                'imagePath' => 'build/images/platinumMastercard/cart.png',
                'title' => 'Global Customer Assistance Services',
                'points'=>[
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
                    'Use promocode ‘VISABKNG’ for discounts ranging from 6% to 8%',
                    'Offer valid until December 31, 2024'
                ],
                'learnMoreLink' => '/',
            ],
            [
                'imagePath' => 'build/images/platinumMastercard/GlobalBlueVisa.png',
                'title' => 'Shopping with Global Blue',
                'points' => [
                    'Visa Cardholders receive 20% Extra Refund (up to €500) with Global Blue tax-free shopping',
                    'Use promocode ‘VISABKNG’ for discounts ranging from 6% to 8%',
                    'Offer valid until December 31, 2024',
                ],
                'learnMoreLink' => '/',
            ],
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

        $faq=[
            "ONE"=>[
                "Title"=>"How do I request my Suyool Visa Platinum card?",
                "Desc"=>"Once your information is validated & confirmed, you can directly request your Suyool Visa Platinum debit card from your app. Once your request is approved, your card will be delivered to your address for free."
            ],
            "TWO"=>[
                "Title"=>"What is the fee of requesting the Suyool Visa Platinum card?",
                "Desc"=>"The fee for requesting your Suyool Debit Card is $12 to be paid annually."
            ],
            "THREE"=>[
                "Title"=>"Can I use the card online?",
                "Desc"=>"Yes, you can use the Suyool Visa Platinum card online."
            ],
            "FOUR"=>[
                "Title"=>"Can I use the card internationally",
                "Desc"=>"Yes, you can use your Suyool Visa Platinum card anywhere Visa is accepted."
            ],
            "FIVE"=>[
                "Title"=>"Is the Suyool Visa Platinum card an international card?",
                "Desc"=>"Yes! The Suyool Visa Platinum card is an international fresh USD debit card."
            ],
            "SIX"=>[
                "Title"=>"Can I withdraw cash from an ATM in Lebanon?",
                "Desc"=>"Yes, you can withdraw cash from specific ATMs (fresh usd ones) in Lebanon with a fee of 3.75$ + 0.5% of the amount withdrawn. Some banks might charge additional fees."
            ],
        ];

        $parameters = [
            'cardData' => $cardData,
            'lifeStyleData' => $lifeStyleData,
            'protection'=>$protection,
            'title' => $title,
            'desc' => $desc,
            'barBgColor' => 'barWhite',
            'metaimage'=>$metaimage,
            'descmeta'=>$descmeta,
            'visa'=>$visa,
            'faq'=>$faq
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
        $metaimage="build/images/platinumMastercard/metavisa.png";
        $descmeta="Start Enjoying Platinum Benefits Instantly, From Travel Discounts
        to Shopping Perks, and Elevate Your Lifestyle Beyond Imagination.";
        $visa=true;
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
            [
                'imagePath' => 'build/images/platinumMastercard/visa.png',
                'title' => 'Visa Luxury Hotel Collection',
                'points' => [
                    'Best available rate guarantee',
                    'Automatic room upgrade, and VIP guest status',
                    'Offer includes complimentary Wi-Fi, daily continental breakfast, and $25 USD credit; valid until December 31, 2023'
                ],
                'learnMoreLink' => '/',
            ],
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
                'points'=>[
                    'Avis Budget Group offers renowned car rentals globally',
                    'Visa Platinum cardholders receive a 10% discount on Budget rentals worldwide'
                ],
                'learnMoreLink' => '/',
            ],
            [
                'imagePath' => 'build/images/platinumMastercard/medical.png',
                'title' => 'Medical & travel assistance',
                'points'=>[
                    'Visa Platinum Cardholders have access to comprehensive global assistance services',
                    'Services include medical advice, referrals, and essential medicine delivery',
                    'Also provides legal referrals and interpreter services'
                ],
                'learnMoreLink' => '/',
            ],
            [
                'imagePath' => 'build/images/platinumMastercard/cart.png',
                'title' => 'Global Customer Assistance Services',
                'points'=>[
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
                    'Use promocode ‘VISABKNG’ for discounts ranging from 6% to 8%',
                    'Offer valid until December 31, 2024'
                ],
                'learnMoreLink' => '/',
            ],
            [
                'imagePath' => 'build/images/platinumMastercard/GlobalBlueVisa.png',
                'title' => 'Shopping with Global Blue',
                'points' => [
                    'Visa Cardholders receive 20% Extra Refund (up to €500) with Global Blue tax-free shopping',
                    'Use promocode ‘VISABKNG’ for discounts ranging from 6% to 8%',
                    'Offer valid until December 31, 2024',
                ],
                'learnMoreLink' => '/',
            ],
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

        $faq=[
            "ONE"=>[
                "Title"=>"How do I request my Suyool Visa Platinum card?",
                "Desc"=>"Once your information is validated & confirmed, you can directly request your Suyool Visa Platinum debit card from your app. Once your request is approved, your card will be delivered to your address for free."
            ],
            "TWO"=>[
                "Title"=>"What is the fee of requesting the Suyool Visa Platinum card?",
                "Desc"=>"The fee for requesting your Suyool Debit Card is $12 to be paid annually."
            ],
            "THREE"=>[
                "Title"=>"Can I use the card online?",
                "Desc"=>"Yes, you can use the Suyool Visa Platinum card online."
            ],
            "FOUR"=>[
                "Title"=>"Can I use the card internationally",
                "Desc"=>"Yes, you can use your Suyool Visa Platinum card anywhere Visa is accepted."
            ],
            "FIVE"=>[
                "Title"=>"Is the Suyool Visa Platinum card an international card?",
                "Desc"=>"Yes! The Suyool Visa Platinum card is an international fresh USD debit card."
            ],
            "SIX"=>[
                "Title"=>"Can I withdraw cash from an ATM in Lebanon?",
                "Desc"=>"Yes, you can withdraw cash from specific ATMs (fresh usd ones) in Lebanon with a fee of 3.75$ + 0.5% of the amount withdrawn. Some banks might charge additional fees."
            ],
        ];

        $parameters = [
            'cardData' => $cardData,
            'lifeStyleData' => $lifeStyleData,
            'protection'=>$protection,
            'title' => $title,
            'desc' => $desc,
            'barBgColor' => 'barWhite',
            'metaimage'=>$metaimage,
            'descmeta'=>$descmeta,
            'visa'=>$visa,
            'faq'=>$faq,
            'canonical_url' => $canonical_url
        ];
        $parameters['hideLearnMore'] = "";

        return $this->render('homepage/visa.html.twig', $parameters);
    }


}