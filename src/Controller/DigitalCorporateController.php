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
     * @Route("/digital-corporate-account", name="app_digital_corporate")
     */
    public function digitalCorporate(Request $request,TranslatorInterface $translatorInterface): Response
    {
        $parameters = $this->trans->translation($request, $translatorInterface);
        $translatorInterface->setLocale("en");
        $parameters['lang']="en";
        $parameters['metaimage']="build/images/digital_corporate/digitalCorp-meta.png";
        $parameters['descmeta']="Conduct transactions, and oversee your business’s revenues, expenses, and activities directly from one place - your Suyool Corporate Dashboard. Ideal for businesses of all sizes, from small to large.";
        $merchantImages = [
            'merchants/alfa.png',
            'merchants/spinneys.png',
            'merchants/loto.png',
            'merchants/medco.png',
            'merchants/charcutier.png',
        ];
        $payrollContent = [
            'title' => 'Payroll in a glance',
            'subTitle' => 'Payroll solution that allows you to disburse salaries directly into the Mobile Wallet Accounts of your employees',
            'checks' => [
                'Avoid cash management problems.',
                'Payroll is disbursed to accounts same day upon receipt of request.',
                'Simplified enrollment process for employees.',
                'Salary will be directly available in employees’ wallet accounts & is instantly accessible through Suyool app & card.',
            ],
        ];
        $parameters['payrollImagePath'] = 'payrollGlance.png';

        $financeContent = [
            'title' => 'Manage all your financials from one dashboard',
            'subTitle' => 'Break free from traditional banking. Dive into the new wave',
            'checks' => [
                'Real-time account overview.',
                'Free & instant transfers to any other Suyool account (Suppliers).',
                '24/7 instant exchange (USD/LBP) at live parallel market rate.',
                'Cash out anytime with insured delivery.',
            ],
        ];
        $parameters['financeImagePath'] = 'manageDash.png';

        $platinumCard = [
            'title' => 'Suyool Corporate Platinum Debit Card',
            'subTitle' => 'The best way to enhance departments’ autonomy',
            'checks' => [
                'Request as many cards as needed for your team.',
                'Set spending limits for each corporate card.',
                'Real-time tracking of corporate cards activities.',
                'Improve your budgeting & cash flow management.',
            ],
        ];
        $parameters['platinumCardImagePath'] = 'corporate-plat-master.png';

        $bookKeeping = [
            'title' => 'Keep Track of <br> Your Money In Real Time',
            'subTitle' => 'Experience seamless financial control',
            'checks' => [
                'Keep track of cash flow, expenses, revenue.',
                'Get complete visibility of your business operation.',
                'Automatic real-time transaction recording for accurate bookkeeping.',
                'Configure & set accesses for every role.',
                'Avoid double entries & human errors.',
            ],
        ];
        $parameters['liveOverwlImagePath'] = 'keepTrackMoney.png';



        $merchantDash = [
            'title' => 'Merchant Dashboard <br> for a 360° sales overview',
            'subTitle' => 'Access data from all sales channels in one place',
            'checks' => [
                'Track sales in real-time.',
                'Auto-exchange at live parallel market rate.',
                'Instant availability of funds in both currencies.',
                'Real-time reporting for sales per branch & till.',
            ],
        ];
        $parameters['merchantDashImagePath'] = 'merchant-dashboard.png';


        $uniqueFeatures = [
            [
                'title' => 'Apply to Suyool in Minutes',
                'description' => 'Without having to go to a branch office, all the financial services you need are now at hand!',
                'image' => 'build/images/digital_corporate/numb1.svg',
            ],
            [
                'title' => 'Application will be reviewed',
                'description' => 'Our compliance officer will review your application within 4-5 Business days.',
                'image' => 'build/images/digital_corporate/numb2.svg',
            ],
            [
                'title' => 'Your business account is open',
                'description' => 'Start setting dashboard roles & get control over your business finances.',
                'image' => 'build/images/digital_corporate/numb3.svg',
            ],
        ];
        $parameters['uniqueFeaturesTitle'] = 'And have more control over your business finances.

';

        $safeHands = [
            [
                'title' => 'Cybersecurity at International Standards',
                'image' => 'build/images/digital_corporate/cybersecurityStandars.svg',
                'description' => 'Our IT security is regularly audited by Grant Thornton LLP.',
            ],
            [
                'title' => 'Regulated by BDL',
                'image' => 'build/images/digital_corporate/regulatedBDL.svg',
                'description' => 'Suyool is a financial institution licensed by Banque Du Liban.',
            ],
            [
                'title' => '24/7 Support',
                'image' => 'build/images/digital_corporate/support24-7.svg',
                'description' => 'A dedicated team is available 24/7 to answer your questions.',
            ],
        ];

        $benefits = [
            [
                'image' => 'build/images/digital_corporate/best-user-exp.svg',
                'title' => 'Best User Experience',
                'description' => 'Optimal user experience for your business transactions.',
            ],
            [
                'image' => 'build/images/digital_corporate/cashless-transactions.svg',
                'title' => 'Cashless transactions',
                'description' => 'Instant and available in real-time.',
            ],
            [
                'image' => 'build/images/digital_corporate/instant-funds.svg',
                'title' => 'Instant availabilities',
                'description' => 'Once they are in your account,<br>The money is yours to use.',
            ],
        ];
        $parameters['benefitsTitle'] = 'Benefits';
        $parameters['benefitsSubTitle'] = 'Our benefits?';
        $infoPurpleSection = [
            'topTitle' => 'INTEGRATED POS',
            'title' => 'Suyool POS: The Optimal Choice for a Seamless Customer Experience',
            'text' => 'Enhance your customers’ shopping journey! With Suyool POS, whether they’re shopping in-store, online, on desktop or mobile, they’ll enjoy a seamless omnichannel payment experience complete with dual currency options.',
            'buttonText' => 'Learn More',
            'buttonLink' => '/omnichannel', // Update the link accordingly
        ];

        $infoSection = [
            'title' => '',
            'items' => [
                [
                    'image' => 'build/images/digital_corporate/card1.svg',
                    'description' => 'Corporate Platinum Debit Card',
                ],
                [
                    'image' => 'build/images/digital_corporate/payrollSolution.svg',
                    'description' => 'Payroll Solution',
                ],
                [
                    'image' => 'build/images/digital_corporate/localSuppliers.svg',
                    'description' => 'Pay Local Suppliers',
                ],
                [
                    'image' => 'build/images/digital_corporate/request-bulk.svg',
                    'description' => 'Request Money in Bulk From Clients',
                ],
                [
                    'image' => 'build/images/digital_corporate/exchangeusdlbp.svg',
                    'description' => 'Exchange USD/LBP',
                ],
                [
                    'image' => 'build/images/digital_corporate/cashouticon.svg',
                    'description' => 'Cash Out',
                ],
            ],
        ];
        $parameters['infoSection']= $infoSection;

        $parameters['topSectionTitle'] = "Digital Corporate Account For Cashless Transactions";
        $parameters['topSectionDesc']  = "Conduct transactions, and oversee your business’s revenues, expenses, and activities directly from one place - your Suyool Corporate Dashboard. Ideal for businesses of all sizes, from small to large.";
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
        $parameters['corporateBTn'] ='Apply as Corporate';
        $parameters['barBgColor'] ='barWhite';

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
        $parameters['metaimage']="build/images/corporate_omnichanel.png";
        $parameters['descmeta']="Omnichannel, Secure & Instant Payments";
        $merchantImages = [
            'merchants/alfa.png',
            'merchants/spinneys.png',
            'merchants/loto.png',
            'merchants/medco.png',
            'merchants/charcutier.png',
        ];

        $infoUnderTopSection = [
            [
                'icon' => 'build/images/digital_corporate/acceptDual.svg',
                'text' => 'Accept Dual Currencies LBP/ USD',
            ],
            [
                'icon' => 'build/images/digital_corporate/omnichanelPay.svg',
                'text' => 'Omnichannel Payment In-Store, Mobile & Online',
            ],
            [
                'icon' => 'build/images/digital_corporate/instantAvailb.svg',
                'text' => 'Instant Availability Funds Upon Payments',
            ],
        ];

        $parameters['infoUnderTopSection'] = $infoUnderTopSection;

        $financeContent = [
            'title' => 'Integrated POS for a <br> Seamless In-Store Experience',
            'subTitle' => 'Gain new loyal customers with our unique shopping experience',
            'checks' => [
                'Dual Currency to give both options to customers.',
                'QR Payment is done by scanning specific users’ QR codes.',
                'Transaction History for a full overview of transactions.',
                'Instant Reversal feature with secured access.',
            ],
        ];
        $parameters['financeImagePath'] = 'integreatedPOS.png';


        $payrollContent = [
            'title' => 'Online Payment Gateway For Websites & Apps',
            'subTitle' => 'Safe & effortless online experience for your customers',
            'checks' => [
                'Payment online with Suyool on desktop is done by scanning the QR code displayed at checkout.',
                'Payment online with Suyool on mobile, is done by just taping on the received payment request on Suyool App. A one-step checkout process.',
            ],
        ];
        $parameters['payrollImagePath'] = 'online-pay-gatw.png';

        $bookKeeping = [
            'title' => 'Live Overview of <br> Sales Transactions',
            'subTitle' => 'All accessible from one place',
            'checks' => [
                'Free & instant transfers to any other Suyool account (Suppliers).',
                '24/7 instant exchange (USD/LBP) at live parallel market rate.',
                'Cash out anytime with insured delivery.',
                'Real-time account overview.',
            ],
        ];
        $parameters['liveOverwlImagePath'] = 'live-overw-tran.png';


        $merchantDash = [
            'title' => 'Digital Corporate Account <br> for all your financials',
            'subTitle' => 'Break free from traditional banking. Dive into the new wave.',
            'checks' => [
                'Free & instant transfers to any other Suyool account (Suppliers).',
                '24/7 instant exchange (USD/LBP) at live parallel market rate.',
                'Cash out anytime with insured delivery.',
                'Real-time account overview.',
            ],
        ];
        $parameters['merchantDashImagePath'] = 'digital-corp-account.png';

        $uniqueFeatures = [
            [
                'title' => 'Apply to Suyool',
                'description' => 'Without having to go to a branch office, all the financial services you need are now at hand!',
                'image' => 'build/images/digital_corporate/numb1.svg',
            ],
            [
                'title' => 'Application reviewed & accepted',
                'description' => 'Our compliance officer will review your application within 4-5 Business days.',
                'image' => 'build/images/digital_corporate/numb2.svg',
            ],
            [
                'title' => 'Your business account is created',
                'description' => 'Instantly start benefiting from all Suyool features.',
                'image' => 'build/images/digital_corporate/numb3.svg',
            ],
        ];
        $parameters['uniqueFeaturesTitle'] = 'And have a full overview of your sales';

        $safeHands = [
            [
                'title' => 'Cybersecurity at International Standards',
                'image' => 'build/images/digital_corporate/cybersecurityStandars.svg',
                'description' => 'Our IT security is regularly audited by Grant Thornton LLP.',
            ],
            [
                'title' => 'Regulated by BDL',
                'image' => 'build/images/digital_corporate/regulatedBDL.svg',
                'description' => 'Suyool is a financial institution licensed by Banque Du Liban.',
            ],
            [
                'title' => '24/7 Support',
                'image' => 'build/images/digital_corporate/support24-7.svg',
                'description' => 'A dedicated team is available 24/7 to answer your questions.',
            ],
        ];

        $benefits = [
            [
                'image' => 'build/images/digital_corporate/noHiddFees.svg',
                'title' => 'No Hidden Fees',
                'description' => 'Full transparency of fees.',
            ],
            [
                'image' => 'build/images/digital_corporate/bulkPayReq.svg',
                'title' => 'Bulk Payment Requests',
                'description' => 'Easily request payments from clients remotely from the dashboard.',
            ],
            [
                'image' => 'build/images/digital_corporate/transHist.svg',
                'title' => 'Transaction history with instant reversal',
                'description' => 'Take full control over your transactions directly from the app.',
            ],
        ];
        $parameters['benefitsTitle'] = 'Benefits';
        $parameters['benefitsSubTitle'] = 'Your Transactions, Your Control';

        $infoPurpleSection = [
            'topTitle' => 'CORPORATE ACCOUNT',
            'title' => 'The Merchant Dashboard is Linked To The Corporate Digital Account',
            'text' => 'Payment processed at your stores, online & offline will be available instantly In your corporate account.',
            'buttonText' => 'Learn More',
            'buttonLink' => '/digital-corporate-account', // Update the link accordingly
        ];

        $parameters['topSectionTitle'] = "Omnichannel, Secure & Instant Payments";
        $parameters['topSectionDesc']  = "Provide your customers with a seamless shopping experience across all channels, including in store, mobile, and online with a 360 degree real-time overview of your sales.";
        $parameters['topSectionBtn'] = "Apply as Merchant";
//        $parameters['topSectionUnderBTnText'] ="From $20/month. Try it free for 30 days.";
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
        $parameters['corporateBTn'] ='Apply as Corporate';
        $parameters['barBgColor'] ='barWhite';

        return $this->render('digitalCorporate/omnichannel.html.twig',$parameters);
    }
}
