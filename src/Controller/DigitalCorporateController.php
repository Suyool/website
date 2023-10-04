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
        $parameters['merchantImages'] = $merchantImages;

        return $this->render('digitalCorporate/index.html.twig',$parameters);
    }
}
