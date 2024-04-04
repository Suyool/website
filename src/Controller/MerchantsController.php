<?php

namespace App\Controller;

use App\Translation\translation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class MerchantsController extends AbstractController
{

    private $trans;
    private $infoSection;
    private $infoSection2;

    public function __construct(translation $trans)
    {
        $this->trans = $trans;
        $this->infoSection = [
            'title' => '6 Ways To Use Your Money',
            'items' => [
                [
                    'image' => 'build/images/alfa_employee/card3.svg',
                    'description' => 'Visa Platinum Card',
                ],
                [
                    'image' => 'build/images/alfa_employee/cashout.svg',
                    'description' => 'Free Payroll Cash Out',
                ],
                [
                    'image' => 'build/images/alfa_employee/arrow1.svg',
                    'description' => 'Send & Receive Money for Free',
                ],
                [
                    'image' => 'build/images/alfa_employee/request-money.svg',
                    'description' => 'Request Money',
                ],
                [
                    'image' => 'build/images/alfa_employee/payroll.svg',
                    'description' => 'Pay Bills',
                ],
                [
                    'image' => 'build/images/alfa_employee/payQr1.svg',
                    'description' => 'Pay with Suyool QR',
                ],
            ],
        ];

        $this->infoSection2 = [
            'title' => '6 Ways To Use Your Money',
            'items' => [
                [
                    'image' => 'build/images/alfa_employee/card3.svg',
                    'description' => 'Visa Platinum Card',
                ],
                [
                    'image' => 'build/images/alfa_employee/cashout1.svg',
                    'description' => 'Cash Withdrawal',
                ],
                [
                    'image' => 'build/images/alfa_employee/arrow1.svg',
                    'description' => 'Send & Receive Money for Free',
                ],
                [
                    'image' => 'build/images/alfa_employee/request-money.svg',
                    'description' => 'Request Money',
                ],
                [
                    'image' => 'build/images/alfa_employee/payroll.svg',
                    'description' => 'Pay Bills',
                ],
                [
                    'image' => 'build/images/alfa_employee/payQr1.svg',
                    'description' => 'Pay with Suyool QR',
                ],
            ],
        ];
    }



    /**
     * @Route("/alfa-employee", name="app_alfa_employee")
     */
    public function alfa(Request $request, TranslatorInterface $translatorInterface): Response
    {
        return $this->redirectToRoute('homepage');
        $parameters = $this->trans->translation($request, $translatorInterface);
        $translatorInterface->setLocale("en");
        $parameters['lang'] = "en";
        $parameters['metaimage'] = "build/images/alfa_employee/alfametaimage.png";
        $parameters['descmeta'] = "Your Payroll is now on Suyool";
        $parameters['faq'] = [
            "ONE" => [
                "Title" => "WHAT_IS_SUYOOL_ALFA",
                "Desc" => "SUYOOL_IS_A_CASHLESS_ECOSYSTEM_THAT_INCORPORATES_ALFA"
            ],
            "TWO" => [
                "Title" => "CAN_ANYONE_OPEN_A_SUYOOL_ACCOUNT_ALFA",
                "Desc" => "ANY_LEBANESE_CITIZEN_CAN_OPEN_A_SUYOOL_ACCOUNT_ALFA"
            ],
            "THREE" => [
                "Title" => "WHAT_ARE_THE_BENEFITS_FOR_USJ_EMPLOYEES_ALFA",
                "Desc" => "YOU_WILL_BENEFIT_FROM_A_FREE_PLATINUM_MASTERCARD_ALFA"
            ],
            "FOUR" => [
                "Title" => "IS_THERE_ANY_FEE_TO_GET_MY_SUYOOL_PLATINUM_MASTERCARD_ALFA",
                "Desc" => "YOUR_SUYOOL_MASTERCARD_WILL_BE_FREE_OF_CHARGE_ALFA"
            ],
            "FIVE" => [
                "Title" => "WHERE_CAN_I_USE_MY_SUYOOL_PLATINUM_MASTERCARD_ALFA",
                "Desc" => "YOU_CAN_USE_YOUR_SUYOOL_MASTERCARD_AT_ANY_POS_ALFA"
            ],
            "SIX" => [
                "Title" => "WHERE_CAN_I_WITHDRAW_MY_SALARY_IN_CASH_ALFA",
                "Desc" => "USERS_CAN_ACCESS_THEIR_MONEY_FROM_MORE_THAN_700_ALFA"
            ],
        ];

        $parameters['title'] = "Alfa Employee | Suyool";
        $parameters['desc'] = "Facing today’s financial challenges, we moved our payroll to Suyool. You will get your own digital dual-currency account,a Platinum Debit Card & a payment tool with the best rates available.";

        $parameters['infoSection'] = $this->infoSection;

        return $this->render('merchants/alfa.html.twig', $parameters);
    }

    /**
     * @Route("/usj", name="app_usj")
     */
    public function usj(Request $request, TranslatorInterface $translatorInterface): Response
    {
        $parameters = $this->trans->translation($request, $translatorInterface);
        //        $translatorInterface->setLocale("en");

        if ($parameters['lang'] == "en") {
            $parameters['metaimage'] = "build/images/usj/metaenglish.png";
            $parameters['descmeta'] = "Your Payroll is now on Suyool";
        } else if ($parameters['lang'] == "fr") {
            $parameters['metaimage'] = "build/images/usj/metafr.png";
            $parameters['descmeta'] = "Votre salaire est désormais sur Suyool";
        } else {
            $parameters['metaimage'] = "build/images/usj/metaarabic.png";
            $parameters['descmeta'] = "الأن راتبك على سيول!";
        }
        $parameters['faq'] = [
            "ONE" => [
                "Title" => "WHAT_IS_SUYOOL",
                "Desc" => "SUYOOL_IS_A_CASHLESS_ECOSYSTEM_THAT_INCORPORATES"
            ],
            "TWO" => [
                "Title" => "CAN_ANYONE_OPEN_A_SUYOOL_ACCOUNT",
                "Desc" => "ANY_LEBANESE_CITIZEN_CAN_OPEN_A_SUYOOL_ACCOUNT"
            ],
            "THREE" => [
                "Title" => "WHAT_ARE_THE_BENEFITS_FOR_USJ_EMPLOYEES",
                "Desc" => "YOU_WILL_BENEFIT_FROM_A_FREE_PLATINUM_MASTERCARD"
            ],
            "FOUR" => [
                "Title" => "IS_THERE_ANY_FEE_TO_GET_MY_SUYOOL_PLATINUM_MASTERCARD",
                "Desc" => "YOUR_SUYOOL_MASTERCARD_WILL_BE_FREE_OF_CHARGE"
            ],
            "FIVE" => [
                "Title" => "WHERE_CAN_I_USE_MY_SUYOOL_PLATINUM_MASTERCARD",
                "Desc" => "YOU_CAN_USE_YOUR_SUYOOL_MASTERCARD_AT_ANY_POS"
            ],
            "SIX" => [
                "Title" => "WHERE_CAN_I_WITHDRAW_MY_SALARY_IN_CASH",
                "Desc" => "USERS_CAN_ACCESS_THEIR_MONEY_FROM_MORE_THAN_700"
            ],
        ];


        $parameters['title'] = "USJ | Suyool";
        $parameters['desc'] = "Facing today’s financial challenges, we moved our payroll to Suyool.
        You will get your own digital dual-currency account, a complete payment tool
        with the best rates and a Platinum Debit Card linked to the account.";
        return $this->render('merchants/usj.html.twig', $parameters);
    }

    /**
     * @Route("/hdf", name="app_hdf")
     */
    public function hotel_dieu(Request $request, TranslatorInterface $translatorInterface): Response
    {
        $parameters = $this->trans->translation($request, $translatorInterface);
        //        $translatorInterface->setLocale("en");

        if ($parameters['lang'] == "en") {
            $parameters['metaimage'] = "build/images/payroll/hdfMeta.png";
            $parameters['descmeta'] = "Your Payroll is now on Suyool";
        } else if ($parameters['lang'] == "fr") {
            $parameters['metaimage'] = "build/images/payroll/hdfMetafr.png";
            $parameters['descmeta'] = "Votre salaire est désormais sur Suyool";
        } else {
            $parameters['metaimage'] = "build/images/payroll/hdfMeta.png";
            $parameters['descmeta'] = "الأن راتبك على سيول!";
        }
        $parameters['faq'] = [
            "ONE" => [
                "Title" => "WHAT_IS_SUYOOL",
                "Desc" => "SUYOOL_IS_A_CASHLESS_ECOSYSTEM_THAT_INCORPORATES"
            ],
            "TWO" => [
                "Title" => "CAN_ANYONE_OPEN_A_SUYOOL_ACCOUNT",
                "Desc" => "ANY_LEBANESE_CITIZEN_CAN_OPEN_A_SUYOOL_ACCOUNT"
            ],
            "THREE" => [
                "Title" => "WHAT_ARE_THE_BENEFITS_FOR_HDF_EMPLOYEES",
                "Desc" => "YOU_WILL_BENEFIT_FROM_A_FREE_PLATINUM_MASTERCARD_HDF"
            ],
            "FOUR" => [
                "Title" => "IS_THERE_ANY_FEE_TO_GET_MY_SUYOOL_PLATINUM_MASTERCARD",
                "Desc" => "YOUR_SUYOOL_MASTERCARD_WILL_BE_FREE_OF_CHARGE_HDF"
            ],
            "FIVE" => [
                "Title" => "WHERE_CAN_I_USE_MY_SUYOOL_PLATINUM_MASTERCARD",
                "Desc" => "YOU_CAN_USE_YOUR_SUYOOL_MASTERCARD_AT_ANY_POS"
            ],
            "SIX" => [
                "Title" => "WHERE_CAN_I_WITHDRAW_MY_SALARY_IN_CASH",
                "Desc" => "USERS_CAN_ACCESS_THEIR_MONEY_FROM_MORE_THAN_700"
            ],
        ];


        $parameters['title'] = "Hôtel-Dieu De France | Suyool";
        $parameters['desc'] = "Facing today’s financial challenges, we moved our payroll to Suyool.
        You will get your own digital dual-currency account, a complete payment tool
        with the best rates and a Platinum Debit Card linked to the account.";
        return $this->render('merchants/hdf.html.twig', $parameters);
    }

    /**
     * @Route("/ndj", name="app_NDj")
     */
    public function ndj(Request $request, TranslatorInterface $translatorInterface): Response
    {
        $parameters = $this->trans->translation($request, $translatorInterface);
        $translatorInterface->setLocale("fr");

        $parameters['metaimage'] = "build/images/ndj/metafr.png";
        $parameters['descmeta'] = "Votre salaire est désormais sur Suyool";

        $parameters['faq'] = [
            "ONE" => [
                "Title" => "WHAT_IS_SUYOOL",
                "Desc" => "SUYOOL_IS_A_CASHLESS_ECOSYSTEM_THAT_INCORPORATES"
            ],
            "TWO" => [
                "Title" => "CAN_ANYONE_OPEN_A_SUYOOL_ACCOUNT",
                "Desc" => "ANY_LEBANESE_CITIZEN_CAN_OPEN_A_SUYOOL_ACCOUNT"
            ],
            "THREE" => [
                "Title" => "NDJ_WHAT_ARE_THE_BENEFITS_FOR_USJ_EMPLOYEES",
                "Desc" => "YOU_WILL_BENEFIT_FROM_A_FREE_PLATINUM_MASTERCARD"
            ],
            "FOUR" => [
                "Title" => "IS_THERE_ANY_FEE_TO_GET_MY_SUYOOL_PLATINUM_MASTERCARD",
                "Desc" => "NDJ_YOUR_SUYOOL_MASTERCARD_WILL_BE_FREE_OF_CHARGE"
            ],
            "FIVE" => [
                "Title" => "WHERE_CAN_I_USE_MY_SUYOOL_PLATINUM_MASTERCARD",
                "Desc" => "YOU_CAN_USE_YOUR_SUYOOL_MASTERCARD_AT_ANY_POS"
            ],
            "SIX" => [
                "Title" => "WHERE_CAN_I_WITHDRAW_MY_SALARY_IN_CASH",
                "Desc" => "USERS_CAN_ACCESS_THEIR_MONEY_FROM_MORE_THAN_700"
            ],
        ];

        $parameters['title'] = "NDJ | Suyool";
        $parameters['desc'] = "Facing today’s financial challenges, we moved our payroll to Suyool.
        You will get your own digital dual-currency account, a complete payment tool
        with the best rates and a Platinum Debit Card linked to the account.";
        return $this->render('merchants/ndj.html.twig', $parameters);
    }

    /**
     * @Route("/elnashra", name="app_elnashra")
     */
    public function elnashra(Request $request, TranslatorInterface $translatorInterface): Response
    {

        $parameters = $this->trans->translation($request, $translatorInterface);
        $translatorInterface->setLocale("en");
        $parameters['faq'] = [
            "ONE" => [
                "Title" => "WHAT_IS_SUYOOL",
                "Desc" => "SUYOOL_IS_A_CASHLESS_ECOSYSTEM_THAT_INCORPORATES"
            ],
            "TWO" => [
                "Title" => "CAN_ANYONE_OPEN_A_SUYOOL_ACCOUNT",
                "Desc" => "ANY_LEBANESE_CITIZEN_CAN_OPEN_A_SUYOOL_ACCOUNT"
            ],
            "THREE" => [
                "Title" => "WHAT_ARE_THE_BENEFITS_FOR_ELNASHRA_EMPLOYEES",
                "Desc" => "YOU_WILL_BENEFIT_FROM_A_FREE_PLATINUM_MASTERCARD_NASHRA"
            ],
            "FOUR" => [
                "Title" => "IS_THERE_ANY_FEE_TO_GET_MY_SUYOOL_PLATINUM_MASTERCARD",
                "Desc" => "YOUR_SUYOOL_MASTERCARD_WILL_BE_FREE_OF_CHARGE_NASHRA"
            ],
            "FIVE" => [
                "Title" => "WHERE_CAN_I_USE_MY_SUYOOL_PLATINUM_MASTERCARD",
                "Desc" => "YOU_CAN_USE_YOUR_SUYOOL_MASTERCARD_AT_ANY_POS"
            ],
            "SIX" => [
                "Title" => "WHERE_CAN_I_WITHDRAW_MY_SALARY_IN_CASH",
                "Desc" => "USERS_CAN_ACCESS_THEIR_MONEY_FROM_MORE_THAN_700"
            ],
        ];

        $parameters['infoSection'] = $this->infoSection;

        $parameters['title'] = "elnashra | Suyool";
        $parameters['desc'] = "Facing today’s financial challenges, we moved our payroll to Suyool.
        You will get your own digital dual-currency account, a complete payment tool
        with the best rates and a Platinum Debit Card linked to the account.";
        return $this->render('merchants/elnashra.html.twig', $parameters);
    }

    /**
     * @Route("/lldj-employee", name="app_lldj_employee")
     */
    public function lldj(Request $request, TranslatorInterface $translatorInterface): Response
    {
        $parameters = $this->trans->translation($request, $translatorInterface);
        $translatorInterface->setLocale("en");
        $parameters['lang'] = "en";
        $parameters['metaimage'] = "build/images/lldj/metaLLDJ.jpg";
        $parameters['descmeta'] = "Your Payroll is now on Suyool";
        $parameters['faq'] = [
            "ONE" => [
                "Title" => "WHAT_IS_SUYOOL_LLDJ",
                "Desc" => "SUYOOL_IS_A_CASHLESS_ECOSYSTEM_THAT_INCORPORATES_LLDJ"
            ],
            "TWO" => [
                "Title" => "CAN_ANYONE_OPEN_A_SUYOOL_ACCOUNT_LLDJ",
                "Desc" => "ANY_LEBANESE_CITIZEN_CAN_OPEN_A_SUYOOL_ACCOUNT_LLDJ"
            ],
            "THREE" => [
                "Title" => "WHAT_ARE_THE_BENEFITS_FOR_USJ_EMPLOYEES_LLDJ",
                "Desc" => "YOU_WILL_BENEFIT_FROM_A_FREE_PLATINUM_MASTERCARD_LLDJ"
            ],
            "FOUR" => [
                "Title" => "IS_THERE_ANY_FEE_TO_GET_MY_SUYOOL_PLATINUM_MASTERCARD_LLDJ",
                "Desc" => "YOUR_SUYOOL_MASTERCARD_WILL_BE_FREE_OF_CHARGE_LLDJ"
            ],
            "FIVE" => [
                "Title" => "WHERE_CAN_I_USE_MY_SUYOOL_PLATINUM_MASTERCARD_LLDJ",
                "Desc" => "YOU_CAN_USE_YOUR_SUYOOL_MASTERCARD_AT_ANY_POS_LLDJ"
            ],
            "SIX" => [
                "Title" => "WHERE_CAN_I_WITHDRAW_MY_SALARY_IN_CASH_LLDJ",
                "Desc" => "USERS_CAN_ACCESS_THEIR_MONEY_FROM_MORE_THAN_700_LLDJ"
            ],
        ];

        $parameters['title'] = "LLDJ Employee | Suyool";
        $parameters['desc'] = "Facing today’s financial challenges, we moved our payroll to Suyool. You will get your own digital dual-currency account,a Platinum Debit Card & a payment tool with the best rates available.";
        $parameters['infoSection'] = $this->infoSection;

        return $this->render('merchants/lldj.html.twig', $parameters);
    }

    /**
     * @Route("/aramex", name="app_aramex")
     */
    public function aramex(Request $request, TranslatorInterface $translatorInterface): Response
    {
        $parameters = $this->trans->translation($request, $translatorInterface);
        $translatorInterface->setLocale("en");
        $parameters['lang'] = "en";
        $parameters['metaimage'] = "build/images/payroll/aramexMeta.png";
        $parameters['descmeta'] = "Why is Suyool the best option for your payroll?";
        $parameters['faq'] = [
            "ONE" => [
                "Title" => "WHAT_IS_SUYOOL_ARAMEX",
                "Desc" => "SUYOOL_IS_A_CASHLESS_ECOSYSTEM_THAT_INCORPORATES_ARAMEX"
            ],
            "TWO" => [
                "Title" => "CAN_ANYONE_OPEN_A_SUYOOL_ACCOUNT_ARAMEX",
                "Desc" => "ANY_LEBANESE_CITIZEN_CAN_OPEN_A_SUYOOL_ACCOUNT_ARAMEX"
            ],
            "THREE" => [
                "Title" => "WHAT_ARE_THE_BENEFITS_FOR_USJ_EMPLOYEES_ARAMEX",
                "Desc" => "YOU_WILL_BENEFIT_FROM_A_FREE_PLATINUM_MASTERCARD_ARAMEX"
            ],
            "FOUR" => [
                "Title" => "IS_THERE_ANY_FEE_TO_GET_MY_SUYOOL_PLATINUM_MASTERCARD_ARAMEX",
                "Desc" => "YOUR_SUYOOL_MASTERCARD_WILL_BE_FREE_OF_CHARGE_ARAMEX"
            ],
            "FIVE" => [
                "Title" => "WHERE_CAN_I_USE_MY_SUYOOL_PLATINUM_MASTERCARD_ARAMEX",
                "Desc" => "YOU_CAN_USE_YOUR_SUYOOL_MASTERCARD_AT_ANY_POS_ARAMEX"
            ],
            "SIX" => [
                "Title" => "WHERE_CAN_I_WITHDRAW_MY_SALARY_IN_CASH_ARAMEX",
                "Desc" => "USERS_CAN_ACCESS_THEIR_MONEY_FROM_MORE_THAN_700_ARAMEX"
            ],
        ];

        $parameters['title'] = "Aramex | Suyool";
        $parameters['desc'] = "Facing today’s financial challenges, we moved our payroll to Suyool. You will get your own digital dual-currency account,a Platinum Debit Card & a payment tool with the best rates available.";

        $parameters['infoSection'] = $this->infoSection;

        return $this->render('merchants/aramex.html.twig', $parameters);
    }

    /**
     * @Route("/emood", name="emood")
     */
    public function emood(Request $request, TranslatorInterface $translatorInterface): Response
    {
        $parameters = $this->trans->translation($request, $translatorInterface);
        $translatorInterface->setLocale("en");
        $parameters['lang'] = "en";
        $parameters['metaimage'] = "build/images/emood/emood-meta.jpg";
        $parameters['descmeta'] = "Your Payroll is now on Suyool";
        $parameters['faq'] = [
            "ONE" => [
                "Title" => "WHAT_IS_SUYOOL_LLDJ",
                "Desc" => "SUYOOL_IS_A_CASHLESS_ECOSYSTEM_THAT_INCORPORATES_LLDJ"
            ],
            "TWO" => [
                "Title" => "CAN_ANYONE_OPEN_A_SUYOOL_ACCOUNT_LLDJ",
                "Desc" => "ANY_LEBANESE_CITIZEN_CAN_OPEN_A_SUYOOL_ACCOUNT_LLDJ"
            ],
            "THREE" => [
                "Title" => "WHAT_ARE_THE_BENEFITS_FOR_USJ_EMPLOYEES_EMOOD",
                "Desc" => "YOU_WILL_BENEFIT_FROM_A_FREE_PLATINUM_MASTERCARD_EMOOD"
            ],
            "FOUR" => [
                "Title" => "IS_THERE_ANY_FEE_TO_GET_MY_SUYOOL_PLATINUM_MASTERCARD_LLDJ",
                "Desc" => "YOUR_SUYOOL_MASTERCARD_WILL_BE_FREE_OF_CHARGE_EMOOD"
            ],
            "FIVE" => [
                "Title" => "WHERE_CAN_I_USE_MY_SUYOOL_PLATINUM_MASTERCARD_LLDJ",
                "Desc" => "YOU_CAN_USE_YOUR_SUYOOL_MASTERCARD_AT_ANY_POS_LLDJ"
            ],
            "SIX" => [
                "Title" => "WHERE_CAN_I_WITHDRAW_MY_SALARY_IN_CASH_LLDJ",
                "Desc" => "USERS_CAN_ACCESS_THEIR_MONEY_FROM_MORE_THAN_700_LLDJ"
            ],
        ];

        $parameters['title'] = "E-mood Employee | Suyool";
        $parameters['desc'] = "Facing today’s financial challenges, we moved our payroll to Suyool. You will get your own digital dual-currency account,a Platinum Debit Card & a payment tool with the best rates available.";

        $parameters['infoSection'] = $this->infoSection;

        return $this->render('merchants/emood.html.twig', $parameters);
    }

    /**
     * @Route("/web-addicts", name="web_addicts")
     */
    public function webAddicts(Request $request, TranslatorInterface $translatorInterface): Response
    {
        $parameters = $this->trans->translation($request, $translatorInterface);
        $translatorInterface->setLocale("en");
        $parameters['lang'] = "en";
        $parameters['metaimage'] = "build/images/web-addicts/web-meta.jpg";
        $parameters['descmeta'] = "Your Payroll is now on Suyool";
        $parameters['faq'] = [
            "ONE" => [
                "Title" => "WHAT_IS_SUYOOL_LLDJ",
                "Desc" => "SUYOOL_IS_A_CASHLESS_ECOSYSTEM_THAT_INCORPORATES_LLDJ"
            ],
            "TWO" => [
                "Title" => "CAN_ANYONE_OPEN_A_SUYOOL_ACCOUNT_LLDJ",
                "Desc" => "ANY_LEBANESE_CITIZEN_CAN_OPEN_A_SUYOOL_ACCOUNT_LLDJ"
            ],
            "THREE" => [
                "Title" => "WHAT_ARE_THE_BENEFITS_FOR_USJ_EMPLOYEES_ADDICTS",
                "Desc" => "YOU_WILL_BENEFIT_FROM_A_FREE_PLATINUM_MASTERCARD_ADDICTS"
            ],
            "FOUR" => [
                "Title" => "IS_THERE_ANY_FEE_TO_GET_MY_SUYOOL_PLATINUM_MASTERCARD_LLDJ",
                "Desc" => "YOUR_SUYOOL_MASTERCARD_WILL_BE_FREE_OF_CHARGE_ADDICTS"
            ],
            "FIVE" => [
                "Title" => "WHERE_CAN_I_USE_MY_SUYOOL_PLATINUM_MASTERCARD_LLDJ",
                "Desc" => "YOU_CAN_USE_YOUR_SUYOOL_MASTERCARD_AT_ANY_POS_LLDJ"
            ],
            "SIX" => [
                "Title" => "WHERE_CAN_I_WITHDRAW_MY_SALARY_IN_CASH_LLDJ",
                "Desc" => "USERS_CAN_ACCESS_THEIR_MONEY_FROM_MORE_THAN_700_LLDJ"
            ],
        ];

        $parameters['title'] = "The Web Addicts | Suyool";
        $parameters['desc'] = "Facing today’s financial challenges, we moved our payroll to Suyool. You will get your own digital dual-currency account,a Platinum Debit Card & a payment tool with the best rates available.";

        $parameters['infoSection'] = $this->infoSection;

        return $this->render('merchants/web-addicts.html.twig', $parameters);
    }

    /**
     * @Route("/Phenicia", name="droguerie-Phenicia")
     */
    public function phenicia(Request $request, TranslatorInterface $translatorInterface): Response
    {
        $parameters = $this->trans->translation($request, $translatorInterface);
        $translatorInterface->setLocale("en");
        $parameters['lang'] = "en";
        $parameters['metaimage'] = "build/images/droguerie/metaPhar.png";
        $parameters['descmeta'] = "Your Payroll is now on Suyool";
        $parameters['faq'] = [
            "ONE" => [
                "Title" => "WHAT_IS_SUYOOL_DrogueriePhenicia",
                "Desc" => "SUYOOL_IS_A_CASHLESS_ECOSYSTEM_THAT_INCORPORATES_DrogueriePhenicia"
            ],
            "TWO" => [
                "Title" => "CAN_ANYONE_OPEN_A_SUYOOL_ACCOUNT_DrogueriePhenicia",
                "Desc" => "ANY_LEBANESE_CITIZEN_CAN_OPEN_A_SUYOOL_ACCOUNT_DrogueriePhenicia"
            ],
            "THREE" => [
                "Title" => "WHAT_ARE_THE_BENEFITS_FOR_USJ_EMPLOYEES_DrogueriePhenicia",
                "Desc" => "YOU_WILL_BENEFIT_FROM_A_FREE_PLATINUM_MASTERCARD_DrogueriePhenicia"
            ],
            "FOUR" => [
                "Title" => "IS_THERE_ANY_FEE_TO_GET_MY_SUYOOL_PLATINUM_MASTERCARD_DrogueriePhenicia",
                "Desc" => "YOUR_SUYOOL_MASTERCARD_WILL_BE_FREE_OF_CHARGE_ADDICTS_DrogueriePhenicia"
            ],
            "FIVE" => [
                "Title" => "WHERE_CAN_I_USE_MY_SUYOOL_PLATINUM_MASTERCARD_DrogueriePhenicia",
                "Desc" => "YOU_CAN_USE_YOUR_SUYOOL_MASTERCARD_AT_ANY_POS_DrogueriePhenicia"
            ],
            "SIX" => [
                "Title" => "WHERE_CAN_I_WITHDRAW_MY_SALARY_IN_CASH_DrogueriePhenicia",
                "Desc" => "USERS_CAN_ACCESS_THEIR_MONEY_FROM_MORE_THAN_700_DrogueriePhenicia"
            ],
        ];

        $parameters['title'] = "Droguerie Phenicia | Suyool";
        $parameters['desc'] = "Facing today’s financial challenges, we moved our payroll to Suyool. You will get your own digital dual-currency account,a Platinum Debit Card & a payment tool with the best rates available.";

        $parameters['infoSection'] = $this->infoSection;

        return $this->render('merchants/droguerie-Phenicia.html.twig', $parameters);
    }

    /**
     * @Route("/medco", name="medco")
     */
    public function medco(Request $request, TranslatorInterface $translatorInterface): Response
    {
        $parameters = $this->trans->translation($request, $translatorInterface);
        $translatorInterface->setLocale("en");
        $parameters['lang'] = "en";
        $parameters['metaimage'] = "build/images/medco/medcoMeta.png";
        $parameters['descmeta'] = "Your Payroll is now on Suyool";
        $parameters['faq'] = [
            "ONE" => [
                "Title" => "WHAT_IS_SUYOOL_DrogueriePhenicia",
                "Desc" => "SUYOOL_IS_A_CASHLESS_ECOSYSTEM_THAT_INCORPORATES_DrogueriePhenicia"
            ],
            "TWO" => [
                "Title" => "CAN_ANYONE_OPEN_A_SUYOOL_ACCOUNT_DrogueriePhenicia",
                "Desc" => "ANY_LEBANESE_CITIZEN_CAN_OPEN_A_SUYOOL_ACCOUNT_DrogueriePhenicia"
            ],
            "THREE" => [
                "Title" => "WHAT_ARE_THE_BENEFITS_FOR_USJ_EMPLOYEES_MEDCO",
                "Desc" => "YOU_WILL_BENEFIT_FROM_A_FREE_PLATINUM_MASTERCARD_MEDCO"
            ],
            "FOUR" => [
                "Title" => "IS_THERE_ANY_FEE_TO_GET_MY_SUYOOL_PLATINUM_MASTERCARD_DrogueriePhenicia",
                "Desc" => "YOUR_SUYOOL_MASTERCARD_WILL_BE_FREE_OF_CHARGE_ADDICTS_MEDCO"
            ],
            "FIVE" => [
                "Title" => "WHERE_CAN_I_USE_MY_SUYOOL_PLATINUM_MASTERCARD_DrogueriePhenicia",
                "Desc" => "YOU_CAN_USE_YOUR_SUYOOL_MASTERCARD_AT_ANY_POS_DrogueriePhenicia"
            ],
            "SIX" => [
                "Title" => "WHERE_CAN_I_WITHDRAW_MY_SALARY_IN_CASH_DrogueriePhenicia_MEDCO",
                "Desc" => "USERS_CAN_ACCESS_THEIR_MONEY_FROM_MORE_THAN_700_DrogueriePhenicia_MEDCO"
            ],
        ];

        $parameters['title'] = "Medco | Suyool";
        $parameters['desc'] = "Facing today’s financial challenges, we moved our payroll to Suyool. You will get your own digital dual-currency account,a Platinum Debit Card & a payment tool with the best rates available.";
        $infoSection = [
            'title' => '6 Ways To Use Your Money',
            'items' => [
                [
                    'image' => 'build/images/alfa_employee/card3.svg',
                    'description' => 'Visa Platinum Card',
                ],
                [
                    'image' => 'build/images/alfa_employee/allowance1.svg',
                    'description' => 'Free Allowance Withdraw',
                ],
                [
                    'image' => 'build/images/alfa_employee/arrow1.svg',
                    'description' => 'Send & Receive Money for Free',
                ],
                [
                    'image' => 'build/images/alfa_employee/request-money.svg',
                    'description' => 'Request Money',
                ],
                [
                    'image' => 'build/images/alfa_employee/payroll.svg',
                    'description' => 'Pay Bills',
                ],
                [
                    'image' => 'build/images/alfa_employee/payQr1.svg',
                    'description' => 'Pay with Suyool QR',
                ],
            ],
        ];
        $parameters['infoSection'] = $infoSection;

        return $this->render('merchants/medco.html.twig', $parameters);
    }

    // /**
    //  * @Route("/group-kallasi", name="kallasi")
    //  */
    public function kallasi(Request $request, TranslatorInterface $translatorInterface): Response
    {
        $parameters = $this->trans->translation($request, $translatorInterface);
        $translatorInterface->setLocale("en");
        $parameters['lang'] = "en";
        $parameters['metaimage'] = "build/images/kallasi/kallasiMeta.png";
        $parameters['descmeta'] = "Your Payroll is now on Suyool";
        $parameters['faq'] = [
            "ONE" => [
                "Title" => "WHAT_IS_SUYOOL_DrogueriePhenicia",
                "Desc" => "SUYOOL_IS_A_CASHLESS_ECOSYSTEM_THAT_INCORPORATES_DrogueriePhenicia"
            ],
            "TWO" => [
                "Title" => "CAN_ANYONE_OPEN_A_SUYOOL_ACCOUNT_DrogueriePhenicia",
                "Desc" => "ANY_LEBANESE_CITIZEN_CAN_OPEN_A_SUYOOL_ACCOUNT_DrogueriePhenicia"
            ],
            "THREE" => [
                "Title" => "WHAT_ARE_THE_BENEFITS_FOR_USJ_EMPLOYEES_KALLASI",
                "Desc" => "YOU_WILL_BENEFIT_FROM_A_FREE_PLATINUM_MASTERCARD_KALLASI"
            ],
            "FOUR" => [
                "Title" => "IS_THERE_ANY_FEE_TO_GET_MY_SUYOOL_PLATINUM_MASTERCARD_DrogueriePhenicia",
                "Desc" => "YOUR_SUYOOL_MASTERCARD_WILL_BE_FREE_OF_CHARGE_ADDICTS_KALLASI"
            ],
            "FIVE" => [
                "Title" => "WHERE_CAN_I_USE_MY_SUYOOL_PLATINUM_MASTERCARD_DrogueriePhenicia",
                "Desc" => "YOU_CAN_USE_YOUR_SUYOOL_MASTERCARD_AT_ANY_POS_DrogueriePhenicia"
            ],
            "SIX" => [
                "Title" => "WHERE_CAN_I_WITHDRAW_MY_SALARY_IN_CASH_DrogueriePhenicia",
                "Desc" => "USERS_CAN_ACCESS_THEIR_MONEY_FROM_MORE_THAN_700_DrogueriePhenicia"
            ],
        ];

        $parameters['title'] = "Group ka | Suyool";
        $parameters['desc'] = "Facing today’s financial challenges, we moved our payroll to Suyool. You will get your own digital dual-currency account,a Platinum Debit Card & a payment tool with the best rates available.";

        $parameters['infoSection'] = $this->infoSection;

        return $this->render('merchants/kallasi.html.twig', $parameters);
    }

    /**
     * @Route("/laser-vision", name="laservision")
     */
    public function laser(Request $request, TranslatorInterface $translatorInterface): Response
    {
        $parameters = $this->trans->translation($request, $translatorInterface);
        $translatorInterface->setLocale("en");
        $parameters['lang'] = "en";
        $parameters['metaimage'] = "build/images/laser/laserMeta.png";
        $parameters['descmeta'] = "Your Payroll is now on Suyool";
        $parameters['faq'] = [
            "ONE" => [
                "Title" => "WHAT_IS_SUYOOL_DrogueriePhenicia",
                "Desc" => "SUYOOL_IS_A_CASHLESS_ECOSYSTEM_THAT_INCORPORATES_DrogueriePhenicia"
            ],
            "TWO" => [
                "Title" => "CAN_ANYONE_OPEN_A_SUYOOL_ACCOUNT_DrogueriePhenicia",
                "Desc" => "ANY_LEBANESE_CITIZEN_CAN_OPEN_A_SUYOOL_ACCOUNT_DrogueriePhenicia"
            ],
            "THREE" => [
                "Title" => "WHAT_ARE_THE_BENEFITS_FOR_USJ_EMPLOYEES_LASER",
                "Desc" => "YOU_WILL_BENEFIT_FROM_A_FREE_PLATINUM_MASTERCARD_LASER"
            ],
            "FOUR" => [
                "Title" => "IS_THERE_ANY_FEE_TO_GET_MY_SUYOOL_PLATINUM_MASTERCARD_DrogueriePhenicia",
                "Desc" => "YOUR_SUYOOL_MASTERCARD_WILL_BE_FREE_OF_CHARGE_ADDICTS_LASER"
            ],
            "FIVE" => [
                "Title" => "WHERE_CAN_I_USE_MY_SUYOOL_PLATINUM_MASTERCARD_DrogueriePhenicia",
                "Desc" => "YOU_CAN_USE_YOUR_SUYOOL_MASTERCARD_AT_ANY_POS_DrogueriePhenicia"
            ],
            "SIX" => [
                "Title" => "WHERE_CAN_I_WITHDRAW_MY_SALARY_IN_CASH_DrogueriePhenicia",
                "Desc" => "USERS_CAN_ACCESS_THEIR_MONEY_FROM_MORE_THAN_700_DrogueriePhenicia"
            ],
        ];

        $parameters['title'] = "Laser vision | Suyool";
        $parameters['desc'] = "Facing today’s financial challenges, we moved our payroll to Suyool. You will get your own digital dual-currency account,a Platinum Debit Card & a payment tool with the best rates available.";

        $parameters['infoSection'] = $this->infoSection;

        return $this->render('merchants/laser.html.twig', $parameters);
    }

    /**
     * @Route("/editec", name="editec")
     */
    public function editec(Request $request, TranslatorInterface $translatorInterface): Response
    {
        $parameters = $this->trans->translation($request, $translatorInterface);
        $translatorInterface->setLocale("en");
        $parameters['lang'] = "en";
        $parameters['metaimage'] = "build/images/editec/editecMeta.png";
        $parameters['descmeta'] = "Your Payroll is now on Suyool";
        $parameters['faq'] = [
            "ONE" => [
                "Title" => "WHAT_IS_SUYOOL_DrogueriePhenicia",
                "Desc" => "SUYOOL_IS_A_CASHLESS_ECOSYSTEM_THAT_INCORPORATES_DrogueriePhenicia"
            ],
            "TWO" => [
                "Title" => "CAN_ANYONE_OPEN_A_SUYOOL_ACCOUNT_DrogueriePhenicia",
                "Desc" => "ANY_LEBANESE_CITIZEN_CAN_OPEN_A_SUYOOL_ACCOUNT_DrogueriePhenicia"
            ],
            "THREE" => [
                "Title" => "WHAT_ARE_THE_BENEFITS_FOR_USJ_EMPLOYEES_EDITEC",
                "Desc" => "YOU_WILL_BENEFIT_FROM_A_FREE_PLATINUM_MASTERCARD_EDITEC"
            ],
            "FOUR" => [
                "Title" => "IS_THERE_ANY_FEE_TO_GET_MY_SUYOOL_PLATINUM_MASTERCARD_DrogueriePhenicia",
                "Desc" => "YOUR_SUYOOL_MASTERCARD_WILL_BE_FREE_OF_CHARGE_ADDICTS_EDITEC"
            ],
            "FIVE" => [
                "Title" => "WHERE_CAN_I_USE_MY_SUYOOL_PLATINUM_MASTERCARD_DrogueriePhenicia",
                "Desc" => "YOU_CAN_USE_YOUR_SUYOOL_MASTERCARD_AT_ANY_POS_DrogueriePhenicia"
            ],
            "SIX" => [
                "Title" => "WHERE_CAN_I_WITHDRAW_MY_SALARY_IN_CASH_DrogueriePhenicia",
                "Desc" => "USERS_CAN_ACCESS_THEIR_MONEY_FROM_MORE_THAN_700_DrogueriePhenicia"
            ],
        ];

        $parameters['title'] = "Editec | Suyool";
        $parameters['desc'] = "Facing today’s financial challenges, we moved our payroll to Suyool. You will get your own digital dual-currency account,a Platinum Debit Card & a payment tool with the best rates available.";

        $parameters['infoSection'] = $this->infoSection;

        return $this->render('merchants/editec.html.twig', $parameters);
    }

    /**
     * @Route("/fahed", name="fahed")
     */
    public function fahed(Request $request, TranslatorInterface $translatorInterface): Response
    {
        $parameters = $this->trans->translation($request, $translatorInterface);
        $translatorInterface->setLocale("en");
        $parameters['lang'] = "en";
        $parameters['metaimage'] = "build/images/fahed/fahedMeta.png";
        $parameters['descmeta'] = "Your Payroll is now on Suyool";
        $parameters['faq'] = [
            "ONE" => [
                "Title" => "WHAT_IS_SUYOOL_DrogueriePhenicia",
                "Desc" => "SUYOOL_IS_A_CASHLESS_ECOSYSTEM_THAT_INCORPORATES_DrogueriePhenicia"
            ],
            "TWO" => [
                "Title" => "CAN_ANYONE_OPEN_A_SUYOOL_ACCOUNT_DrogueriePhenicia",
                "Desc" => "ANY_LEBANESE_CITIZEN_CAN_OPEN_A_SUYOOL_ACCOUNT_DrogueriePhenicia"
            ],
            "THREE" => [
                "Title" => "WHAT_ARE_THE_BENEFITS_FOR_USJ_EMPLOYEES_FAHED",
                "Desc" => "YOU_WILL_BENEFIT_FROM_A_FREE_PLATINUM_MASTERCARD_FAHED"
            ],
            "FOUR" => [
                "Title" => "IS_THERE_ANY_FEE_TO_GET_MY_SUYOOL_PLATINUM_MASTERCARD_DrogueriePhenicia",
                "Desc" => "YOUR_SUYOOL_MASTERCARD_WILL_BE_FREE_OF_CHARGE_ADDICTS_FAHED"
            ],
            "FIVE" => [
                "Title" => "WHERE_CAN_I_USE_MY_SUYOOL_PLATINUM_MASTERCARD_DrogueriePhenicia",
                "Desc" => "YOU_CAN_USE_YOUR_SUYOOL_MASTERCARD_AT_ANY_POS_DrogueriePhenicia"
            ],
            "SIX" => [
                "Title" => "WHERE_CAN_I_WITHDRAW_MY_SALARY_IN_CASH_DrogueriePhenicia",
                "Desc" => "USERS_CAN_ACCESS_THEIR_MONEY_FROM_MORE_THAN_700_DrogueriePhenicia"
            ],
        ];

        $parameters['title'] = "Fahed | Suyool";
        $parameters['desc'] = "Facing today’s financial challenges, we moved our payroll to Suyool. You will get your own digital dual-currency account,a Platinum Debit Card & a payment tool with the best rates available.";

        $parameters['infoSection'] = $this->infoSection;

        return $this->render('merchants/fahed.html.twig', $parameters);
    }

    /**
     * @Route("/medco-payroll", name="medco_payroll")
     */
    public function medcoPayroll(Request $request, TranslatorInterface $translatorInterface): Response
    {
        $parameters = $this->trans->translation($request, $translatorInterface);
        $translatorInterface->setLocale("en");
        $parameters['lang'] = "en";
        $parameters['metaimage'] = "build/images/medco/medco-payroll-meta.png";
        $parameters['descmeta'] = "Why is Suyool the best option for your payroll?";
        $parameters['faq'] = [
            "ONE" => [
                "Title" => "WHAT_IS_SUYOOL_DrogueriePhenicia",
                "Desc" => "SUYOOL_IS_A_CASHLESS_ECOSYSTEM_THAT_INCORPORATES_DrogueriePhenicia"
            ],
            "TWO" => [
                "Title" => "CAN_ANYONE_OPEN_A_SUYOOL_ACCOUNT_DrogueriePhenicia",
                "Desc" => "ANY_LEBANESE_CITIZEN_CAN_OPEN_A_SUYOOL_ACCOUNT_DrogueriePhenicia"
            ],
            "THREE" => [
                "Title" => "WHAT_ARE_THE_BENEFITS_FOR_USJ_EMPLOYEES_MEDCO",
                "Desc" => "YOU_WILL_BENEFIT_FROM_A_FREE_PLATINUM_MASTERCARD_MEDCOPAYROLL"
            ],
            "FOUR" => [
                "Title" => "IS_THERE_ANY_FEE_TO_GET_MY_SUYOOL_PLATINUM_MASTERCARD_DrogueriePhenicia",
                "Desc" => "YOUR_SUYOOL_MASTERCARD_WILL_BE_FREE_OF_CHARGE_ADDICTS_MEDCO"
            ],
            "FIVE" => [
                "Title" => "WHERE_CAN_I_USE_MY_SUYOOL_PLATINUM_MASTERCARD_DrogueriePhenicia",
                "Desc" => "YOU_CAN_USE_YOUR_SUYOOL_MASTERCARD_AT_ANY_POS_DrogueriePhenicia"
            ],
            "SIX" => [
                "Title" => "WHERE_CAN_I_WITHDRAW_MY_SALARY_IN_CASH_DrogueriePhenicia_MEDCOPAYROLL",
                "Desc" => "USERS_CAN_ACCESS_THEIR_MONEY_FROM_MORE_THAN_700_DrogueriePhenicia_MEDCOPAYROLL"
            ],
        ];

        $parameters['title'] = "Medco | Suyool";
        $parameters['desc'] = "Facing today's financial challenges, we moved our payroll to Suyool. You will get your own digital dual-currency account,a Platinum Debit Card & a payment tool with the best rates available.";

        $parameters['infoSection'] = $this->infoSection2;

        return $this->render('medco-payroll/index.html.twig', $parameters);
    }

    /**
     * @Route("/debbas-payroll", name="debbas-payroll")
     */
    public function debbaspayroll(Request $request, TranslatorInterface $translatorInterface): Response
    {
        $parameters = $this->trans->translation($request, $translatorInterface);
        $translatorInterface->setLocale("en");
        $parameters['lang'] = "en";
        $parameters['metaimage'] = "build/images/debbas-payroll/metaImage.png";
        $parameters['descmeta'] = "Why is Suyool the best option for your payroll?";
        $parameters['faq'] = [
            "ONE" => [
                "Title" => "WHAT_IS_SUYOOL_DrogueriePhenicia",
                "Desc" => "SUYOOL_IS_A_CASHLESS_ECOSYSTEM_THAT_INCORPORATES_DrogueriePhenicia"
            ],
            "TWO" => [
                "Title" => "CAN_ANYONE_OPEN_A_SUYOOL_ACCOUNT_DrogueriePhenicia",
                "Desc" => "ANY_LEBANESE_CITIZEN_CAN_OPEN_A_SUYOOL_ACCOUNT_DrogueriePhenicia"
            ],
            "THREE" => [
                "Title" => "WHAT_ARE_THE_BENEFITS_FOR_USJ_EMPLOYEES_DEBBAS",
                "Desc" => "YOU_WILL_BENEFIT_FROM_A_FREE_PLATINUM_MASTERCARD_DEBBAS"
            ],
            "FOUR" => [
                "Title" => "IS_THERE_ANY_FEE_TO_GET_MY_SUYOOL_PLATINUM_MASTERCARD_DrogueriePhenicia",
                "Desc" => "YOUR_SUYOOL_MASTERCARD_WILL_BE_FREE_OF_CHARGE_ADDICTS_DEBBAS"
            ],
            "FIVE" => [
                "Title" => "WHERE_CAN_I_USE_MY_SUYOOL_PLATINUM_MASTERCARD_DrogueriePhenicia",
                "Desc" => "YOU_CAN_USE_YOUR_SUYOOL_MASTERCARD_AT_ANY_POS_DrogueriePhenicia"
            ],
            "SIX" => [
                "Title" => "WHERE_CAN_I_WITHDRAW_MY_SALARY_IN_CASH_DrogueriePhenicia_MEDCOPAYROLL",
                "Desc" => "USERS_CAN_ACCESS_THEIR_MONEY_FROM_MORE_THAN_700_DrogueriePhenicia_MEDCOPAYROLL"
            ],
        ];

        $parameters['title'] = "Debbas | Suyool";
        $parameters['desc'] = "Facing today's financial challenges, we moved our payroll to Suyool. You will get your own digital dual-currency account,a Platinum Debit Card & a payment tool with the best rates available.";

        $parameters['infoSection'] = $this->infoSection2;

        return $this->render('debbas-payroll/index.html.twig', $parameters);
    }

    /**
     * @Route("/debbane", name="debbane-payroll")
     */
    public function debbane(Request $request, TranslatorInterface $translatorInterface): Response
    {
        $parameters = $this->trans->translation($request, $translatorInterface);
        $translatorInterface->setLocale("en");
        $parameters['lang'] = "en";
        $parameters['metaimage'] = "build/images/payroll/debbaneMeta.png";
        $parameters['descmeta'] = "Why is Suyool the best option for your payroll?";
        $parameters['faq'] = [
            "ONE" => [
                "Title" => "WHAT_IS_SUYOOL_DrogueriePhenicia",
                "Desc" => "SUYOOL_IS_A_CASHLESS_ECOSYSTEM_THAT_INCORPORATES_DrogueriePhenicia"
            ],
            "TWO" => [
                "Title" => "CAN_ANYONE_OPEN_A_SUYOOL_ACCOUNT_DrogueriePhenicia",
                "Desc" => "ANY_LEBANESE_CITIZEN_CAN_OPEN_A_SUYOOL_ACCOUNT_DrogueriePhenicia"
            ],
            "THREE" => [
                "Title" => "WHAT_ARE_THE_BENEFITS_FOR_USJ_EMPLOYEES_DEBBANE",
                "Desc" => "YOU_WILL_BENEFIT_FROM_A_FREE_PLATINUM_MASTERCARD_DEBBANE"
            ],
            "FOUR" => [
                "Title" => "IS_THERE_ANY_FEE_TO_GET_MY_SUYOOL_PLATINUM_MASTERCARD_DrogueriePhenicia",
                "Desc" => "YOUR_SUYOOL_MASTERCARD_WILL_BE_FREE_OF_CHARGE_ADDICTS_DEBBANE"
            ],
            "FIVE" => [
                "Title" => "WHERE_CAN_I_USE_MY_SUYOOL_PLATINUM_MASTERCARD_DrogueriePhenicia",
                "Desc" => "YOU_CAN_USE_YOUR_SUYOOL_MASTERCARD_AT_ANY_POS_DrogueriePhenicia"
            ],
            "SIX" => [
                "Title" => "WHERE_CAN_I_WITHDRAW_MY_SALARY_IN_CASH_DrogueriePhenicia_MEDCOPAYROLL",
                "Desc" => "USERS_CAN_ACCESS_THEIR_MONEY_FROM_MORE_THAN_700_DrogueriePhenicia_MEDCOPAYROLL"
            ],
        ];

        $parameters['title'] = "Debbane Saikali Group | Suyool";
        $parameters['desc'] = "Facing today's financial challenges, we moved our payroll to Suyool. You will get your own digital dual-currency account,a Platinum Debit Card & a payment tool with the best rates available.";

        $parameters['infoSection'] = $this->infoSection2;

        return $this->render('debbane/index.html.twig', $parameters);
    }

    /**
     * @Route("/mikesport", name="mikesport-payroll")
     */
    public function mikesport(Request $request, TranslatorInterface $translatorInterface): Response
    {
        $parameters = $this->trans->translation($request, $translatorInterface);
        $translatorInterface->setLocale("en");
        $parameters['lang'] = "en";
        $parameters['metaimage'] = "build/images/payroll/mikeMeta.png";
        $parameters['descmeta'] = "Why is Suyool the best option for your payroll?";
        $parameters['faq'] = [
            "ONE" => [
                "Title" => "WHAT_IS_SUYOOL_DrogueriePhenicia",
                "Desc" => "SUYOOL_IS_A_CASHLESS_ECOSYSTEM_THAT_INCORPORATES_DrogueriePhenicia"
            ],
            "TWO" => [
                "Title" => "CAN_ANYONE_OPEN_A_SUYOOL_ACCOUNT_DrogueriePhenicia",
                "Desc" => "ANY_LEBANESE_CITIZEN_CAN_OPEN_A_SUYOOL_ACCOUNT_DrogueriePhenicia"
            ],
            "THREE" => [
                "Title" => "WHAT_ARE_THE_BENEFITS_FOR_USJ_EMPLOYEES_MIKESPORT",
                "Desc" => "YOU_WILL_BENEFIT_FROM_A_FREE_PLATINUM_MASTERCARD_MIKESPORT"
            ],
            "FOUR" => [
                "Title" => "IS_THERE_ANY_FEE_TO_GET_MY_SUYOOL_PLATINUM_MASTERCARD_DrogueriePhenicia",
                "Desc" => "YOUR_SUYOOL_MASTERCARD_WILL_BE_FREE_OF_CHARGE_ADDICTS_MIKESPORT"
            ],
            "FIVE" => [
                "Title" => "WHERE_CAN_I_USE_MY_SUYOOL_PLATINUM_MASTERCARD_DrogueriePhenicia",
                "Desc" => "YOU_CAN_USE_YOUR_SUYOOL_MASTERCARD_AT_ANY_POS_DrogueriePhenicia"
            ],
            "SIX" => [
                "Title" => "WHERE_CAN_I_WITHDRAW_MY_SALARY_IN_CASH_DrogueriePhenicia_MEDCOPAYROLL",
                "Desc" => "USERS_CAN_ACCESS_THEIR_MONEY_FROM_MORE_THAN_700_DrogueriePhenicia_MEDCOPAYROLL"
            ],
        ];

        $parameters['title'] = "Mike Sport | Suyool";
        $parameters['desc'] = "Facing today's financial challenges, we moved our payroll to Suyool. You will get your own digital dual-currency account,a Platinum Debit Card & a payment tool with the best rates available.";

        $parameters['infoSection'] = $this->infoSection2;

        return $this->render('mikesport/index.html.twig', $parameters);
    }

    /**
     * @Route("/climate-technology", name="climate-technology-payroll")
     */
    public function climateTechnology(Request $request, TranslatorInterface $translatorInterface): Response
    {
        $parameters = $this->trans->translation($request, $translatorInterface);
        $translatorInterface->setLocale("en");
        $parameters['lang'] = "en";
        $parameters['metaimage'] = "build/images/payroll/climateMeta.png";
        $parameters['descmeta'] = "Why is Suyool the best option for your payroll?";
        $parameters['faq'] = [
            "ONE" => [
                "Title" => "WHAT_IS_SUYOOL_DrogueriePhenicia",
                "Desc" => "SUYOOL_IS_A_CASHLESS_ECOSYSTEM_THAT_INCORPORATES_DrogueriePhenicia"
            ],
            "TWO" => [
                "Title" => "CAN_ANYONE_OPEN_A_SUYOOL_ACCOUNT_DrogueriePhenicia",
                "Desc" => "ANY_LEBANESE_CITIZEN_CAN_OPEN_A_SUYOOL_ACCOUNT_DrogueriePhenicia"
            ],
            "THREE" => [
                "Title" => "WHAT_ARE_THE_BENEFITS_FOR_USJ_EMPLOYEES_CLIMATE",
                "Desc" => "YOU_WILL_BENEFIT_FROM_A_FREE_PLATINUM_MASTERCARD_CLIMATE"
            ],
            "FOUR" => [
                "Title" => "IS_THERE_ANY_FEE_TO_GET_MY_SUYOOL_PLATINUM_MASTERCARD_DrogueriePhenicia",
                "Desc" => "YOUR_SUYOOL_MASTERCARD_WILL_BE_FREE_OF_CHARGE_ADDICTS_CLIMATE"
            ],
            "FIVE" => [
                "Title" => "WHERE_CAN_I_USE_MY_SUYOOL_PLATINUM_MASTERCARD_DrogueriePhenicia",
                "Desc" => "YOU_CAN_USE_YOUR_SUYOOL_MASTERCARD_AT_ANY_POS_DrogueriePhenicia"
            ],
            "SIX" => [
                "Title" => "WHERE_CAN_I_WITHDRAW_MY_SALARY_IN_CASH_DrogueriePhenicia_MEDCOPAYROLL",
                "Desc" => "USERS_CAN_ACCESS_THEIR_MONEY_FROM_MORE_THAN_700_DrogueriePhenicia_MEDCOPAYROLL"
            ],
        ];

        $parameters['title'] = "Climate Technology | Suyool";
        $parameters['desc'] = "Facing today's financial challenges, we moved our payroll to Suyool. You will get your own digital dual-currency account,a Platinum Debit Card & a payment tool with the best rates available.";

        $parameters['infoSection'] = $this->infoSection2;

        return $this->render('merchants/climate.html.twig', $parameters);
    }

    /**
     * @Route("/nds", name="nds-payroll")
     */
    public function nds(Request $request, TranslatorInterface $translatorInterface): Response
    {
        $parameters = $this->trans->translation($request, $translatorInterface);
        $translatorInterface->setLocale("en");
        $parameters['lang'] = "en";
        $parameters['metaimage'] = "build/images/payroll/ndsMeta2.png";
        $parameters['descmeta'] = "Why is Suyool the best option for your payroll?";
        $parameters['faq'] = [
            "ONE" => [
                "Title" => "WHAT_IS_SUYOOL_DrogueriePhenicia",
                "Desc" => "SUYOOL_IS_A_CASHLESS_ECOSYSTEM_THAT_INCORPORATES_DrogueriePhenicia"
            ],
            "TWO" => [
                "Title" => "CAN_ANYONE_OPEN_A_SUYOOL_ACCOUNT_DrogueriePhenicia",
                "Desc" => "ANY_LEBANESE_CITIZEN_CAN_OPEN_A_SUYOOL_ACCOUNT_DrogueriePhenicia"
            ],
            "THREE" => [
                "Title" => "WHAT_ARE_THE_BENEFITS_FOR_USJ_EMPLOYEES_NDS",
                "Desc" => "YOU_WILL_BENEFIT_FROM_A_FREE_PLATINUM_MASTERCARD_NDS"
            ],
            "FOUR" => [
                "Title" => "IS_THERE_ANY_FEE_TO_GET_MY_SUYOOL_PLATINUM_MASTERCARD_DrogueriePhenicia",
                "Desc" => "YOUR_SUYOOL_MASTERCARD_WILL_BE_FREE_OF_CHARGE_ADDICTS_NDS"
            ],
            "FIVE" => [
                "Title" => "WHERE_CAN_I_USE_MY_SUYOOL_PLATINUM_MASTERCARD_DrogueriePhenicia",
                "Desc" => "YOU_CAN_USE_YOUR_SUYOOL_MASTERCARD_AT_ANY_POS_DrogueriePhenicia"
            ],
            "SIX" => [
                "Title" => "WHERE_CAN_I_WITHDRAW_MY_SALARY_IN_CASH_DrogueriePhenicia_MEDCOPAYROLL",
                "Desc" => "USERS_CAN_ACCESS_THEIR_MONEY_FROM_MORE_THAN_700_DrogueriePhenicia_MEDCOPAYROLL"
            ],
        ];

        $parameters['title'] = "Notre Dame des Secours | Suyool";
        $parameters['desc'] = "Facing today's financial challenges, we moved our payroll to Suyool. You will get your own digital dual-currency account,a Platinum Debit Card & a payment tool with the best rates available.";

        $parameters['infoSection'] = $this->infoSection2;

        return $this->render('nds/index.html.twig', $parameters);
    }

    /**
     * @Route("/ecm", name="mikesport-ecm")
     */
    public function ecm(Request $request, TranslatorInterface $translatorInterface): Response
    {
        $parameters = $this->trans->translation($request, $translatorInterface);
        $translatorInterface->setLocale("en");
        $parameters['lang'] = "en";
        $parameters['metaimage'] = "build/images/payroll/ecmMeta.png";
        $parameters['descmeta'] = "Why is Suyool the best option for your payroll?";
        $parameters['faq'] = [
            "ONE" => [
                "Title" => "WHAT_IS_SUYOOL_DrogueriePhenicia",
                "Desc" => "SUYOOL_IS_A_CASHLESS_ECOSYSTEM_THAT_INCORPORATES_DrogueriePhenicia"
            ],
            "TWO" => [
                "Title" => "CAN_ANYONE_OPEN_A_SUYOOL_ACCOUNT_DrogueriePhenicia",
                "Desc" => "ANY_LEBANESE_CITIZEN_CAN_OPEN_A_SUYOOL_ACCOUNT_DrogueriePhenicia"
            ],
            "THREE" => [
                "Title" => "WHAT_ARE_THE_BENEFITS_FOR_USJ_EMPLOYEES_ECM",
                "Desc" => "YOU_WILL_BENEFIT_FROM_A_FREE_PLATINUM_MASTERCARD_ECM"
            ],
            "FOUR" => [
                "Title" => "IS_THERE_ANY_FEE_TO_GET_MY_SUYOOL_PLATINUM_MASTERCARD_DrogueriePhenicia",
                "Desc" => "YOUR_SUYOOL_MASTERCARD_WILL_BE_FREE_OF_CHARGE_ADDICTS_ECM"
            ],
            "FIVE" => [
                "Title" => "WHERE_CAN_I_USE_MY_SUYOOL_PLATINUM_MASTERCARD_DrogueriePhenicia",
                "Desc" => "YOU_CAN_USE_YOUR_SUYOOL_MASTERCARD_AT_ANY_POS_DrogueriePhenicia"
            ],
            "SIX" => [
                "Title" => "WHERE_CAN_I_WITHDRAW_MY_SALARY_IN_CASH_DrogueriePhenicia_MEDCOPAYROLL",
                "Desc" => "USERS_CAN_ACCESS_THEIR_MONEY_FROM_MORE_THAN_700_DrogueriePhenicia_MEDCOPAYROLL"
            ],
        ];

        $parameters['title'] = "ECM | Suyool";
        $parameters['desc'] = "Facing today's financial challenges, we moved our payroll to Suyool. You will get your own digital dual-currency account,a Platinum Debit Card & a payment tool with the best rates available.";

        $parameters['infoSection'] = $this->infoSection2;

        return $this->render('ecm/index.html.twig', $parameters);
    }


    /**
     * @Route("/payroll", name="payroll")
     */
    public function payroll(Request $request, TranslatorInterface $translatorInterface): Response
    {
        $parameters = $this->trans->translation($request, $translatorInterface);
        $translatorInterface->setLocale("en");
        $parameters['lang'] = "en";
        $parameters['metaimage'] = "build/images/payroll/payroll-metaImg.png";
        $parameters['descmeta'] = "Your Payroll is now on Suyool";
        $parameters['faq'] = [
            "ONE" => [
                "Title" => "WHAT_IS_SUYOOL_DrogueriePhenicia",
                "Desc" => "SUYOOL_IS_A_CASHLESS_ECOSYSTEM_THAT_INCORPORATES_DrogueriePhenicia"
            ],
            "TWO" => [
                "Title" => "CAN_ANYONE_OPEN_A_SUYOOL_ACCOUNT_DrogueriePhenicia",
                "Desc" => "ANY_LEBANESE_CITIZEN_CAN_OPEN_A_SUYOOL_ACCOUNT_DrogueriePhenicia"
            ],
            "THREE" => [
                "Title" => "WHAT_ARE_THE_BENEFITS_FOR_USJ_EMPLOYEES_PAYROLL",
                "Desc" => "YOU_WILL_BENEFIT_FROM_A_FREE_PLATINUM_MASTERCARD_PAYROLL"
            ],
            "FOUR" => [
                "Title" => "IS_THERE_ANY_FEE_TO_GET_MY_SUYOOL_PLATINUM_MASTERCARD_PAYROLL",
                "Desc" => "YOUR_SUYOOL_MASTERCARD_WILL_BE_FREE_OF_CHARGE_ADDICTS_PAYROLL"
            ],
            "FIVE" => [
                "Title" => "WHERE_CAN_I_USE_MY_SUYOOL_PLATINUM_MASTERCARD_DrogueriePhenicia",
                "Desc" => "YOU_CAN_USE_YOUR_SUYOOL_MASTERCARD_AT_ANY_POS_DrogueriePhenicia"
            ],
            "SIX" => [
                "Title" => "WHERE_CAN_I_WITHDRAW_MY_SALARY_IN_CASH_DrogueriePhenicia_MEDCOPAYROLL",
                "Desc" => "USERS_CAN_ACCESS_THEIR_MONEY_FROM_MORE_THAN_700_DrogueriePhenicia_MEDCOPAYROLL"
            ],
        ];

        $parameters['title'] = "Payroll | Suyool";
        $parameters['desc'] = "Facing today’s financial challenges, we moved our payroll to Suyool. You will get your own digital dual-currency account,a Platinum Debit Card & a payment tool with the best rates available.";

        $parameters['infoSection'] = $this->infoSection2;

        return $this->render('payroll/index.html.twig', $parameters);
    }

    /**
     * @Route("/indevco", name="indevco")
     */
    public function indevco(Request $request, TranslatorInterface $translatorInterface): Response
    {
        $parameters = $this->trans->translation($request, $translatorInterface);
        $translatorInterface->setLocale("en");
        $parameters['lang'] = "en";
        $parameters['metaimage'] = "build/images/payroll/indevcoMeta.png";
        $parameters['descmeta'] = "Why is Suyool the best option for your payroll?";
        $parameters['faq'] = [
            "ONE" => [
                "Title" => "WHAT_IS_SUYOOL_DrogueriePhenicia",
                "Desc" => "SUYOOL_IS_A_CASHLESS_ECOSYSTEM_THAT_INCORPORATES_DrogueriePhenicia"
            ],
            "TWO" => [
                "Title" => "CAN_ANYONE_OPEN_A_SUYOOL_ACCOUNT_DrogueriePhenicia",
                "Desc" => "ANY_LEBANESE_CITIZEN_CAN_OPEN_A_SUYOOL_ACCOUNT_DrogueriePhenicia"
            ],
            "THREE" => [
                "Title" => "WHAT_ARE_THE_BENEFITS_FOR_USJ_EMPLOYEES_INDEVCO",
                "Desc" => "YOU_WILL_BENEFIT_FROM_A_FREE_PLATINUM_MASTERCARD_INDEVCO"
            ],
            "FOUR" => [
                "Title" => "IS_THERE_ANY_FEE_TO_GET_MY_SUYOOL_PLATINUM_MASTERCARD_DrogueriePhenicia",
                "Desc" => "YOUR_SUYOOL_MASTERCARD_WILL_BE_FREE_OF_CHARGE_ADDICTS_INDEVCO"
            ],
            "FIVE" => [
                "Title" => "WHERE_CAN_I_USE_MY_SUYOOL_PLATINUM_MASTERCARD_DrogueriePhenicia",
                "Desc" => "YOU_CAN_USE_YOUR_SUYOOL_MASTERCARD_AT_ANY_POS_DrogueriePhenicia"
            ],
            "SIX" => [
                "Title" => "WHERE_CAN_I_WITHDRAW_MY_SALARY_IN_CASH_DrogueriePhenicia_MEDCOPAYROLL",
                "Desc" => "USERS_CAN_ACCESS_THEIR_MONEY_FROM_MORE_THAN_700_DrogueriePhenicia_MEDCOPAYROLL"
            ],
        ];

        $parameters['title'] = "Indevco | Suyool";
        $parameters['desc'] = "Facing today's financial challenges, we moved our payroll to Suyool. You will get your own digital dual-currency account,a Platinum Debit Card & a payment tool with the best rates available.";

        $parameters['infoSection'] = $this->infoSection2;

        return $this->render('indevco/index.html.twig', $parameters);
    }

    /**
     * @Route("/fig", name="fig")
     */
    public function fig(Request $request, TranslatorInterface $translatorInterface): Response
    {
        $parameters = $this->trans->translation($request, $translatorInterface);
        $translatorInterface->setLocale("en");
        $parameters['lang'] = "en";
        $parameters['metaimage'] = "build/images/payroll/figMeta.png";
        $parameters['descmeta'] = "Why is Suyool the best option for your payroll?";
        $parameters['faq'] = [
            "ONE" => [
                "Title" => "WHAT_IS_SUYOOL_DrogueriePhenicia",
                "Desc" => "SUYOOL_IS_A_CASHLESS_ECOSYSTEM_THAT_INCORPORATES_DrogueriePhenicia"
            ],
            "TWO" => [
                "Title" => "CAN_ANYONE_OPEN_A_SUYOOL_ACCOUNT_DrogueriePhenicia",
                "Desc" => "ANY_LEBANESE_CITIZEN_CAN_OPEN_A_SUYOOL_ACCOUNT_DrogueriePhenicia"
            ],
            "THREE" => [
                "Title" => "WHAT_ARE_THE_BENEFITS_FOR_USJ_EMPLOYEES_FIG",
                "Desc" => "YOU_WILL_BENEFIT_FROM_A_FREE_PLATINUM_MASTERCARD_FIG"
            ],
            "FOUR" => [
                "Title" => "IS_THERE_ANY_FEE_TO_GET_MY_SUYOOL_PLATINUM_MASTERCARD_DrogueriePhenicia",
                "Desc" => "YOUR_SUYOOL_MASTERCARD_WILL_BE_FREE_OF_CHARGE_ADDICTS_FIG"
            ],
            "FIVE" => [
                "Title" => "WHERE_CAN_I_USE_MY_SUYOOL_PLATINUM_MASTERCARD_DrogueriePhenicia",
                "Desc" => "YOU_CAN_USE_YOUR_SUYOOL_MASTERCARD_AT_ANY_POS_DrogueriePhenicia"
            ],
            "SIX" => [
                "Title" => "WHERE_CAN_I_WITHDRAW_MY_SALARY_IN_CASH_DrogueriePhenicia_MEDCOPAYROLL",
                "Desc" => "USERS_CAN_ACCESS_THEIR_MONEY_FROM_MORE_THAN_700_DrogueriePhenicia_MEDCOPAYROLL"
            ],
        ];

        $parameters['title'] = "Frem Industrial Group | Suyool";
        $parameters['desc'] = "Facing today's financial challenges, we moved our payroll to Suyool. You will get your own digital dual-currency account,a Platinum Debit Card & a payment tool with the best rates available.";

        $parameters['infoSection'] = $this->infoSection2;

        return $this->render('fig/index.html.twig', $parameters);
    }

    /**
     * @Route("/sad", name="sad-payroll")
     */
    public function sad(Request $request, TranslatorInterface $translatorInterface): Response
    {
        $parameters = $this->trans->translation($request, $translatorInterface);
        $translatorInterface->setLocale("en");
        $parameters['lang'] = "en";
        $parameters['metaimage'] = "build/images/payroll/sadMeta1.png";
        $parameters['descmeta'] = "Why is Suyool the best option for your payroll?";
        $parameters['faq'] = [
            "ONE" => [
                "Title" => "WHAT_IS_SUYOOL_DrogueriePhenicia",
                "Desc" => "SUYOOL_IS_A_CASHLESS_ECOSYSTEM_THAT_INCORPORATES_DrogueriePhenicia"
            ],
            "TWO" => [
                "Title" => "CAN_ANYONE_OPEN_A_SUYOOL_ACCOUNT_DrogueriePhenicia",
                "Desc" => "ANY_LEBANESE_CITIZEN_CAN_OPEN_A_SUYOOL_ACCOUNT_DrogueriePhenicia"
            ],
            "THREE" => [
                "Title" => "WHAT_ARE_THE_BENEFITS_FOR_USJ_EMPLOYEES_SAD",
                "Desc" => "YOU_WILL_BENEFIT_FROM_A_FREE_PLATINUM_MASTERCARD_SAD"
            ],
            "FOUR" => [
                "Title" => "IS_THERE_ANY_FEE_TO_GET_MY_SUYOOL_PLATINUM_MASTERCARD_DrogueriePhenicia",
                "Desc" => "YOUR_SUYOOL_MASTERCARD_WILL_BE_FREE_OF_CHARGE_ADDICTS_SAD"
            ],
            "FIVE" => [
                "Title" => "WHERE_CAN_I_USE_MY_SUYOOL_PLATINUM_MASTERCARD_DrogueriePhenicia",
                "Desc" => "YOU_CAN_USE_YOUR_SUYOOL_MASTERCARD_AT_ANY_POS_DrogueriePhenicia"
            ],
            "SIX" => [
                "Title" => "WHERE_CAN_I_WITHDRAW_MY_SALARY_IN_CASH_DrogueriePhenicia_MEDCOPAYROLL",
                "Desc" => "USERS_CAN_ACCESS_THEIR_MONEY_FROM_MORE_THAN_700_DrogueriePhenicia_MEDCOPAYROLL"
            ],
        ];

        $parameters['title'] = "S.A.D | Suyool";
        $parameters['desc'] = "Facing today's financial challenges, we moved our payroll to Suyool. You will get your own digital dual-currency account,a Platinum Debit Card & a payment tool with the best rates available.";

        $parameters['infoSection'] = $this->infoSection2;

        return $this->render('sad/index.html.twig', $parameters);
    }

    /**
     * @Route("/phoenix-technology", name="phoenix-technology")
     */
    public function phoenixTechnology(Request $request, TranslatorInterface $translatorInterface): Response
    {
        $parameters = $this->trans->translation($request, $translatorInterface);
        $translatorInterface->setLocale("en");
        $parameters['lang'] = "en";
        $parameters['metaimage'] = "build/images/payroll/phoenixTechnologyMeta.png";
        $parameters['descmeta'] = "Why is Suyool the best option for your payroll?";
        $parameters['faq'] = [
            "ONE" => [
                "Title" => "WHAT_IS_SUYOOL_DrogueriePhenicia",
                "Desc" => "SUYOOL_IS_A_CASHLESS_ECOSYSTEM_THAT_INCORPORATES_DrogueriePhenicia"
            ],
            "TWO" => [
                "Title" => "CAN_ANYONE_OPEN_A_SUYOOL_ACCOUNT_DrogueriePhenicia",
                "Desc" => "ANY_LEBANESE_CITIZEN_CAN_OPEN_A_SUYOOL_ACCOUNT_DrogueriePhenicia"
            ],
            "THREE" => [
                "Title" => "WHAT_ARE_THE_BENEFITS_FOR_USJ_EMPLOYEES_PHOENIX_TECHNOLOGY",
                "Desc" => "YOU_WILL_BENEFIT_FROM_A_FREE_PLATINUM_MASTERCARD_PHOENIX_TECHNOLOGY"
            ],
            "FOUR" => [
                "Title" => "IS_THERE_ANY_FEE_TO_GET_MY_SUYOOL_PLATINUM_MASTERCARD_DrogueriePhenicia",
                "Desc" => "YOUR_SUYOOL_MASTERCARD_WILL_BE_FREE_OF_CHARGE_ADDICTS_PHOENIX_TECHNOLOGY"
            ],
            "FIVE" => [
                "Title" => "WHERE_CAN_I_USE_MY_SUYOOL_PLATINUM_MASTERCARD_DrogueriePhenicia",
                "Desc" => "YOU_CAN_USE_YOUR_SUYOOL_MASTERCARD_AT_ANY_POS_DrogueriePhenicia"
            ],
            "SIX" => [
                "Title" => "WHERE_CAN_I_WITHDRAW_MY_SALARY_IN_CASH_DrogueriePhenicia_MEDCOPAYROLL",
                "Desc" => "USERS_CAN_ACCESS_THEIR_MONEY_FROM_MORE_THAN_700_DrogueriePhenicia_MEDCOPAYROLL"
            ],
        ];

        $parameters['title'] = "Phoenix Technology | Suyool";
        $parameters['desc'] = "Facing today's financial challenges, we moved our payroll to Suyool. You will get your own digital dual-currency account,a Platinum Debit Card & a payment tool with the best rates available.";

        $parameters['infoSection'] = $this->infoSection2;

        return $this->render('phoenix-technology/index.html.twig', $parameters);
    }


    /**
     * @Route("/phoenix-energy", name="phoenix-energy")
     */
    public function phoenixEnergy(Request $request, TranslatorInterface $translatorInterface): Response
    {
        $parameters = $this->trans->translation($request, $translatorInterface);
        $translatorInterface->setLocale("en");
        $parameters['lang'] = "en";
        $parameters['metaimage'] = "build/images/payroll/phoenixEnergyMeta.png";
        $parameters['descmeta'] = "Why is Suyool the best option for your payroll?";
        $parameters['faq'] = [
            "ONE" => [
                "Title" => "WHAT_IS_SUYOOL_DrogueriePhenicia",
                "Desc" => "SUYOOL_IS_A_CASHLESS_ECOSYSTEM_THAT_INCORPORATES_DrogueriePhenicia"
            ],
            "TWO" => [
                "Title" => "CAN_ANYONE_OPEN_A_SUYOOL_ACCOUNT_DrogueriePhenicia",
                "Desc" => "ANY_LEBANESE_CITIZEN_CAN_OPEN_A_SUYOOL_ACCOUNT_DrogueriePhenicia"
            ],
            "THREE" => [
                "Title" => "WHAT_ARE_THE_BENEFITS_FOR_USJ_EMPLOYEES_PHOENIX_ENERGY",
                "Desc" => "YOU_WILL_BENEFIT_FROM_A_FREE_PLATINUM_MASTERCARD_PHOENIX_ENERGY"
            ],
            "FOUR" => [
                "Title" => "IS_THERE_ANY_FEE_TO_GET_MY_SUYOOL_PLATINUM_MASTERCARD_DrogueriePhenicia",
                "Desc" => "YOUR_SUYOOL_MASTERCARD_WILL_BE_FREE_OF_CHARGE_ADDICTS_PHOENIX_ENERGY"
            ],
            "FIVE" => [
                "Title" => "WHERE_CAN_I_USE_MY_SUYOOL_PLATINUM_MASTERCARD_DrogueriePhenicia",
                "Desc" => "YOU_CAN_USE_YOUR_SUYOOL_MASTERCARD_AT_ANY_POS_DrogueriePhenicia"
            ],
            "SIX" => [
                "Title" => "WHERE_CAN_I_WITHDRAW_MY_SALARY_IN_CASH_DrogueriePhenicia_MEDCOPAYROLL",
                "Desc" => "USERS_CAN_ACCESS_THEIR_MONEY_FROM_MORE_THAN_700_DrogueriePhenicia_MEDCOPAYROLL"
            ],
        ];

        $parameters['title'] = "Phoenix Energy | Suyool";
        $parameters['desc'] = "Facing today's financial challenges, we moved our payroll to Suyool. You will get your own digital dual-currency account,a Platinum Debit Card & a payment tool with the best rates available.";

        $parameters['infoSection'] = $this->infoSection2;

        return $this->render('phoenix-energy/index.html.twig', $parameters);
    }


    /**
     * @Route("/phoenix-machinery", name="phoenix-machinery")
     */
    public function phoenixMachinery(Request $request, TranslatorInterface $translatorInterface): Response
    {
        $parameters = $this->trans->translation($request, $translatorInterface);
        $translatorInterface->setLocale("en");
        $parameters['lang'] = "en";
        $parameters['metaimage'] = "build/images/payroll/phoenixMachineryMeta.png";
        $parameters['descmeta'] = "Why is Suyool the best option for your payroll?";
        $parameters['faq'] = [
            "ONE" => [
                "Title" => "WHAT_IS_SUYOOL_DrogueriePhenicia",
                "Desc" => "SUYOOL_IS_A_CASHLESS_ECOSYSTEM_THAT_INCORPORATES_DrogueriePhenicia"
            ],
            "TWO" => [
                "Title" => "CAN_ANYONE_OPEN_A_SUYOOL_ACCOUNT_DrogueriePhenicia",
                "Desc" => "ANY_LEBANESE_CITIZEN_CAN_OPEN_A_SUYOOL_ACCOUNT_DrogueriePhenicia"
            ],
            "THREE" => [
                "Title" => "WHAT_ARE_THE_BENEFITS_FOR_USJ_EMPLOYEES_PHOENIX_MACHINERY",
                "Desc" => "YOU_WILL_BENEFIT_FROM_A_FREE_PLATINUM_MASTERCARD_PHOENIX_MACHINERY"
            ],
            "FOUR" => [
                "Title" => "IS_THERE_ANY_FEE_TO_GET_MY_SUYOOL_PLATINUM_MASTERCARD_DrogueriePhenicia",
                "Desc" => "YOUR_SUYOOL_MASTERCARD_WILL_BE_FREE_OF_CHARGE_ADDICTS_PHOENIX_MACHINERY"
            ],
            "FIVE" => [
                "Title" => "WHERE_CAN_I_USE_MY_SUYOOL_PLATINUM_MASTERCARD_DrogueriePhenicia",
                "Desc" => "YOU_CAN_USE_YOUR_SUYOOL_MASTERCARD_AT_ANY_POS_DrogueriePhenicia"
            ],
            "SIX" => [
                "Title" => "WHERE_CAN_I_WITHDRAW_MY_SALARY_IN_CASH_DrogueriePhenicia_MEDCOPAYROLL",
                "Desc" => "USERS_CAN_ACCESS_THEIR_MONEY_FROM_MORE_THAN_700_DrogueriePhenicia_MEDCOPAYROLL"
            ],
        ];

        $parameters['title'] = "Phoenix Machinery | Suyool";
        $parameters['desc'] = "Facing today's financial challenges, we moved our payroll to Suyool. You will get your own digital dual-currency account,a Platinum Debit Card & a payment tool with the best rates available.";

        $parameters['infoSection'] = $this->infoSection2;

        return $this->render('phoenix-machinery/index.html.twig', $parameters);
    }


    /**
     * @Route("/hopital-saint-charles", name="hopital-saint-charles")
     */
    public function hopitalSaintCharles(Request $request, TranslatorInterface $translatorInterface): Response
    {
        $parameters = $this->trans->translation($request, $translatorInterface);
        $translatorInterface->setLocale("en");
        $parameters['lang'] = "en";
        $parameters['metaimage'] = "build/images/payroll/hopitalSaintCharlesMeta.png";
        $parameters['descmeta'] = "Why is Suyool the best option for your payroll?";
        $parameters['faq'] = [
            "ONE" => [
                "Title" => "WHAT_IS_SUYOOL_DrogueriePhenicia",
                "Desc" => "SUYOOL_IS_A_CASHLESS_ECOSYSTEM_THAT_INCORPORATES_DrogueriePhenicia"
            ],
            "TWO" => [
                "Title" => "CAN_ANYONE_OPEN_A_SUYOOL_ACCOUNT_DrogueriePhenicia",
                "Desc" => "ANY_LEBANESE_CITIZEN_CAN_OPEN_A_SUYOOL_ACCOUNT_DrogueriePhenicia"
            ],
            "THREE" => [
                "Title" => "WHAT_ARE_THE_BENEFITS_FOR_USJ_EMPLOYEES_HOPITAL_SAINT_CHARLES",
                "Desc" => "YOU_WILL_BENEFIT_FROM_A_FREE_PLATINUM_MASTERCARD_HOPITAL_SAINT_CHARLES"
            ],
            "FOUR" => [
                "Title" => "IS_THERE_ANY_FEE_TO_GET_MY_SUYOOL_PLATINUM_MASTERCARD_DrogueriePhenicia",
                "Desc" => "YOUR_SUYOOL_MASTERCARD_WILL_BE_FREE_OF_CHARGE_ADDICTS_HOPITAL_SAINT_CHARLES"
            ],
            "FIVE" => [
                "Title" => "WHERE_CAN_I_USE_MY_SUYOOL_PLATINUM_MASTERCARD_DrogueriePhenicia",
                "Desc" => "YOU_CAN_USE_YOUR_SUYOOL_MASTERCARD_AT_ANY_POS_DrogueriePhenicia"
            ],
            "SIX" => [
                "Title" => "WHERE_CAN_I_WITHDRAW_MY_SALARY_IN_CASH_DrogueriePhenicia_MEDCOPAYROLL",
                "Desc" => "USERS_CAN_ACCESS_THEIR_MONEY_FROM_MORE_THAN_700_DrogueriePhenicia_MEDCOPAYROLL"
            ],
        ];

        $parameters['title'] = "Hôpital Saint-Charles | Suyool";
        $parameters['desc'] = "Facing today's financial challenges, we moved our payroll to Suyool. You will get your own digital dual-currency account,a Platinum Debit Card & a payment tool with the best rates available.";

        $parameters['infoSection'] = $this->infoSection2;

        return $this->render('hopital-saint-charles/index.html.twig', $parameters);
    }

    /**
     * @Route("/bluefield", name="bluefield-payroll")
     */
    public function bluefield(Request $request, TranslatorInterface $translatorInterface): Response
    {
        $parameters = $this->trans->translation($request, $translatorInterface);
        $translatorInterface->setLocale("en");
        $parameters['lang'] = "en";
        $parameters['metaimage'] = "build/images/payroll/bluefieldMeta.png";
        $parameters['descmeta'] = "Why is Suyool the best option for your payroll?";
        $parameters['faq'] = [
            "ONE" => [
                "Title" => "WHAT_IS_SUYOOL_DrogueriePhenicia",
                "Desc" => "SUYOOL_IS_A_CASHLESS_ECOSYSTEM_THAT_INCORPORATES_DrogueriePhenicia"
            ],
            "TWO" => [
                "Title" => "CAN_ANYONE_OPEN_A_SUYOOL_ACCOUNT_DrogueriePhenicia",
                "Desc" => "ANY_LEBANESE_CITIZEN_CAN_OPEN_A_SUYOOL_ACCOUNT_DrogueriePhenicia"
            ],
            "THREE" => [
                "Title" => "WHAT_ARE_THE_BENEFITS_FOR_USJ_EMPLOYEES_FIELD",
                "Desc" => "YOU_WILL_BENEFIT_FROM_A_FREE_PLATINUM_MASTERCARD_FIELD"
            ],
            "FOUR" => [
                "Title" => "IS_THERE_ANY_FEE_TO_GET_MY_SUYOOL_PLATINUM_MASTERCARD_DrogueriePhenicia",
                "Desc" => "YOUR_SUYOOL_MASTERCARD_WILL_BE_FREE_OF_CHARGE_ADDICTS_FIELD"
            ],
            "FIVE" => [
                "Title" => "WHERE_CAN_I_USE_MY_SUYOOL_PLATINUM_MASTERCARD_DrogueriePhenicia",
                "Desc" => "YOU_CAN_USE_YOUR_SUYOOL_MASTERCARD_AT_ANY_POS_DrogueriePhenicia"
            ],
            "SIX" => [
                "Title" => "WHERE_CAN_I_WITHDRAW_MY_SALARY_IN_CASH_DrogueriePhenicia_MEDCOPAYROLL",
                "Desc" => "USERS_CAN_ACCESS_THEIR_MONEY_FROM_MORE_THAN_700_DrogueriePhenicia_MEDCOPAYROLL"
            ],
        ];

        $parameters['title'] = "Blue Field Group | Suyool";
        $parameters['desc'] = "Facing today's financial challenges, we moved our payroll to Suyool. You will get your own digital dual-currency account,a Platinum Debit Card & a payment tool with the best rates available.";

        $parameters['infoSection'] = $this->infoSection2;

        return $this->render('bluefield/index.html.twig', $parameters);
    }

    /**
     * @Route("/ctnet", name="ctnet-payroll")
     */
    public function ctnet(Request $request, TranslatorInterface $translatorInterface): Response
    {
        $parameters = $this->trans->translation($request, $translatorInterface);
        $translatorInterface->setLocale("en");
        $parameters['lang'] = "en";
        $parameters['metaimage'] = "build/images/payroll/ctnetMeta.png";
        $parameters['descmeta'] = "Why is Suyool the best option for your payroll?";
        $parameters['faq'] = [
            "ONE" => [
                "Title" => "WHAT_IS_SUYOOL_DrogueriePhenicia",
                "Desc" => "SUYOOL_IS_A_CASHLESS_ECOSYSTEM_THAT_INCORPORATES_DrogueriePhenicia"
            ],
            "TWO" => [
                "Title" => "CAN_ANYONE_OPEN_A_SUYOOL_ACCOUNT_DrogueriePhenicia",
                "Desc" => "ANY_LEBANESE_CITIZEN_CAN_OPEN_A_SUYOOL_ACCOUNT_DrogueriePhenicia"
            ],
            "THREE" => [
                "Title" => "WHAT_ARE_THE_BENEFITS_FOR_USJ_EMPLOYEES_CTNET",
                "Desc" => "YOU_WILL_BENEFIT_FROM_A_FREE_PLATINUM_MASTERCARD_CTNET"
            ],
            "FOUR" => [
                "Title" => "IS_THERE_ANY_FEE_TO_GET_MY_SUYOOL_PLATINUM_MASTERCARD_DrogueriePhenicia",
                "Desc" => "YOUR_SUYOOL_MASTERCARD_WILL_BE_FREE_OF_CHARGE_ADDICTS_CTNET"
            ],
            "FIVE" => [
                "Title" => "WHERE_CAN_I_USE_MY_SUYOOL_PLATINUM_MASTERCARD_DrogueriePhenicia",
                "Desc" => "YOU_CAN_USE_YOUR_SUYOOL_MASTERCARD_AT_ANY_POS_DrogueriePhenicia"
            ],
            "SIX" => [
                "Title" => "WHERE_CAN_I_WITHDRAW_MY_SALARY_IN_CASH_DrogueriePhenicia_MEDCOPAYROLL",
                "Desc" => "USERS_CAN_ACCESS_THEIR_MONEY_FROM_MORE_THAN_700_DrogueriePhenicia_MEDCOPAYROLL"
            ],
        ];

        $parameters['title'] = "CTNet | Suyool";
        $parameters['desc'] = "Facing today's financial challenges, we moved our payroll to Suyool. You will get your own digital dual-currency account,a Platinum Debit Card & a payment tool with the best rates available.";

        $parameters['infoSection'] = $this->infoSection2;

        return $this->render('ctnet/index.html.twig', $parameters);
    }

    /**
     * @Route("/gespa-sal", name="gespa-sal-payroll")
     */
    public function gespa_sal(Request $request, TranslatorInterface $translatorInterface): Response
    {
        $parameters = $this->trans->translation($request, $translatorInterface);
        $translatorInterface->setLocale("en");
        $parameters['lang'] = "en";
        $parameters['metaimage'] = "build/images/payroll/gespaMeta.png";
        $parameters['descmeta'] = "Why is Suyool the best option for your payroll?";
        $parameters['faq'] = [
            "ONE" => [
                "Title" => "WHAT_IS_SUYOOL_DrogueriePhenicia",
                "Desc" => "SUYOOL_IS_A_CASHLESS_ECOSYSTEM_THAT_INCORPORATES_DrogueriePhenicia"
            ],
            "TWO" => [
                "Title" => "CAN_ANYONE_OPEN_A_SUYOOL_ACCOUNT_DrogueriePhenicia",
                "Desc" => "ANY_LEBANESE_CITIZEN_CAN_OPEN_A_SUYOOL_ACCOUNT_DrogueriePhenicia"
            ],
            "THREE" => [
                "Title" => "WHAT_ARE_THE_BENEFITS_FOR_USJ_EMPLOYEES_GESPASAL",
                "Desc" => "YOU_WILL_BENEFIT_FROM_A_FREE_PLATINUM_MASTERCARD_GESPASAL"
            ],
            "FOUR" => [
                "Title" => "IS_THERE_ANY_FEE_TO_GET_MY_SUYOOL_PLATINUM_MASTERCARD_DrogueriePhenicia",
                "Desc" => "YOUR_SUYOOL_MASTERCARD_WILL_BE_FREE_OF_CHARGE_ADDICTS_GESPASAL"
            ],
            "FIVE" => [
                "Title" => "WHERE_CAN_I_USE_MY_SUYOOL_PLATINUM_MASTERCARD_DrogueriePhenicia",
                "Desc" => "YOU_CAN_USE_YOUR_SUYOOL_MASTERCARD_AT_ANY_POS_DrogueriePhenicia"
            ],
            "SIX" => [
                "Title" => "WHERE_CAN_I_WITHDRAW_MY_SALARY_IN_CASH_DrogueriePhenicia_MEDCOPAYROLL",
                "Desc" => "USERS_CAN_ACCESS_THEIR_MONEY_FROM_MORE_THAN_700_DrogueriePhenicia_MEDCOPAYROLL"
            ],
        ];

        $parameters['title'] = "Gespa SAL | Suyool";
        $parameters['desc'] = "Facing today's financial challenges, we moved our payroll to Suyool. You will get your own digital dual-currency account,a Platinum Debit Card & a payment tool with the best rates available.";

        $parameters['infoSection'] = $this->infoSection2;

        return $this->render('gespaSal/index.html.twig', $parameters);
    }

    /**
     * @Route("/gespa-international-sal", name="gespa-international-sal-payroll")
     */
    public function gespa_sal_international(Request $request, TranslatorInterface $translatorInterface): Response
    {
        $parameters = $this->trans->translation($request, $translatorInterface);
        $translatorInterface->setLocale("en");
        $parameters['lang'] = "en";
        $parameters['metaimage'] = "build/images/payroll/gespaintMeta.png";
        $parameters['descmeta'] = "Why is Suyool the best option for your payroll?";
        $parameters['faq'] = [
            "ONE" => [
                "Title" => "WHAT_IS_SUYOOL_DrogueriePhenicia",
                "Desc" => "SUYOOL_IS_A_CASHLESS_ECOSYSTEM_THAT_INCORPORATES_DrogueriePhenicia"
            ],
            "TWO" => [
                "Title" => "CAN_ANYONE_OPEN_A_SUYOOL_ACCOUNT_DrogueriePhenicia",
                "Desc" => "ANY_LEBANESE_CITIZEN_CAN_OPEN_A_SUYOOL_ACCOUNT_DrogueriePhenicia"
            ],
            "THREE" => [
                "Title" => "WHAT_ARE_THE_BENEFITS_FOR_USJ_EMPLOYEES_GESPASALINT",
                "Desc" => "YOU_WILL_BENEFIT_FROM_A_FREE_PLATINUM_MASTERCARD_GESPASALINT"
            ],
            "FOUR" => [
                "Title" => "IS_THERE_ANY_FEE_TO_GET_MY_SUYOOL_PLATINUM_MASTERCARD_DrogueriePhenicia",
                "Desc" => "YOUR_SUYOOL_MASTERCARD_WILL_BE_FREE_OF_CHARGE_ADDICTS_GESPASALINT"
            ],
            "FIVE" => [
                "Title" => "WHERE_CAN_I_USE_MY_SUYOOL_PLATINUM_MASTERCARD_DrogueriePhenicia",
                "Desc" => "YOU_CAN_USE_YOUR_SUYOOL_MASTERCARD_AT_ANY_POS_DrogueriePhenicia"
            ],
            "SIX" => [
                "Title" => "WHERE_CAN_I_WITHDRAW_MY_SALARY_IN_CASH_DrogueriePhenicia_MEDCOPAYROLL",
                "Desc" => "USERS_CAN_ACCESS_THEIR_MONEY_FROM_MORE_THAN_700_DrogueriePhenicia_MEDCOPAYROLL"
            ],
        ];

        $parameters['title'] = "Gespa International SAL | Suyool";
        $parameters['desc'] = "Facing today's financial challenges, we moved our payroll to Suyool. You will get your own digital dual-currency account,a Platinum Debit Card & a payment tool with the best rates available.";

        $parameters['infoSection'] = $this->infoSection2;

        return $this->render('gespaintSal/index.html.twig', $parameters);
    }


    /**
     * @Route("/altatrade", name="altatrade-payroll")
     */
    public function altatrade(Request $request, TranslatorInterface $translatorInterface): Response
    {
        $parameters = $this->trans->translation($request, $translatorInterface);
        $translatorInterface->setLocale("en");
        $parameters['lang'] = "en";
        $parameters['metaimage'] = "build/images/payroll/altatradeMeta.png";
        $parameters['descmeta'] = "Why is Suyool the best option for your payroll?";
        $parameters['faq'] = [
            "ONE" => [
                "Title" => "WHAT_IS_SUYOOL_DrogueriePhenicia",
                "Desc" => "SUYOOL_IS_A_CASHLESS_ECOSYSTEM_THAT_INCORPORATES_DrogueriePhenicia"
            ],
            "TWO" => [
                "Title" => "CAN_ANYONE_OPEN_A_SUYOOL_ACCOUNT_DrogueriePhenicia",
                "Desc" => "ANY_LEBANESE_CITIZEN_CAN_OPEN_A_SUYOOL_ACCOUNT_DrogueriePhenicia"
            ],
            "THREE" => [
                "Title" => "WHAT_ARE_THE_BENEFITS_FOR_USJ_EMPLOYEES_ALTATRADE",
                "Desc" => "YOU_WILL_BENEFIT_FROM_A_FREE_PLATINUM_MASTERCARD_ALTATRADE"
            ],
            "FOUR" => [
                "Title" => "IS_THERE_ANY_FEE_TO_GET_MY_SUYOOL_PLATINUM_MASTERCARD_DrogueriePhenicia",
                "Desc" => "YOUR_SUYOOL_MASTERCARD_WILL_BE_FREE_OF_CHARGE_ADDICTS_ALTATRADE"
            ],
            "FIVE" => [
                "Title" => "WHERE_CAN_I_USE_MY_SUYOOL_PLATINUM_MASTERCARD_DrogueriePhenicia",
                "Desc" => "YOU_CAN_USE_YOUR_SUYOOL_MASTERCARD_AT_ANY_POS_DrogueriePhenicia"
            ],
            "SIX" => [
                "Title" => "WHERE_CAN_I_WITHDRAW_MY_SALARY_IN_CASH_DrogueriePhenicia_MEDCOPAYROLL",
                "Desc" => "USERS_CAN_ACCESS_THEIR_MONEY_FROM_MORE_THAN_700_DrogueriePhenicia_MEDCOPAYROLL"
            ],
        ];

        $parameters['title'] = "Altatrade | Suyool";
        $parameters['desc'] = "Facing today's financial challenges, we moved our payroll to Suyool. You will get your own digital dual-currency account,a Platinum Debit Card & a payment tool with the best rates available.";

        $parameters['infoSection'] = $this->infoSection2;

        return $this->render('merchants/altratrade.html.twig', $parameters);
    }
    /**
     * @Route("/seapera-sal", name="seapera-sal-payroll")
     */
    public function seapera_sal(Request $request, TranslatorInterface $translatorInterface): Response
    {
        $parameters = $this->trans->translation($request, $translatorInterface);
        $translatorInterface->setLocale("en");
        $parameters['lang'] = "en";
        $parameters['metaimage'] = "build/images/payroll/SeaperaMeta.png";
        $parameters['descmeta'] = "Why is Suyool the best option for your payroll?";
        $parameters['faq'] = [
            "ONE" => [
                "Title" => "WHAT_IS_SUYOOL_DrogueriePhenicia",
                "Desc" => "SUYOOL_IS_A_CASHLESS_ECOSYSTEM_THAT_INCORPORATES_DrogueriePhenicia"
            ],
            "TWO" => [
                "Title" => "CAN_ANYONE_OPEN_A_SUYOOL_ACCOUNT_DrogueriePhenicia",
                "Desc" => "ANY_LEBANESE_CITIZEN_CAN_OPEN_A_SUYOOL_ACCOUNT_DrogueriePhenicia"
            ],
            "THREE" => [
                "Title" => "WHAT_ARE_THE_BENEFITS_FOR_USJ_EMPLOYEES_SEAPERA",
                "Desc" => "YOU_WILL_BENEFIT_FROM_A_FREE_PLATINUM_MASTERCARD_SEAPERA"
            ],
            "FOUR" => [
                "Title" => "IS_THERE_ANY_FEE_TO_GET_MY_SUYOOL_PLATINUM_MASTERCARD_DrogueriePhenicia",
                "Desc" => "YOUR_SUYOOL_MASTERCARD_WILL_BE_FREE_OF_CHARGE_ADDICTS_SEAPERA"
            ],
            "FIVE" => [
                "Title" => "WHERE_CAN_I_USE_MY_SUYOOL_PLATINUM_MASTERCARD_DrogueriePhenicia",
                "Desc" => "YOU_CAN_USE_YOUR_SUYOOL_MASTERCARD_AT_ANY_POS_DrogueriePhenicia"
            ],
            "SIX" => [
                "Title" => "WHERE_CAN_I_WITHDRAW_MY_SALARY_IN_CASH_DrogueriePhenicia_MEDCOPAYROLL",
                "Desc" => "USERS_CAN_ACCESS_THEIR_MONEY_FROM_MORE_THAN_700_DrogueriePhenicia_MEDCOPAYROLL"
            ],
        ];

        $parameters['title'] = "Seapera SAL | Suyool";
        $parameters['desc'] = "Facing today's financial challenges, we moved our payroll to Suyool. You will get your own digital dual-currency account,a Platinum Debit Card & a payment tool with the best rates available.";

        $parameters['infoSection'] = $this->infoSection2;

        return $this->render('merchants/seapera.html.twig', $parameters);
    }

    /**
     * @Route("/mediapak", name="Mediapak-payroll")
     */
    public function Mediapak(Request $request, TranslatorInterface $translatorInterface): Response
    {
        $parameters = $this->trans->translation($request, $translatorInterface);
        $translatorInterface->setLocale("en");
        $parameters['lang'] = "en";
        $parameters['metaimage'] = "build/images/payroll/MediapakMeta.png";
        $parameters['descmeta'] = "Why is Suyool the best option for your payroll?";
        $parameters['faq'] = [
            "ONE" => [
                "Title" => "WHAT_IS_SUYOOL_DrogueriePhenicia",
                "Desc" => "SUYOOL_IS_A_CASHLESS_ECOSYSTEM_THAT_INCORPORATES_DrogueriePhenicia"
            ],
            "TWO" => [
                "Title" => "CAN_ANYONE_OPEN_A_SUYOOL_ACCOUNT_DrogueriePhenicia",
                "Desc" => "ANY_LEBANESE_CITIZEN_CAN_OPEN_A_SUYOOL_ACCOUNT_DrogueriePhenicia"
            ],
            "THREE" => [
                "Title" => "WHAT_ARE_THE_BENEFITS_FOR_USJ_EMPLOYEES_MEDIAPAK",
                "Desc" => "YOU_WILL_BENEFIT_FROM_A_FREE_PLATINUM_MASTERCARD_MEDIAPAK"
            ],
            "FOUR" => [
                "Title" => "IS_THERE_ANY_FEE_TO_GET_MY_SUYOOL_PLATINUM_MASTERCARD_DrogueriePhenicia",
                "Desc" => "YOUR_SUYOOL_MASTERCARD_WILL_BE_FREE_OF_CHARGE_ADDICTS_MEDIAPAK"
            ],
            "FIVE" => [
                "Title" => "WHERE_CAN_I_USE_MY_SUYOOL_PLATINUM_MASTERCARD_DrogueriePhenicia",
                "Desc" => "YOU_CAN_USE_YOUR_SUYOOL_MASTERCARD_AT_ANY_POS_DrogueriePhenicia"
            ],
            "SIX" => [
                "Title" => "WHERE_CAN_I_WITHDRAW_MY_SALARY_IN_CASH_DrogueriePhenicia_MEDCOPAYROLL",
                "Desc" => "USERS_CAN_ACCESS_THEIR_MONEY_FROM_MORE_THAN_700_DrogueriePhenicia_MEDCOPAYROLL"
            ],
        ];

        $parameters['title'] = "Mediapak | Suyool";
        $parameters['desc'] = "Facing today's financial challenges, we moved our payroll to Suyool. You will get your own digital dual-currency account,a Platinum Debit Card & a payment tool with the best rates available.";

        $parameters['infoSection'] = $this->infoSection2;

        return $this->render('merchants/mediapack.html.twig', $parameters);
    }

    /**
     * @Route("/alkarma-immobiliere-sal", name="alkarma-immobiliere-payroll")
     */
    public function immobiliere(Request $request, TranslatorInterface $translatorInterface): Response
    {
        $parameters = $this->trans->translation($request, $translatorInterface);
        $translatorInterface->setLocale("en");
        $parameters['lang'] = "en";
        $parameters['metaimage'] = "build/images/payroll/alkarmaMeta.png";
        $parameters['descmeta'] = "Why is Suyool the best option for your payroll?";
        $parameters['faq'] = [
            "ONE" => [
                "Title" => "WHAT_IS_SUYOOL_DrogueriePhenicia",
                "Desc" => "SUYOOL_IS_A_CASHLESS_ECOSYSTEM_THAT_INCORPORATES_DrogueriePhenicia"
            ],
            "TWO" => [
                "Title" => "CAN_ANYONE_OPEN_A_SUYOOL_ACCOUNT_DrogueriePhenicia",
                "Desc" => "ANY_LEBANESE_CITIZEN_CAN_OPEN_A_SUYOOL_ACCOUNT_DrogueriePhenicia"
            ],
            "THREE" => [
                "Title" => "WHAT_ARE_THE_BENEFITS_FOR_USJ_EMPLOYEES_IMMOBILIERE",
                "Desc" => "YOU_WILL_BENEFIT_FROM_A_FREE_PLATINUM_MASTERCARD_IMMOBILIERE"
            ],
            "FOUR" => [
                "Title" => "IS_THERE_ANY_FEE_TO_GET_MY_SUYOOL_PLATINUM_MASTERCARD_DrogueriePhenicia",
                "Desc" => "YOUR_SUYOOL_MASTERCARD_WILL_BE_FREE_OF_CHARGE_ADDICTS_IMMOBILIERE"
            ],
            "FIVE" => [
                "Title" => "WHERE_CAN_I_USE_MY_SUYOOL_PLATINUM_MASTERCARD_DrogueriePhenicia",
                "Desc" => "YOU_CAN_USE_YOUR_SUYOOL_MASTERCARD_AT_ANY_POS_DrogueriePhenicia"
            ],
            "SIX" => [
                "Title" => "WHERE_CAN_I_WITHDRAW_MY_SALARY_IN_CASH_DrogueriePhenicia_MEDCOPAYROLL",
                "Desc" => "USERS_CAN_ACCESS_THEIR_MONEY_FROM_MORE_THAN_700_DrogueriePhenicia_MEDCOPAYROLL"
            ],
        ];

        $parameters['title'] = "Al Karma Immobiliere SAL | Suyool";
        $parameters['desc'] = "Facing today's financial challenges, we moved our payroll to Suyool. You will get your own digital dual-currency account,a Platinum Debit Card & a payment tool with the best rates available.";

        $parameters['infoSection'] = $this->infoSection2;

        return $this->render('alkarma/index.html.twig', $parameters);
    }

    /**
     * @Route("/mic-sal", name="mic-sal-payroll")
     */
    public function micSAL(Request $request, TranslatorInterface $translatorInterface): Response
    {
        $parameters = $this->trans->translation($request, $translatorInterface);
        $translatorInterface->setLocale("en");
        $parameters['lang'] = "en";
        $parameters['metaimage'] = "build/images/payroll/micMeta.png";
        $parameters['descmeta'] = "Why is Suyool the best option for your payroll?";
        $parameters['faq'] = [
            "ONE" => [
                "Title" => "WHAT_IS_SUYOOL_DrogueriePhenicia",
                "Desc" => "SUYOOL_IS_A_CASHLESS_ECOSYSTEM_THAT_INCORPORATES_DrogueriePhenicia"
            ],
            "TWO" => [
                "Title" => "CAN_ANYONE_OPEN_A_SUYOOL_ACCOUNT_DrogueriePhenicia",
                "Desc" => "ANY_LEBANESE_CITIZEN_CAN_OPEN_A_SUYOOL_ACCOUNT_DrogueriePhenicia"
            ],
            "THREE" => [
                "Title" => "WHAT_ARE_THE_BENEFITS_FOR_USJ_EMPLOYEES_MIC",
                "Desc" => "YOU_WILL_BENEFIT_FROM_A_FREE_PLATINUM_MASTERCARD_MIC"
            ],
            "FOUR" => [
                "Title" => "IS_THERE_ANY_FEE_TO_GET_MY_SUYOOL_PLATINUM_MASTERCARD_DrogueriePhenicia",
                "Desc" => "YOUR_SUYOOL_MASTERCARD_WILL_BE_FREE_OF_CHARGE_ADDICTS_MIC"
            ],
            "FIVE" => [
                "Title" => "WHERE_CAN_I_USE_MY_SUYOOL_PLATINUM_MASTERCARD_DrogueriePhenicia",
                "Desc" => "YOU_CAN_USE_YOUR_SUYOOL_MASTERCARD_AT_ANY_POS_DrogueriePhenicia"
            ],
            "SIX" => [
                "Title" => "WHERE_CAN_I_WITHDRAW_MY_SALARY_IN_CASH_DrogueriePhenicia_MEDCOPAYROLL",
                "Desc" => "USERS_CAN_ACCESS_THEIR_MONEY_FROM_MORE_THAN_700_DrogueriePhenicia_MEDCOPAYROLL"
            ],
        ];

        $parameters['title'] = "MIC S.A.L | Suyool";
        $parameters['desc'] = "Facing today's financial challenges, we moved our payroll to Suyool. You will get your own digital dual-currency account,a Platinum Debit Card & a payment tool with the best rates available.";

        $parameters['infoSection'] = $this->infoSection2;

        return $this->render('mic/index.html.twig', $parameters);
    }

    /**
     * @Route("/bcl-sal", name="bcl-sal-payroll")
     */
    public function bclSAL(Request $request, TranslatorInterface $translatorInterface): Response
    {
        $parameters = $this->trans->translation($request, $translatorInterface);
        $translatorInterface->setLocale("en");
        $parameters['lang'] = "en";
        $parameters['metaimage'] = "build/images/payroll/bclMeta.png";
        $parameters['descmeta'] = "Why is Suyool the best option for your payroll?";
        $parameters['faq'] = [
            "ONE" => [
                "Title" => "WHAT_IS_SUYOOL_DrogueriePhenicia",
                "Desc" => "SUYOOL_IS_A_CASHLESS_ECOSYSTEM_THAT_INCORPORATES_DrogueriePhenicia"
            ],
            "TWO" => [
                "Title" => "CAN_ANYONE_OPEN_A_SUYOOL_ACCOUNT_DrogueriePhenicia",
                "Desc" => "ANY_LEBANESE_CITIZEN_CAN_OPEN_A_SUYOOL_ACCOUNT_DrogueriePhenicia"
            ],
            "THREE" => [
                "Title" => "WHAT_ARE_THE_BENEFITS_FOR_USJ_EMPLOYEES_BCL",
                "Desc" => "YOU_WILL_BENEFIT_FROM_A_FREE_PLATINUM_MASTERCARD_BCL"
            ],
            "FOUR" => [
                "Title" => "IS_THERE_ANY_FEE_TO_GET_MY_SUYOOL_PLATINUM_MASTERCARD_DrogueriePhenicia",
                "Desc" => "YOUR_SUYOOL_MASTERCARD_WILL_BE_FREE_OF_CHARGE_ADDICTS_BCL"
            ],
            "FIVE" => [
                "Title" => "WHERE_CAN_I_USE_MY_SUYOOL_PLATINUM_MASTERCARD_DrogueriePhenicia",
                "Desc" => "YOU_CAN_USE_YOUR_SUYOOL_MASTERCARD_AT_ANY_POS_DrogueriePhenicia"
            ],
            "SIX" => [
                "Title" => "WHERE_CAN_I_WITHDRAW_MY_SALARY_IN_CASH_DrogueriePhenicia_MEDCOPAYROLL",
                "Desc" => "USERS_CAN_ACCESS_THEIR_MONEY_FROM_MORE_THAN_700_DrogueriePhenicia_MEDCOPAYROLL"
            ],
        ];

        $parameters['title'] = "BCL S.A.L | Suyool";
        $parameters['desc'] = "Facing today's financial challenges, we moved our payroll to Suyool. You will get your own digital dual-currency account,a Platinum Debit Card & a payment tool with the best rates available.";

        $parameters['infoSection'] = $this->infoSection2;

        return $this->render('bcl/index.html.twig', $parameters);
    }

    /**
     * @Route("/the-net-global", name="the_net_global")
     */
    public function thenetglobal(Request $request, TranslatorInterface $translatorInterface): Response
    {
        $parameters = $this->trans->translation($request, $translatorInterface);
        $translatorInterface->setLocale("en");
        $parameters['lang'] = "en";
        $parameters['metaimage'] = "build/images/payroll/theMeta2.png";
        $parameters['descmeta'] = "Why is Suyool the best option for your payroll?";
        $parameters['faq'] = [
            "ONE" => [
                "Title" => "WHAT_IS_SUYOOL_DrogueriePhenicia",
                "Desc" => "SUYOOL_IS_A_CASHLESS_ECOSYSTEM_THAT_INCORPORATES_DrogueriePhenicia"
            ],
            "TWO" => [
                "Title" => "CAN_ANYONE_OPEN_A_SUYOOL_ACCOUNT_DrogueriePhenicia",
                "Desc" => "ANY_LEBANESE_CITIZEN_CAN_OPEN_A_SUYOOL_ACCOUNT_DrogueriePhenicia"
            ],
            "THREE" => [
                "Title" => "WHAT_ARE_THE_BENEFITS_FOR_USJ_EMPLOYEES_THE",
                "Desc" => "YOU_WILL_BENEFIT_FROM_A_FREE_PLATINUM_MASTERCARD_THE"
            ],
            "FOUR" => [
                "Title" => "IS_THERE_ANY_FEE_TO_GET_MY_SUYOOL_PLATINUM_MASTERCARD_DrogueriePhenicia",
                "Desc" => "YOUR_SUYOOL_MASTERCARD_WILL_BE_FREE_OF_CHARGE_ADDICTS_THE"
            ],
            "FIVE" => [
                "Title" => "WHERE_CAN_I_USE_MY_SUYOOL_PLATINUM_MASTERCARD_DrogueriePhenicia",
                "Desc" => "YOU_CAN_USE_YOUR_SUYOOL_MASTERCARD_AT_ANY_POS_DrogueriePhenicia"
            ],
            "SIX" => [
                "Title" => "WHERE_CAN_I_WITHDRAW_MY_SALARY_IN_CASH_DrogueriePhenicia_MEDCOPAYROLL",
                "Desc" => "USERS_CAN_ACCESS_THEIR_MONEY_FROM_MORE_THAN_700_DrogueriePhenicia_MEDCOPAYROLL"
            ],
        ];

        $parameters['title'] = "The Net Global | Suyool";
        $parameters['desc'] = "Facing today's financial challenges, we moved our payroll to Suyool. You will get your own digital dual-currency account,a Platinum Debit Card & a payment tool with the best rates available.";

        $parameters['infoSection'] = $this->infoSection2;

        return $this->render('thenetglobal/index.html.twig', $parameters);
    }

    /**
     * @Route("/itineris", name="the_itineris")
     */
    public function itineris(Request $request, TranslatorInterface $translatorInterface): Response
    {
        $parameters = $this->trans->translation($request, $translatorInterface);
        $translatorInterface->setLocale("en");
        $parameters['lang'] = "en";
        $parameters['metaimage'] = "build/images/payroll/itinerisMeta.png";
        $parameters['descmeta'] = "Why is Suyool the best option for your payroll?";
        $parameters['faq'] = [
            "ONE" => [
                "Title" => "WHAT_IS_SUYOOL_DrogueriePhenicia",
                "Desc" => "SUYOOL_IS_A_CASHLESS_ECOSYSTEM_THAT_INCORPORATES_DrogueriePhenicia"
            ],
            "TWO" => [
                "Title" => "CAN_ANYONE_OPEN_A_SUYOOL_ACCOUNT_DrogueriePhenicia",
                "Desc" => "ANY_LEBANESE_CITIZEN_CAN_OPEN_A_SUYOOL_ACCOUNT_DrogueriePhenicia"
            ],
            "THREE" => [
                "Title" => "WHAT_ARE_THE_BENEFITS_FOR_USJ_EMPLOYEES_ITINERIS",
                "Desc" => "YOU_WILL_BENEFIT_FROM_A_FREE_PLATINUM_MASTERCARD_ITINERIS"
            ],
            "FOUR" => [
                "Title" => "IS_THERE_ANY_FEE_TO_GET_MY_SUYOOL_PLATINUM_MASTERCARD_DrogueriePhenicia",
                "Desc" => "YOUR_SUYOOL_MASTERCARD_WILL_BE_FREE_OF_CHARGE_ADDICTS_ITINERIS"
            ],
            "FIVE" => [
                "Title" => "WHERE_CAN_I_USE_MY_SUYOOL_PLATINUM_MASTERCARD_DrogueriePhenicia",
                "Desc" => "YOU_CAN_USE_YOUR_SUYOOL_MASTERCARD_AT_ANY_POS_DrogueriePhenicia"
            ],
            "SIX" => [
                "Title" => "WHERE_CAN_I_WITHDRAW_MY_SALARY_IN_CASH_DrogueriePhenicia_MEDCOPAYROLL",
                "Desc" => "USERS_CAN_ACCESS_THEIR_MONEY_FROM_MORE_THAN_700_DrogueriePhenicia_MEDCOPAYROLL"
            ],
        ];

        $parameters['title'] = "Itineris SARL | Suyool";
        $parameters['desc'] = "Facing today's financial challenges, we moved our payroll to Suyool. You will get your own digital dual-currency account,a Platinum Debit Card & a payment tool with the best rates available.";

        $parameters['infoSection'] = $this->infoSection2;

        return $this->render('itineris/index.html.twig', $parameters);
    }

    /**
     * @Route("/zein-j-harb-partner", name="zein-j-harb-partner")
     */
    public function zein(Request $request, TranslatorInterface $translatorInterface): Response
    {
        $parameters = $this->trans->translation($request, $translatorInterface);
        $translatorInterface->setLocale("en");
        $parameters['lang'] = "en";
        $parameters['metaimage'] = "build/images/payroll/zpMeta.png";
        $parameters['descmeta'] = "Why is Suyool the best option for your payroll?";
        $parameters['faq'] = [
            "ONE" => [
                "Title" => "WHAT_IS_SUYOOL_DrogueriePhenicia",
                "Desc" => "SUYOOL_IS_A_CASHLESS_ECOSYSTEM_THAT_INCORPORATES_DrogueriePhenicia"
            ],
            "TWO" => [
                "Title" => "CAN_ANYONE_OPEN_A_SUYOOL_ACCOUNT_DrogueriePhenicia",
                "Desc" => "ANY_LEBANESE_CITIZEN_CAN_OPEN_A_SUYOOL_ACCOUNT_DrogueriePhenicia"
            ],
            "THREE" => [
                "Title" => "WHAT_ARE_THE_BENEFITS_FOR_USJ_EMPLOYEES_ZP",
                "Desc" => "YOU_WILL_BENEFIT_FROM_A_FREE_PLATINUM_MASTERCARD_ZP"
            ],
            "FOUR" => [
                "Title" => "IS_THERE_ANY_FEE_TO_GET_MY_SUYOOL_PLATINUM_MASTERCARD_DrogueriePhenicia",
                "Desc" => "YOUR_SUYOOL_MASTERCARD_WILL_BE_FREE_OF_CHARGE_ADDICTS_ZP"
            ],
            "FIVE" => [
                "Title" => "WHERE_CAN_I_USE_MY_SUYOOL_PLATINUM_MASTERCARD_DrogueriePhenicia",
                "Desc" => "YOU_CAN_USE_YOUR_SUYOOL_MASTERCARD_AT_ANY_POS_DrogueriePhenicia"
            ],
            "SIX" => [
                "Title" => "WHERE_CAN_I_WITHDRAW_MY_SALARY_IN_CASH_DrogueriePhenicia_MEDCOPAYROLL",
                "Desc" => "USERS_CAN_ACCESS_THEIR_MONEY_FROM_MORE_THAN_700_DrogueriePhenicia_MEDCOPAYROLL"
            ],
        ];

        $parameters['title'] = "Zein J. Harb & Partner | Suyool";
        $parameters['desc'] = "Facing today's financial challenges, we moved our payroll to Suyool. You will get your own digital dual-currency account,a Platinum Debit Card & a payment tool with the best rates available.";

        $parameters['infoSection'] = $this->infoSection2;

        return $this->render('zp/index.html.twig', $parameters);
    }

    /**
     * @Route("/interform-group", name="interform-app")
     */
    public function interform(Request $request, TranslatorInterface $translatorInterface): Response
    {
        $parameters = $this->trans->translation($request, $translatorInterface);
        $translatorInterface->setLocale("en");
        $parameters['lang'] = "en";
        $parameters['metaimage'] = "build/images/payroll/interformMeta.png";
        $parameters['descmeta'] = "Why is Suyool the best option for your payroll?";
        $parameters['faq'] = [
            "ONE" => [
                "Title" => "WHAT_IS_SUYOOL_DrogueriePhenicia",
                "Desc" => "SUYOOL_IS_A_CASHLESS_ECOSYSTEM_THAT_INCORPORATES_DrogueriePhenicia"
            ],
            "TWO" => [
                "Title" => "CAN_ANYONE_OPEN_A_SUYOOL_ACCOUNT_DrogueriePhenicia",
                "Desc" => "ANY_LEBANESE_CITIZEN_CAN_OPEN_A_SUYOOL_ACCOUNT_DrogueriePhenicia"
            ],
            "THREE" => [
                "Title" => "WHAT_ARE_THE_BENEFITS_FOR_USJ_EMPLOYEES_INTERFROM",
                "Desc" => "YOU_WILL_BENEFIT_FROM_A_FREE_PLATINUM_MASTERCARD_INTERFROM"
            ],
            "FOUR" => [
                "Title" => "IS_THERE_ANY_FEE_TO_GET_MY_SUYOOL_PLATINUM_MASTERCARD_DrogueriePhenicia",
                "Desc" => "YOUR_SUYOOL_MASTERCARD_WILL_BE_FREE_OF_CHARGE_ADDICTS_INTERFROM"
            ],
            "FIVE" => [
                "Title" => "WHERE_CAN_I_USE_MY_SUYOOL_PLATINUM_MASTERCARD_DrogueriePhenicia",
                "Desc" => "YOU_CAN_USE_YOUR_SUYOOL_MASTERCARD_AT_ANY_POS_DrogueriePhenicia"
            ],
            "SIX" => [
                "Title" => "WHERE_CAN_I_WITHDRAW_MY_SALARY_IN_CASH_DrogueriePhenicia_MEDCOPAYROLL",
                "Desc" => "USERS_CAN_ACCESS_THEIR_MONEY_FROM_MORE_THAN_700_DrogueriePhenicia_MEDCOPAYROLL"
            ],
        ];

        $parameters['title'] = "Interform Group SAL | Suyool";
        $parameters['desc'] = "Facing today's financial challenges, we moved our payroll to Suyool. You will get your own digital dual-currency account,a Platinum Debit Card & a payment tool with the best rates available.";

        $parameters['infoSection'] = $this->infoSection2;

        return $this->render('interform/index.html.twig', $parameters);
    }

    /**
     * @Route("/inventures", name="inventures-app")
     */
    public function inventures(Request $request, TranslatorInterface $translatorInterface): Response
    {
        $parameters = $this->trans->translation($request, $translatorInterface);
        $translatorInterface->setLocale("en");
        $parameters['lang'] = "en";
        $parameters['metaimage'] = "build/images/payroll/inventuresMeta.png";
        $parameters['descmeta'] = "Why is Suyool the best option for your payroll?";
        $parameters['faq'] = [
            "ONE" => [
                "Title" => "WHAT_IS_SUYOOL_DrogueriePhenicia",
                "Desc" => "SUYOOL_IS_A_CASHLESS_ECOSYSTEM_THAT_INCORPORATES_DrogueriePhenicia"
            ],
            "TWO" => [
                "Title" => "CAN_ANYONE_OPEN_A_SUYOOL_ACCOUNT_DrogueriePhenicia",
                "Desc" => "ANY_LEBANESE_CITIZEN_CAN_OPEN_A_SUYOOL_ACCOUNT_DrogueriePhenicia"
            ],
            "THREE" => [
                "Title" => "WHAT_ARE_THE_BENEFITS_FOR_USJ_EMPLOYEES_INVENTURES",
                "Desc" => "YOU_WILL_BENEFIT_FROM_A_FREE_PLATINUM_MASTERCARD_INVENTURES"
            ],
            "FOUR" => [
                "Title" => "IS_THERE_ANY_FEE_TO_GET_MY_SUYOOL_PLATINUM_MASTERCARD_DrogueriePhenicia",
                "Desc" => "YOUR_SUYOOL_MASTERCARD_WILL_BE_FREE_OF_CHARGE_ADDICTS_INVENTURES"
            ],
            "FIVE" => [
                "Title" => "WHERE_CAN_I_USE_MY_SUYOOL_PLATINUM_MASTERCARD_DrogueriePhenicia",
                "Desc" => "YOU_CAN_USE_YOUR_SUYOOL_MASTERCARD_AT_ANY_POS_DrogueriePhenicia"
            ],
            "SIX" => [
                "Title" => "WHERE_CAN_I_WITHDRAW_MY_SALARY_IN_CASH_DrogueriePhenicia_MEDCOPAYROLL",
                "Desc" => "USERS_CAN_ACCESS_THEIR_MONEY_FROM_MORE_THAN_700_DrogueriePhenicia_MEDCOPAYROLL"
            ],
        ];

        $parameters['title'] = "Inventures | Suyool";
        $parameters['desc'] = "Facing today's financial challenges, we moved our payroll to Suyool. You will get your own digital dual-currency account,a Platinum Debit Card & a payment tool with the best rates available.";

        $parameters['infoSection'] = $this->infoSection2;

        return $this->render('inventures/index.html.twig', $parameters);
    }

    /**
     * @Route("/ima", name="ima-app")
     */
    public function ima(Request $request, TranslatorInterface $translatorInterface): Response
    {
        $parameters = $this->trans->translation($request, $translatorInterface);
        $translatorInterface->setLocale("en");
        $parameters['lang'] = "en";
        $parameters['metaimage'] = "build/images/payroll/imaMeta.png";
        $parameters['descmeta'] = "Why is Suyool the best option for your payroll?";
        $parameters['faq'] = [
            "ONE" => [
                "Title" => "WHAT_IS_SUYOOL_DrogueriePhenicia",
                "Desc" => "SUYOOL_IS_A_CASHLESS_ECOSYSTEM_THAT_INCORPORATES_DrogueriePhenicia"
            ],
            "TWO" => [
                "Title" => "CAN_ANYONE_OPEN_A_SUYOOL_ACCOUNT_DrogueriePhenicia",
                "Desc" => "ANY_LEBANESE_CITIZEN_CAN_OPEN_A_SUYOOL_ACCOUNT_DrogueriePhenicia"
            ],
            "THREE" => [
                "Title" => "WHAT_ARE_THE_BENEFITS_FOR_USJ_EMPLOYEES_IMA",
                "Desc" => "YOU_WILL_BENEFIT_FROM_A_FREE_PLATINUM_MASTERCARD_IMA"
            ],
            "FOUR" => [
                "Title" => "IS_THERE_ANY_FEE_TO_GET_MY_SUYOOL_PLATINUM_MASTERCARD_DrogueriePhenicia",
                "Desc" => "YOUR_SUYOOL_MASTERCARD_WILL_BE_FREE_OF_CHARGE_ADDICTS_IMA"
            ],
            "FIVE" => [
                "Title" => "WHERE_CAN_I_USE_MY_SUYOOL_PLATINUM_MASTERCARD_DrogueriePhenicia",
                "Desc" => "YOU_CAN_USE_YOUR_SUYOOL_MASTERCARD_AT_ANY_POS_DrogueriePhenicia"
            ],
            "SIX" => [
                "Title" => "WHERE_CAN_I_WITHDRAW_MY_SALARY_IN_CASH_DrogueriePhenicia_MEDCOPAYROLL",
                "Desc" => "USERS_CAN_ACCESS_THEIR_MONEY_FROM_MORE_THAN_700_DrogueriePhenicia_MEDCOPAYROLL"
            ],
        ];

        $parameters['title'] = "International Maritime Academy | Suyool";
        $parameters['desc'] = "Facing today's financial challenges, we moved our payroll to Suyool. You will get your own digital dual-currency account,a Platinum Debit Card & a payment tool with the best rates available.";

        $parameters['infoSection'] = $this->infoSection2;

        return $this->render('merchants/ima/index.html.twig', $parameters);
    }

    /**
     * @Route("/pma", name="ima-pma")
     */
    public function pma(Request $request, TranslatorInterface $translatorInterface): Response
    {
        $parameters = $this->trans->translation($request, $translatorInterface);
        $translatorInterface->setLocale("en");
        $parameters['lang'] = "en";
        $parameters['metaimage'] = "build/images/payroll/pmaMeta.png";
        $parameters['descmeta'] = "Why is Suyool the best option for your payroll?";
        $parameters['faq'] = [
            "ONE" => [
                "Title" => "WHAT_IS_SUYOOL_DrogueriePhenicia",
                "Desc" => "SUYOOL_IS_A_CASHLESS_ECOSYSTEM_THAT_INCORPORATES_DrogueriePhenicia"
            ],
            "TWO" => [
                "Title" => "CAN_ANYONE_OPEN_A_SUYOOL_ACCOUNT_DrogueriePhenicia",
                "Desc" => "ANY_LEBANESE_CITIZEN_CAN_OPEN_A_SUYOOL_ACCOUNT_DrogueriePhenicia"
            ],
            "THREE" => [
                "Title" => "WHAT_ARE_THE_BENEFITS_FOR_USJ_EMPLOYEES_PMA",
                "Desc" => "YOU_WILL_BENEFIT_FROM_A_FREE_PLATINUM_MASTERCARD_PMA"
            ],
            "FOUR" => [
                "Title" => "IS_THERE_ANY_FEE_TO_GET_MY_SUYOOL_PLATINUM_MASTERCARD_DrogueriePhenicia",
                "Desc" => "YOUR_SUYOOL_MASTERCARD_WILL_BE_FREE_OF_CHARGE_ADDICTS_PMA"
            ],
            "FIVE" => [
                "Title" => "WHERE_CAN_I_USE_MY_SUYOOL_PLATINUM_MASTERCARD_DrogueriePhenicia",
                "Desc" => "YOU_CAN_USE_YOUR_SUYOOL_MASTERCARD_AT_ANY_POS_DrogueriePhenicia"
            ],
            "SIX" => [
                "Title" => "WHERE_CAN_I_WITHDRAW_MY_SALARY_IN_CASH_DrogueriePhenicia_MEDCOPAYROLL",
                "Desc" => "USERS_CAN_ACCESS_THEIR_MONEY_FROM_MORE_THAN_700_DrogueriePhenicia_MEDCOPAYROLL"
            ],
        ];

        $parameters['title'] = "Phoenician Manning Agency | Suyool";
        $parameters['desc'] = "Facing today's financial challenges, we moved our payroll to Suyool. You will get your own digital dual-currency account,a Platinum Debit Card & a payment tool with the best rates available.";

        $parameters['infoSection'] = $this->infoSection2;

        return $this->render('merchants/pma/index.html.twig', $parameters);
    }

    /**
     * @Route("/blue-base", name="blue_base_payroll")
     */
    public function blue(Request $request, TranslatorInterface $translatorInterface): Response
    {
        $parameters = $this->trans->translation($request, $translatorInterface);
        $translatorInterface->setLocale("en");
        $parameters['lang'] = "en";
        $parameters['metaimage'] = "build/images/payroll/blueMeta.png";
        $parameters['descmeta'] = "Why is Suyool the best option for your payroll?";
        $parameters['faq'] = [
            "ONE" => [
                "Title" => "WHAT_IS_SUYOOL_DrogueriePhenicia",
                "Desc" => "SUYOOL_IS_A_CASHLESS_ECOSYSTEM_THAT_INCORPORATES_DrogueriePhenicia"
            ],
            "TWO" => [
                "Title" => "CAN_ANYONE_OPEN_A_SUYOOL_ACCOUNT_DrogueriePhenicia",
                "Desc" => "ANY_LEBANESE_CITIZEN_CAN_OPEN_A_SUYOOL_ACCOUNT_DrogueriePhenicia"
            ],
            "THREE" => [
                "Title" => "WHAT_ARE_THE_BENEFITS_FOR_USJ_EMPLOYEES_BLUE",
                "Desc" => "YOU_WILL_BENEFIT_FROM_A_FREE_PLATINUM_MASTERCARD_BLUE"
            ],
            "FOUR" => [
                "Title" => "IS_THERE_ANY_FEE_TO_GET_MY_SUYOOL_PLATINUM_MASTERCARD_DrogueriePhenicia",
                "Desc" => "YOUR_SUYOOL_MASTERCARD_WILL_BE_FREE_OF_CHARGE_ADDICTS_BLUE"
            ],
            "FIVE" => [
                "Title" => "WHERE_CAN_I_USE_MY_SUYOOL_PLATINUM_MASTERCARD_DrogueriePhenicia",
                "Desc" => "YOU_CAN_USE_YOUR_SUYOOL_MASTERCARD_AT_ANY_POS_DrogueriePhenicia"
            ],
            "SIX" => [
                "Title" => "WHERE_CAN_I_WITHDRAW_MY_SALARY_IN_CASH_DrogueriePhenicia_MEDCOPAYROLL",
                "Desc" => "USERS_CAN_ACCESS_THEIR_MONEY_FROM_MORE_THAN_700_DrogueriePhenicia_MEDCOPAYROLL"
            ],
        ];

        $parameters['title'] = "Blue Base | Suyool";
        $parameters['desc'] = "Facing today's financial challenges, we moved our payroll to Suyool. You will get your own digital dual-currency account,a Platinum Debit Card & a payment tool with the best rates available.";

        $parameters['infoSection'] = $this->infoSection2;

        return $this->render('bluebase/index.html.twig', $parameters);
    }

    /**
     * @Route("/emile-rassam", name="emile_rassam_payroll")
     */
    public function emile(Request $request, TranslatorInterface $translatorInterface): Response
    {
        $parameters = $this->trans->translation($request, $translatorInterface);
        $translatorInterface->setLocale("en");
        $parameters['lang'] = "en";
        $parameters['metaimage'] = "build/images/payroll/emileMeta.png";
        $parameters['descmeta'] = "Why is Suyool the best option for your payroll?";
        $parameters['faq'] = [
            "ONE" => [
                "Title" => "WHAT_IS_SUYOOL_DrogueriePhenicia",
                "Desc" => "SUYOOL_IS_A_CASHLESS_ECOSYSTEM_THAT_INCORPORATES_DrogueriePhenicia"
            ],
            "TWO" => [
                "Title" => "CAN_ANYONE_OPEN_A_SUYOOL_ACCOUNT_DrogueriePhenicia",
                "Desc" => "ANY_LEBANESE_CITIZEN_CAN_OPEN_A_SUYOOL_ACCOUNT_DrogueriePhenicia"
            ],
            "THREE" => [
                "Title" => "WHAT_ARE_THE_BENEFITS_FOR_USJ_EMPLOYEES_EMILE",
                "Desc" => "YOU_WILL_BENEFIT_FROM_A_FREE_PLATINUM_MASTERCARD_EMILE"
            ],
            "FOUR" => [
                "Title" => "IS_THERE_ANY_FEE_TO_GET_MY_SUYOOL_PLATINUM_MASTERCARD_DrogueriePhenicia",
                "Desc" => "YOUR_SUYOOL_MASTERCARD_WILL_BE_FREE_OF_CHARGE_ADDICTS_EMILE"
            ],
            "FIVE" => [
                "Title" => "WHERE_CAN_I_USE_MY_SUYOOL_PLATINUM_MASTERCARD_DrogueriePhenicia",
                "Desc" => "YOU_CAN_USE_YOUR_SUYOOL_MASTERCARD_AT_ANY_POS_DrogueriePhenicia"
            ],
            "SIX" => [
                "Title" => "WHERE_CAN_I_WITHDRAW_MY_SALARY_IN_CASH_DrogueriePhenicia_MEDCOPAYROLL",
                "Desc" => "USERS_CAN_ACCESS_THEIR_MONEY_FROM_MORE_THAN_700_DrogueriePhenicia_MEDCOPAYROLL"
            ],
        ];

        $parameters['title'] = "Emile Rassam | Suyool";
        $parameters['desc'] = "Facing today's financial challenges, we moved our payroll to Suyool. You will get your own digital dual-currency account,a Platinum Debit Card & a payment tool with the best rates available.";

        $parameters['infoSection'] = $this->infoSection2;

        return $this->render('emilerassam/index.html.twig', $parameters);
    }
}
