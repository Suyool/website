<?php

namespace App\Controller;


use App\Translation\translation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Routing\Annotation\Route;


class DigitalCorporateController extends AbstractController
{
    private $trans;

    public function __construct(translation $trans)
    {
        $this->trans = $trans;
    }
    /**
     * @Route("/digital-corporate", name="app_digital_corporate")
     */
    public function digitalCorporate(Request $request,TranslatorInterface $translatorInterface): Response
    {
        $parameters = $this->trans->translation($request, $translatorInterface);
        $translatorInterface->setLocale("en");
        $parameters['lang']="en";
        $parameters['metaimage']="build/images/alfa_employee/alfametaimage.png";
        $parameters['descmeta']="Your Payroll is now on Suyool";
        $merchantImages = [
            'merchants/alfa.png',
            'merchants/spinneys.png',
            'merchants/loto.png',
            'merchants/medco.png',
            'merchants/charcutier.png',
        ];
        $payrollContent = [
            'title' => 'Payroll in Minutes',
            'subTitle' => 'Payroll solution that allows you to disburse salaries directly into the Mobile Wallet Accounts of your employees',
            'checks' => [
                'Avoid cash management problems',
                'Payroll is disbursed to accounts same day upon receipt of request.',
                'Simplified enrollment process for employees',
            ],
        ];

        $financeContent = [
            'title' => 'Manage all your financials from one dashboard',
            'subTitle' => 'Break free from traditional banking. Dive into the new wave.',
            'checks' => [
                'Real-time account overview in real-time',
                'International transfers faster & at competitive fees',
                'Free & instant transfers to any other Suyool account (Suppliers)',
                '24/7 instant exchange (USD/LBP) at live parallel market rate',
                'Cash out anytime with insured delivery',
            ],
        ];
        $platinumCard = [
            'title' => 'Suyool Corporate Platinum Mastercard',
            'subTitle' => 'The best way to enhance departments’ autonomy',
            'checks' => [
                'The best way to enhance departments’ autonomy',
                'The best way to enhance departments’ autonomy',
                'Set spending limits for each corporate card',
                'Real-time tracking of corporate cards activities',
                'Improve your budgeting & cash flow management',
            ],
        ];
        $bookKeeping = [
            'title' => 'Simplify Bookkeeping<br> & Accounting System',
            'subTitle' => 'Experience seamless financial control',
            'checks' => [
                'Keep track of cash flow, expenses, revenue',
                'Get complete visibility of your business operation',
                'Automatic real-time transaction recording for accurate bookkeeping',
                'Configure & set accesses for every role',
                'Avoid double entries & human errors',
            ],
        ];
        $merchantDash = [
            'title' => 'Merchant Dashboard <br> for a 360° sales overview',
            'subTitle' => 'Access data from all sales channels in one place',
            'checks' => [
                'Track sales in real-time',
                'Auto-exchange at live parallel market rate',
                'Instant availability of funds in both currencies',
                'Real-time reporting for sales per branch & till',
            ],
        ];
        $uniqueFeatures = [
            [
                'title' => 'Apply to Suyool in Minutes',
                'description' => 'Without having to go to a branch office, all the banking services you need are now at hand!',
                'image' => 'build/images/homepage/fresh-account.svg',
            ],
            [
                'title' => 'Application will be reviewed',
                'description' => 'Our compliance officer will review your application within 4-5 Business days.',
                'image' => 'build/images/homepage/exchange.svg',
            ],
            [
                'title' => 'Your business account is open',
                'description' => 'Start setting dashboard roles & get control over your business finances.',
                'image' => 'build/images/homepage/fresh-mastercard.svg',
            ],
        ];
        $safeHands = [
            [
                'title' => 'Your Funds Are Secure',
                'image' => 'build/images/alfa_employee/secure.png',
                'description' => 'Your funds are accessible anytime, anywhere.',
            ],
            [
                'title' => 'Regulated by BDL',
                'image' => 'build/images/alfa_employee/safe.png',
                'description' => 'Suyool is a financial institution licensed by Banque Du Liban.',
            ],
            [
                'title' => '24/7 Support',
                'image' => 'build/images/alfa_employee/convenience.png',
                'description' => 'A dedicated team is available 24/7 to answer your questions.',
            ],
        ];

        $benefits = [
            [
                'title' => 'Competitive Fees',
                'description' => 'Full transparency of fees',
            ],
            [
                'title' => 'Cashless transactions',
                'description' => 'Instant and available in real-time',
            ],
            [
                'title' => 'Instant availabilities',
                'description' => 'Once they are in your account,<br>The money is yours to use',
            ],
        ];
        $parameters['benefitsTitle'] = 'Benefits';
        $parameters['benefitsSubTitle'] = 'Our benefits?';
        $infoPurpleSection = [
            'topTitle' => 'INTEGRATED POS',
            'title' => 'Suyool POS: The Optimal Choice for a Seamless Customer Experience',
            'text' => 'Enhance your customers’ shopping journey! With Suyool POS, whether they’re shopping in-store, online, on desktop or mobile, they’ll enjoy a seamless omnichannel payment experience complete with dual currency options.',
            'buttonText' => 'Learn More',
            'buttonLink' => '/legal_enrollment', // Update the link accordingly
        ];

        $parameters['topSectionTitle'] = "Digital Corporate Account For Cashless Transactions";
        $parameters['topSectionDesc']  = "Avoid cash flow problems & keep track of your business revenues, expenses & transactions directly from one place - your corporate Suyool dashboard. Whether you have a small business or a large corporation.";
        $parameters['topSectionBtn'] = "Apply as Corporate";
        $parameters['payrollContent'] = $payrollContent;
        $parameters['financeContent'] = $financeContent;
        $parameters['platinumCard'] = $platinumCard;
        $parameters['bookKeeping'] = $bookKeeping;
        $parameters['merchantDash'] = $merchantDash;
        $parameters['uniqueFeatures'] = $uniqueFeatures;
        $parameters['safeHands'] = $safeHands;
        $parameters['benefits'] = $benefits;
        $parameters['infoPurpleSection'] = $infoPurpleSection;
        $parameters['merchantImages'] = $merchantImages;

        $parameters['title']="Digital Corporate Account For Cashless Transactions | Suyool";
        $parameters['desc']="Avoid cash flow problems & keep track of your business revenues, expenses & transactions directly from one place - your corporate Suyool dashboard. Whether you have a small business or a large corporation.";

        return $this->render('digitalCorporate/index.html.twig',$parameters);
    }
    /**
     * @Route("/omnichannel", name="app_omnichannel")
     */
    public function omnichannel(Request $request,TranslatorInterface $translatorInterface): Response
    {
        $parameters = $this->trans->translation($request, $translatorInterface);
        $translatorInterface->setLocale("en");
        $parameters['lang']="en";
        $parameters['metaimage']="build/images/alfa_employee/alfametaimage.png";
        $parameters['descmeta']="Your Payroll is now on Suyool";
        $merchantImages = [
            'merchants/alfa.png',
            'merchants/spinneys.png',
            'merchants/loto.png',
            'merchants/medco.png',
            'merchants/charcutier.png',
        ];

        $infoUnderTopSection = [
            [
                'icon' => 'build/images/alfa_employee/secure.png',
                'text' => 'Accept Dual Currencies LBP/ USD',
            ],
            [
                'icon' => 'build/images/alfa_employee/secure.png',
                'text' => 'Omnichannel Payment In-Store, Mobile & Online',
            ],
            [
                'icon' => 'build/images/alfa_employee/secure.png',
                'text' => 'Instant Availability Funds Upon Payments',
            ],
        ];

        $parameters['infoUnderTopSection'] = $infoUnderTopSection;

        $financeContent = [
            'title' => 'Integrated POS for an Omnichannel In-Store Experience',
            'subTitle' => 'Gain new loyal customers with the best seamless shopping experience',
            'checks' => [
                'Dual Currency to give both options to customers',
                'QR Payment is done by scanning specific users’ QR codes',
                'Request to Pay (RTP) where merchants send an automatic RTP & users receive an instant notification with the amount due to pay',
                'Transaction History for a full overview of transactions',
                'Instant Reversal feature with secured access',
            ],
        ];

        $payrollContent = [
            'title' => 'Online Payment Gateway For Websites & Apps',
            'subTitle' => '100% easy & secure',
            'checks' => [
                'Payment online with Suyool on desktop is done by scanning the QR code displayed at checkout.',
                'Payment online with Suyool on mobile, is done by just taping on the received payment request on Suyool App. An One-step checkout process',
            ],
        ];

        $bookKeeping = [
            'title' => 'Live Overview of <br> Sales Transactions',
            'subTitle' => 'All accessible from one place',
            'checks' => [
                'International transfers faster & at competitive fees',
                'Free & instant transfers to any other Suyool account (Suppliers)',
                '24/7 instant exchange (USD/LBP) at live parallel market rate',
                'Cash out anytime with insured delivery',
                'Real-time account overview in real-time',
            ],
        ];
        $merchantDash = [
            'title' => 'Digital Corporate Account <br> for all your financials',
            'subTitle' => 'Break free from traditional banking. Dive into the new wave.',
            'checks' => [
                'International transfers faster & at competitive fees',
                'Free & instant transfers to any other Suyool account (Suppliers)',
                '24/7 instant exchange (USD/LBP) at live parallel market rate',
                'Cash out anytime with insured delivery',
                'Real-time account overview in real-time',
            ],
        ];
        $uniqueFeatures = [
            [
                'title' => 'Apply to Suyool in Minutes',
                'description' => 'Without having to go to a branch office, all the banking services you need are now at hand!',
                'image' => 'build/images/homepage/fresh-account.svg',
            ],
            [
                'title' => 'Application will be reviewed',
                'description' => 'Our compliance officer will review your application within 4-5 Business days.',
                'image' => 'build/images/homepage/exchange.svg',
            ],
            [
                'title' => 'Your business account is open',
                'description' => 'Start setting dashboard roles & get control over your business finances.',
                'image' => 'build/images/homepage/fresh-mastercard.svg',
            ],
        ];
        $safeHands = [
            [
                'title' => 'Your Funds Are Secure',
                'image' => 'build/images/alfa_employee/secure.png',
                'description' => 'Your funds are accessible anytime, anywhere.',
            ],
            [
                'title' => 'Regulated by BDL',
                'image' => 'build/images/alfa_employee/safe.png',
                'description' => 'Suyool is a financial institution licensed by Banque Du Liban.',
            ],
            [
                'title' => '24/7 Support',
                'image' => 'build/images/alfa_employee/convenience.png',
                'description' => 'A dedicated team is available 24/7 to answer your questions.',
            ],
        ];

        $benefits = [
            [
                'title' => 'No Hidden Fees',
                'description' => 'Full transparency of fees',
            ],
            [
                'title' => 'Supplier & Client Payments',
                'description' => 'Easily settle your business dues from your corporate dashboard',
            ],
            [
                'title' => 'Expense & Spend Real-Time Overview',
                'description' => 'Take full control over your business finances',
            ],
        ];
        $parameters['benefitsTitle'] = 'Benefits';
        $parameters['benefitsSubTitle'] = 'Your Money, Your Safety';

        $infoPurpleSection = [
            'topTitle' => 'CORPORATE ACCOUNT',
            'title' => 'The Merchant Dashboard is Linked To The Corporate Digital Account',
            'text' => 'Payment processed at your stores, online & offline will be available instantly In your corporate account.',
            'buttonText' => 'Learn More',
            'buttonLink' => '/legal_enrollment', // Update the link accordingly
        ];

        $parameters['topSectionTitle'] = "Omnichannel, Secure & Instant Payments";
        $parameters['topSectionDesc']  = "Provide your customers with a seamless shopping experience across all channels, including in store, mobile, and online with a 360 degree real-time overview of your sales.";
        $parameters['topSectionBtn'] = "Apply as Merchant";
        $parameters['topSectionUnderBTnText'] ="From $20/month. Try it free for 30 days.";
        $parameters['payrollContent'] = $payrollContent;
        $parameters['financeContent'] = $financeContent;
        $parameters['bookKeeping'] = $bookKeeping;
        $parameters['merchantDash'] = $merchantDash;
        $parameters['uniqueFeatures'] = $uniqueFeatures;
        $parameters['safeHands'] = $safeHands;
        $parameters['benefits'] = $benefits;
        $parameters['infoPurpleSection'] = $infoPurpleSection;
        $parameters['merchantImages'] = $merchantImages;
        $parameters['title']="Omnichannel, Secure & Instant Payments | Suyool";
        $parameters['desc']="Provide your customers with a seamless shopping experience across all channels, including in store, mobile, and online with a 360 degree real-time overview of your sales.";

        return $this->render('digitalCorporate/omnichannel.html.twig',$parameters);
    }
}
