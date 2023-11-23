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

    public function __construct(translation $trans)
    {
        $this->trans = $trans;
    }

    /**
     * @Route("/alfa-employee", name="app_alfa_employee")
     */
    public function alfa(Request $request,TranslatorInterface $translatorInterface): Response
    {
        return $this->redirectToRoute('homepage');
        $parameters = $this->trans->translation($request, $translatorInterface);
        $translatorInterface->setLocale("en");
        $parameters['lang']="en";
        $parameters['metaimage']="build/images/alfa_employee/alfametaimage.png";
        $parameters['descmeta']="Your Payroll is now on Suyool";
        $parameters['faq']=[
            "ONE"=>[
                "Title"=>"WHAT_IS_SUYOOL_ALFA",
                "Desc"=>"SUYOOL_IS_A_CASHLESS_ECOSYSTEM_THAT_INCORPORATES_ALFA"
            ],
            "TWO"=>[
                "Title"=>"CAN_ANYONE_OPEN_A_SUYOOL_ACCOUNT_ALFA",
                "Desc"=>"ANY_LEBANESE_CITIZEN_CAN_OPEN_A_SUYOOL_ACCOUNT_ALFA"
            ],
            "THREE"=>[
                "Title"=>"WHAT_ARE_THE_BENEFITS_FOR_USJ_EMPLOYEES_ALFA",
                "Desc"=>"YOU_WILL_BENEFIT_FROM_A_FREE_PLATINUM_MASTERCARD_ALFA"
            ],
            "FOUR"=>[
                "Title"=>"IS_THERE_ANY_FEE_TO_GET_MY_SUYOOL_PLATINUM_MASTERCARD_ALFA",
                "Desc"=>"YOUR_SUYOOL_MASTERCARD_WILL_BE_FREE_OF_CHARGE_ALFA"
            ],
            "FIVE"=>[
                "Title"=>"WHERE_CAN_I_USE_MY_SUYOOL_PLATINUM_MASTERCARD_ALFA",
                "Desc"=>"YOU_CAN_USE_YOUR_SUYOOL_MASTERCARD_AT_ANY_POS_ALFA"
            ],
            "SIX"=>[
                "Title"=>"WHERE_CAN_I_WITHDRAW_MY_SALARY_IN_CASH_ALFA",
                "Desc"=>"USERS_CAN_ACCESS_THEIR_MONEY_FROM_MORE_THAN_700_ALFA"
            ],
        ];

        $parameters['title']="Alfa Employee | Suyool";
        $parameters['desc']="Facing today’s financial challenges, we moved our payroll to Suyool. You will get your own digital dual-currency account,a Platinum Debit Card & a payment tool with the best rates available.";
        $infoSection = [
            'title' => '6 Ways To Use Your Money',
            'items' => [
                [
                    'image' => 'build/images/alfa_employee/card1.svg',
                    'description' => 'Free Platinum Debit Card',
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
        $parameters['infoSection']= $infoSection;

        return $this->render('merchants/alfa.html.twig',$parameters);
    }

    /**
     * @Route("/usj", name="app_usj")
     */
    public function usj(Request $request,TranslatorInterface $translatorInterface): Response
    {
        $parameters = $this->trans->translation($request, $translatorInterface);
//        $translatorInterface->setLocale("en");

        if($parameters['lang'] == "en"){
            $parameters['metaimage']="build/images/usj/metaenglish.png";
            $parameters['descmeta']="Your Payroll is now on Suyool";
        }else if($parameters['lang'] == "fr"){
            $parameters['metaimage']="build/images/usj/metafr.png";
            $parameters['descmeta']="Votre salaire est désormais sur Suyool";
        }else{
            $parameters['metaimage']="build/images/usj/metaarabic.png";
            $parameters['descmeta']="الأن راتبك على سيول!";
        }
        $parameters['faq']=[
            "ONE"=>[
                "Title"=>"WHAT_IS_SUYOOL",
                "Desc"=>"SUYOOL_IS_A_CASHLESS_ECOSYSTEM_THAT_INCORPORATES"
            ],
            "TWO"=>[
                "Title"=>"CAN_ANYONE_OPEN_A_SUYOOL_ACCOUNT",
                "Desc"=>"ANY_LEBANESE_CITIZEN_CAN_OPEN_A_SUYOOL_ACCOUNT"
            ],
            "THREE"=>[
                "Title"=>"WHAT_ARE_THE_BENEFITS_FOR_USJ_EMPLOYEES",
                "Desc"=>"YOU_WILL_BENEFIT_FROM_A_FREE_PLATINUM_MASTERCARD"
            ],
            "FOUR"=>[
                "Title"=>"IS_THERE_ANY_FEE_TO_GET_MY_SUYOOL_PLATINUM_MASTERCARD",
                "Desc"=>"YOUR_SUYOOL_MASTERCARD_WILL_BE_FREE_OF_CHARGE"
            ],
            "FIVE"=>[
                "Title"=>"WHERE_CAN_I_USE_MY_SUYOOL_PLATINUM_MASTERCARD",
                "Desc"=>"YOU_CAN_USE_YOUR_SUYOOL_MASTERCARD_AT_ANY_POS"
            ],
            "SIX"=>[
                "Title"=>"WHERE_CAN_I_WITHDRAW_MY_SALARY_IN_CASH",
                "Desc"=>"USERS_CAN_ACCESS_THEIR_MONEY_FROM_MORE_THAN_700"
            ],
        ];

        $parameters['title']="USJ | Suyool";
        $parameters['desc']="Facing today’s financial challenges, we moved our payroll to Suyool.
        You will get your own digital dual-currency account, a complete payment tool
        with the best rates and a Platinum Debit Card linked to the account.";
        return $this->render('merchants/usj.html.twig',$parameters);
    }

    /**
     * @Route("/elnashra", name="app_elnashra")
     */
    public function elnashra(Request $request,TranslatorInterface $translatorInterface): Response
    {

        $parameters = $this->trans->translation($request, $translatorInterface);
        $translatorInterface->setLocale("en");
        $parameters['faq']=[
            "ONE"=>[
                "Title"=>"WHAT_IS_SUYOOL",
                "Desc"=>"SUYOOL_IS_A_CASHLESS_ECOSYSTEM_THAT_INCORPORATES"
            ],
            "TWO"=>[
                "Title"=>"CAN_ANYONE_OPEN_A_SUYOOL_ACCOUNT",
                "Desc"=>"ANY_LEBANESE_CITIZEN_CAN_OPEN_A_SUYOOL_ACCOUNT"
            ],
            "THREE"=>[
                "Title"=>"WHAT_ARE_THE_BENEFITS_FOR_ELNASHRA_EMPLOYEES",
                "Desc"=>"YOU_WILL_BENEFIT_FROM_A_FREE_PLATINUM_MASTERCARD_NASHRA"
            ],
            "FOUR"=>[
                "Title"=>"IS_THERE_ANY_FEE_TO_GET_MY_SUYOOL_PLATINUM_MASTERCARD",
                "Desc"=>"YOUR_SUYOOL_MASTERCARD_WILL_BE_FREE_OF_CHARGE_NASHRA"
            ],
            "FIVE"=>[
                "Title"=>"WHERE_CAN_I_USE_MY_SUYOOL_PLATINUM_MASTERCARD",
                "Desc"=>"YOU_CAN_USE_YOUR_SUYOOL_MASTERCARD_AT_ANY_POS"
            ],
            "SIX"=>[
                "Title"=>"WHERE_CAN_I_WITHDRAW_MY_SALARY_IN_CASH",
                "Desc"=>"USERS_CAN_ACCESS_THEIR_MONEY_FROM_MORE_THAN_700"
            ],
        ];

        $parameters['title']="elnashra | Suyool";
        $parameters['desc']="Facing today’s financial challenges, we moved our payroll to Suyool.
        You will get your own digital dual-currency account, a complete payment tool
        with the best rates and a Platinum Debit Card linked to the account.";
        return $this->render('merchants/elnashra.html.twig',$parameters);
    }

    /**
     * @Route("/lldj-employee", name="app_lldj_employee")
     */
    public function lldj(Request $request,TranslatorInterface $translatorInterface): Response
    {
        $parameters = $this->trans->translation($request, $translatorInterface);
        $translatorInterface->setLocale("en");
        $parameters['lang']="en";
        $parameters['metaimage']="build/images/lldj/metaLLDJ.jpg";
        $parameters['descmeta']="Your Payroll is now on Suyool";
        $parameters['faq']=[
            "ONE"=>[
                "Title"=>"WHAT_IS_SUYOOL_LLDJ",
                "Desc"=>"SUYOOL_IS_A_CASHLESS_ECOSYSTEM_THAT_INCORPORATES_LLDJ"
            ],
            "TWO"=>[
                "Title"=>"CAN_ANYONE_OPEN_A_SUYOOL_ACCOUNT_LLDJ",
                "Desc"=>"ANY_LEBANESE_CITIZEN_CAN_OPEN_A_SUYOOL_ACCOUNT_LLDJ"
            ],
            "THREE"=>[
                "Title"=>"WHAT_ARE_THE_BENEFITS_FOR_USJ_EMPLOYEES_LLDJ",
                "Desc"=>"YOU_WILL_BENEFIT_FROM_A_FREE_PLATINUM_MASTERCARD_LLDJ"
            ],
            "FOUR"=>[
                "Title"=>"IS_THERE_ANY_FEE_TO_GET_MY_SUYOOL_PLATINUM_MASTERCARD_LLDJ",
                "Desc"=>"YOUR_SUYOOL_MASTERCARD_WILL_BE_FREE_OF_CHARGE_LLDJ"
            ],
            "FIVE"=>[
                "Title"=>"WHERE_CAN_I_USE_MY_SUYOOL_PLATINUM_MASTERCARD_LLDJ",
                "Desc"=>"YOU_CAN_USE_YOUR_SUYOOL_MASTERCARD_AT_ANY_POS_LLDJ"
            ],
            "SIX"=>[
                "Title"=>"WHERE_CAN_I_WITHDRAW_MY_SALARY_IN_CASH_LLDJ",
                "Desc"=>"USERS_CAN_ACCESS_THEIR_MONEY_FROM_MORE_THAN_700_LLDJ"
            ],
        ];

        $parameters['title']="LLDJ Employee | Suyool";
        $parameters['desc']="Facing today’s financial challenges, we moved our payroll to Suyool. You will get your own digital dual-currency account,a Platinum Debit Card & a payment tool with the best rates available.";
        $infoSection = [
            'title' => '6 Ways To Use Your Money',
            'items' => [
                [
                    'image' => 'build/images/alfa_employee/card1.svg',
                    'description' => 'Free Platinum Debit Card',
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
        $parameters['infoSection']= $infoSection;

        return $this->render('merchants/lldj.html.twig',$parameters);
    }

    /**
     * @Route("/aramex", name="app_aramex")
     */
    public function aramex(Request $request,TranslatorInterface $translatorInterface): Response
    {
        $parameters = $this->trans->translation($request, $translatorInterface);
        $translatorInterface->setLocale("en");
        $parameters['lang']="en";
        $parameters['metaimage']="build/images/aramex/metaAramex.png";
        $parameters['descmeta']="Your Payroll is now on Suyool";
        $parameters['faq']=[
            "ONE"=>[
                "Title"=>"WHAT_IS_SUYOOL_ARAMEX",
                "Desc"=>"SUYOOL_IS_A_CASHLESS_ECOSYSTEM_THAT_INCORPORATES_ARAMEX"
            ],
            "TWO"=>[
                "Title"=>"CAN_ANYONE_OPEN_A_SUYOOL_ACCOUNT_ARAMEX",
                "Desc"=>"ANY_LEBANESE_CITIZEN_CAN_OPEN_A_SUYOOL_ACCOUNT_ARAMEX"
            ],
            "THREE"=>[
                "Title"=>"WHAT_ARE_THE_BENEFITS_FOR_USJ_EMPLOYEES_ARAMEX",
                "Desc"=>"YOU_WILL_BENEFIT_FROM_A_FREE_PLATINUM_MASTERCARD_ARAMEX"
            ],
            "FOUR"=>[
                "Title"=>"IS_THERE_ANY_FEE_TO_GET_MY_SUYOOL_PLATINUM_MASTERCARD_ARAMEX",
                "Desc"=>"YOUR_SUYOOL_MASTERCARD_WILL_BE_FREE_OF_CHARGE_ARAMEX"
            ],
            "FIVE"=>[
                "Title"=>"WHERE_CAN_I_USE_MY_SUYOOL_PLATINUM_MASTERCARD_ARAMEX",
                "Desc"=>"YOU_CAN_USE_YOUR_SUYOOL_MASTERCARD_AT_ANY_POS_ARAMEX"
            ],
            "SIX"=>[
                "Title"=>"WHERE_CAN_I_WITHDRAW_MY_SALARY_IN_CASH_ARAMEX",
                "Desc"=>"USERS_CAN_ACCESS_THEIR_MONEY_FROM_MORE_THAN_700_ARAMEX"
            ],
        ];

        $parameters['title']="Aramex | Suyool";
        $parameters['desc']="Facing today’s financial challenges, we moved our payroll to Suyool. You will get your own digital dual-currency account,a Platinum Debit Card & a payment tool with the best rates available.";
        $infoSection = [
            'title' => '6 Ways To Use Your Money',
            'items' => [
                [
                    'image' => 'build/images/alfa_employee/card1.svg',
                    'description' => 'Free Platinum Debit Card',
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
        $parameters['infoSection']= $infoSection;

        return $this->render('merchants/aramex.html.twig',$parameters);
    }

    /**
     * @Route("/emood", name="emood")
     */
    public function emood(Request $request,TranslatorInterface $translatorInterface): Response
    {
        $parameters = $this->trans->translation($request, $translatorInterface);
        $translatorInterface->setLocale("en");
        $parameters['lang']="en";
        $parameters['metaimage']="build/images/emood/emood-meta.jpg";
        $parameters['descmeta']="Your Payroll is now on Suyool";
        $parameters['faq']=[
            "ONE"=>[
                "Title"=>"WHAT_IS_SUYOOL_LLDJ",
                "Desc"=>"SUYOOL_IS_A_CASHLESS_ECOSYSTEM_THAT_INCORPORATES_LLDJ"
            ],
            "TWO"=>[
                "Title"=>"CAN_ANYONE_OPEN_A_SUYOOL_ACCOUNT_LLDJ",
                "Desc"=>"ANY_LEBANESE_CITIZEN_CAN_OPEN_A_SUYOOL_ACCOUNT_LLDJ"
            ],
            "THREE"=>[
                "Title"=>"WHAT_ARE_THE_BENEFITS_FOR_USJ_EMPLOYEES_EMOOD",
                "Desc"=>"YOU_WILL_BENEFIT_FROM_A_FREE_PLATINUM_MASTERCARD_EMOOD"
            ],
            "FOUR"=>[
                "Title"=>"IS_THERE_ANY_FEE_TO_GET_MY_SUYOOL_PLATINUM_MASTERCARD_LLDJ",
                "Desc"=>"YOUR_SUYOOL_MASTERCARD_WILL_BE_FREE_OF_CHARGE_EMOOD"
            ],
            "FIVE"=>[
                "Title"=>"WHERE_CAN_I_USE_MY_SUYOOL_PLATINUM_MASTERCARD_LLDJ",
                "Desc"=>"YOU_CAN_USE_YOUR_SUYOOL_MASTERCARD_AT_ANY_POS_LLDJ"
            ],
            "SIX"=>[
                "Title"=>"WHERE_CAN_I_WITHDRAW_MY_SALARY_IN_CASH_LLDJ",
                "Desc"=>"USERS_CAN_ACCESS_THEIR_MONEY_FROM_MORE_THAN_700_LLDJ"
            ],
        ];

        $parameters['title']="E-mood Employee | Suyool";
        $parameters['desc']="Facing today’s financial challenges, we moved our payroll to Suyool. You will get your own digital dual-currency account,a Platinum Debit Card & a payment tool with the best rates available.";
        $infoSection = [
            'title' => '6 Ways To Use Your Money',
            'items' => [
                [
                    'image' => 'build/images/alfa_employee/card1.svg',
                    'description' => 'Free Platinum Debit Card',
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
        $parameters['infoSection']= $infoSection;

        return $this->render('merchants/emood.html.twig',$parameters);
    }

    /**
     * @Route("/web-addicts", name="web_addicts")
     */
    public function webAddicts(Request $request,TranslatorInterface $translatorInterface): Response
    {
        $parameters = $this->trans->translation($request, $translatorInterface);
        $translatorInterface->setLocale("en");
        $parameters['lang']="en";
        $parameters['metaimage']="build/images/web-addicts/web-meta.jpg";
        $parameters['descmeta']="Your Payroll is now on Suyool";
        $parameters['faq']=[
            "ONE"=>[
                "Title"=>"WHAT_IS_SUYOOL_LLDJ",
                "Desc"=>"SUYOOL_IS_A_CASHLESS_ECOSYSTEM_THAT_INCORPORATES_LLDJ"
            ],
            "TWO"=>[
                "Title"=>"CAN_ANYONE_OPEN_A_SUYOOL_ACCOUNT_LLDJ",
                "Desc"=>"ANY_LEBANESE_CITIZEN_CAN_OPEN_A_SUYOOL_ACCOUNT_LLDJ"
            ],
            "THREE"=>[
                "Title"=>"WHAT_ARE_THE_BENEFITS_FOR_USJ_EMPLOYEES_ADDICTS",
                "Desc"=>"YOU_WILL_BENEFIT_FROM_A_FREE_PLATINUM_MASTERCARD_ADDICTS"
            ],
            "FOUR"=>[
                "Title"=>"IS_THERE_ANY_FEE_TO_GET_MY_SUYOOL_PLATINUM_MASTERCARD_LLDJ",
                "Desc"=>"YOUR_SUYOOL_MASTERCARD_WILL_BE_FREE_OF_CHARGE_ADDICTS"
            ],
            "FIVE"=>[
                "Title"=>"WHERE_CAN_I_USE_MY_SUYOOL_PLATINUM_MASTERCARD_LLDJ",
                "Desc"=>"YOU_CAN_USE_YOUR_SUYOOL_MASTERCARD_AT_ANY_POS_LLDJ"
            ],
            "SIX"=>[
                "Title"=>"WHERE_CAN_I_WITHDRAW_MY_SALARY_IN_CASH_LLDJ",
                "Desc"=>"USERS_CAN_ACCESS_THEIR_MONEY_FROM_MORE_THAN_700_LLDJ"
            ],
        ];

        $parameters['title']="The Web Addicts | Suyool";
        $parameters['desc']="Facing today’s financial challenges, we moved our payroll to Suyool. You will get your own digital dual-currency account,a Platinum Debit Card & a payment tool with the best rates available.";
        $infoSection = [
            'title' => '6 Ways To Use Your Money',
            'items' => [
                [
                    'image' => 'build/images/alfa_employee/card1.svg',
                    'description' => 'Free Platinum Debit Card',
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
        $parameters['infoSection']= $infoSection;

        return $this->render('merchants/web-addicts.html.twig',$parameters);
    }

    /**
     * @Route("/Phenicia", name="droguerie-Phenicia")
     */
    public function phenicia(Request $request,TranslatorInterface $translatorInterface): Response
    {
        $parameters = $this->trans->translation($request, $translatorInterface);
        $translatorInterface->setLocale("en");
        $parameters['lang']="en";
        $parameters['metaimage']="build/images/droguerie/metaPhar.png";
        $parameters['descmeta']="Your Payroll is now on Suyool";
        $parameters['faq']=[
            "ONE"=>[
                "Title"=>"WHAT_IS_SUYOOL_DrogueriePhenicia",
                "Desc"=>"SUYOOL_IS_A_CASHLESS_ECOSYSTEM_THAT_INCORPORATES_DrogueriePhenicia"
            ],
            "TWO"=>[
                "Title"=>"CAN_ANYONE_OPEN_A_SUYOOL_ACCOUNT_DrogueriePhenicia",
                "Desc"=>"ANY_LEBANESE_CITIZEN_CAN_OPEN_A_SUYOOL_ACCOUNT_DrogueriePhenicia"
            ],
            "THREE"=>[
                "Title"=>"WHAT_ARE_THE_BENEFITS_FOR_USJ_EMPLOYEES_DrogueriePhenicia",
                "Desc"=>"YOU_WILL_BENEFIT_FROM_A_FREE_PLATINUM_MASTERCARD_DrogueriePhenicia"
            ],
            "FOUR"=>[
                "Title"=>"IS_THERE_ANY_FEE_TO_GET_MY_SUYOOL_PLATINUM_MASTERCARD_DrogueriePhenicia",
                "Desc"=>"YOUR_SUYOOL_MASTERCARD_WILL_BE_FREE_OF_CHARGE_ADDICTS_DrogueriePhenicia"
            ],
            "FIVE"=>[
                "Title"=>"WHERE_CAN_I_USE_MY_SUYOOL_PLATINUM_MASTERCARD_DrogueriePhenicia",
                "Desc"=>"YOU_CAN_USE_YOUR_SUYOOL_MASTERCARD_AT_ANY_POS_DrogueriePhenicia"
            ],
            "SIX"=>[
                "Title"=>"WHERE_CAN_I_WITHDRAW_MY_SALARY_IN_CASH_DrogueriePhenicia",
                "Desc"=>"USERS_CAN_ACCESS_THEIR_MONEY_FROM_MORE_THAN_700_DrogueriePhenicia"
            ],
        ];

        $parameters['title']="Droguerie Phenicia | Suyool";
        $parameters['desc']="Facing today’s financial challenges, we moved our payroll to Suyool. You will get your own digital dual-currency account,a Platinum Debit Card & a payment tool with the best rates available.";
        $infoSection = [
            'title' => '6 Ways To Use Your Money',
            'items' => [
                [
                    'image' => 'build/images/alfa_employee/card1.svg',
                    'description' => 'Free Platinum Debit Card',
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
        $parameters['infoSection']= $infoSection;

        return $this->render('merchants/droguerie-Phenicia.html.twig',$parameters);
    }

    /**
     * @Route("/medco", name="medco")
     */
    public function medco(Request $request,TranslatorInterface $translatorInterface): Response
    {
        $parameters = $this->trans->translation($request, $translatorInterface);
        $translatorInterface->setLocale("en");
        $parameters['lang']="en";
        $parameters['metaimage']="build/images/medco/medcoMeta.png";
        $parameters['descmeta']="Your Payroll is now on Suyool";
        $parameters['faq']=[
            "ONE"=>[
                "Title"=>"WHAT_IS_SUYOOL_DrogueriePhenicia",
                "Desc"=>"SUYOOL_IS_A_CASHLESS_ECOSYSTEM_THAT_INCORPORATES_DrogueriePhenicia"
            ],
            "TWO"=>[
                "Title"=>"CAN_ANYONE_OPEN_A_SUYOOL_ACCOUNT_DrogueriePhenicia",
                "Desc"=>"ANY_LEBANESE_CITIZEN_CAN_OPEN_A_SUYOOL_ACCOUNT_DrogueriePhenicia"
            ],
            "THREE"=>[
                "Title"=>"WHAT_ARE_THE_BENEFITS_FOR_USJ_EMPLOYEES_MEDCO",
                "Desc"=>"YOU_WILL_BENEFIT_FROM_A_FREE_PLATINUM_MASTERCARD_MEDCO"
            ],
            "FOUR"=>[
                "Title"=>"IS_THERE_ANY_FEE_TO_GET_MY_SUYOOL_PLATINUM_MASTERCARD_DrogueriePhenicia",
                "Desc"=>"YOUR_SUYOOL_MASTERCARD_WILL_BE_FREE_OF_CHARGE_ADDICTS_MEDCO"
            ],
            "FIVE"=>[
                "Title"=>"WHERE_CAN_I_USE_MY_SUYOOL_PLATINUM_MASTERCARD_DrogueriePhenicia",
                "Desc"=>"YOU_CAN_USE_YOUR_SUYOOL_MASTERCARD_AT_ANY_POS_DrogueriePhenicia"
            ],
            "SIX"=>[
                "Title"=>"WHERE_CAN_I_WITHDRAW_MY_SALARY_IN_CASH_DrogueriePhenicia_MEDCO",
                "Desc"=>"USERS_CAN_ACCESS_THEIR_MONEY_FROM_MORE_THAN_700_DrogueriePhenicia_MEDCO"
            ],
        ];

        $parameters['title']="Medco | Suyool";
        $parameters['desc']="Facing today’s financial challenges, we moved our payroll to Suyool. You will get your own digital dual-currency account,a Platinum Debit Card & a payment tool with the best rates available.";
        $infoSection = [
            'title' => '6 Ways To Use Your Money',
            'items' => [
                [
                    'image' => 'build/images/alfa_employee/card1.svg',
                    'description' => 'Free Platinum Debit Card',
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
        $parameters['infoSection']= $infoSection;

        return $this->render('merchants/medco.html.twig',$parameters);
    }

    // /**
    //  * @Route("/group-kallasi", name="kallasi")
    //  */
    public function kallasi(Request $request,TranslatorInterface $translatorInterface): Response
    {
        $parameters = $this->trans->translation($request, $translatorInterface);
        $translatorInterface->setLocale("en");
        $parameters['lang']="en";
        $parameters['metaimage']="build/images/kallasi/kallasiMeta.png";
        $parameters['descmeta']="Your Payroll is now on Suyool";
        $parameters['faq']=[
            "ONE"=>[
                "Title"=>"WHAT_IS_SUYOOL_DrogueriePhenicia",
                "Desc"=>"SUYOOL_IS_A_CASHLESS_ECOSYSTEM_THAT_INCORPORATES_DrogueriePhenicia"
            ],
            "TWO"=>[
                "Title"=>"CAN_ANYONE_OPEN_A_SUYOOL_ACCOUNT_DrogueriePhenicia",
                "Desc"=>"ANY_LEBANESE_CITIZEN_CAN_OPEN_A_SUYOOL_ACCOUNT_DrogueriePhenicia"
            ],
            "THREE"=>[
                "Title"=>"WHAT_ARE_THE_BENEFITS_FOR_USJ_EMPLOYEES_KALLASI",
                "Desc"=>"YOU_WILL_BENEFIT_FROM_A_FREE_PLATINUM_MASTERCARD_KALLASI"
            ],
            "FOUR"=>[
                "Title"=>"IS_THERE_ANY_FEE_TO_GET_MY_SUYOOL_PLATINUM_MASTERCARD_DrogueriePhenicia",
                "Desc"=>"YOUR_SUYOOL_MASTERCARD_WILL_BE_FREE_OF_CHARGE_ADDICTS_KALLASI"
            ],
            "FIVE"=>[
                "Title"=>"WHERE_CAN_I_USE_MY_SUYOOL_PLATINUM_MASTERCARD_DrogueriePhenicia",
                "Desc"=>"YOU_CAN_USE_YOUR_SUYOOL_MASTERCARD_AT_ANY_POS_DrogueriePhenicia"
            ],
            "SIX"=>[
                "Title"=>"WHERE_CAN_I_WITHDRAW_MY_SALARY_IN_CASH_DrogueriePhenicia",
                "Desc"=>"USERS_CAN_ACCESS_THEIR_MONEY_FROM_MORE_THAN_700_DrogueriePhenicia"
            ],
        ];

        $parameters['title']="Group ka | Suyool";
        $parameters['desc']="Facing today’s financial challenges, we moved our payroll to Suyool. You will get your own digital dual-currency account,a Platinum Debit Card & a payment tool with the best rates available.";
        $infoSection = [
            'title' => '6 Ways To Use Your Money',
            'items' => [
                [
                    'image' => 'build/images/alfa_employee/card1.svg',
                    'description' => 'Free Platinum Debit Card',
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
        $parameters['infoSection']= $infoSection;

        return $this->render('merchants/kallasi.html.twig',$parameters);
    }

    /**
     * @Route("/laser-vision", name="laservision")
     */
    public function laser(Request $request,TranslatorInterface $translatorInterface): Response
    {
        $parameters = $this->trans->translation($request, $translatorInterface);
        $translatorInterface->setLocale("en");
        $parameters['lang']="en";
        $parameters['metaimage']="build/images/laser/laserMeta.png";
        $parameters['descmeta']="Your Payroll is now on Suyool";
        $parameters['faq']=[
            "ONE"=>[
                "Title"=>"WHAT_IS_SUYOOL_DrogueriePhenicia",
                "Desc"=>"SUYOOL_IS_A_CASHLESS_ECOSYSTEM_THAT_INCORPORATES_DrogueriePhenicia"
            ],
            "TWO"=>[
                "Title"=>"CAN_ANYONE_OPEN_A_SUYOOL_ACCOUNT_DrogueriePhenicia",
                "Desc"=>"ANY_LEBANESE_CITIZEN_CAN_OPEN_A_SUYOOL_ACCOUNT_DrogueriePhenicia"
            ],
            "THREE"=>[
                "Title"=>"WHAT_ARE_THE_BENEFITS_FOR_USJ_EMPLOYEES_LASER",
                "Desc"=>"YOU_WILL_BENEFIT_FROM_A_FREE_PLATINUM_MASTERCARD_LASER"
            ],
            "FOUR"=>[
                "Title"=>"IS_THERE_ANY_FEE_TO_GET_MY_SUYOOL_PLATINUM_MASTERCARD_DrogueriePhenicia",
                "Desc"=>"YOUR_SUYOOL_MASTERCARD_WILL_BE_FREE_OF_CHARGE_ADDICTS_LASER"
            ],
            "FIVE"=>[
                "Title"=>"WHERE_CAN_I_USE_MY_SUYOOL_PLATINUM_MASTERCARD_DrogueriePhenicia",
                "Desc"=>"YOU_CAN_USE_YOUR_SUYOOL_MASTERCARD_AT_ANY_POS_DrogueriePhenicia"
            ],
            "SIX"=>[
                "Title"=>"WHERE_CAN_I_WITHDRAW_MY_SALARY_IN_CASH_DrogueriePhenicia",
                "Desc"=>"USERS_CAN_ACCESS_THEIR_MONEY_FROM_MORE_THAN_700_DrogueriePhenicia"
            ],
        ];

        $parameters['title']="Laser vision | Suyool";
        $parameters['desc']="Facing today’s financial challenges, we moved our payroll to Suyool. You will get your own digital dual-currency account,a Platinum Debit Card & a payment tool with the best rates available.";
        $infoSection = [
            'title' => '6 Ways To Use Your Money',
            'items' => [
                [
                    'image' => 'build/images/alfa_employee/card1.svg',
                    'description' => 'Free Platinum Debit Card',
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
        $parameters['infoSection']= $infoSection;

        return $this->render('merchants/laser.html.twig',$parameters);
    }

    /**
     * @Route("/editec", name="editec")
     */
    public function editec(Request $request,TranslatorInterface $translatorInterface): Response
    {
        $parameters = $this->trans->translation($request, $translatorInterface);
        $translatorInterface->setLocale("en");
        $parameters['lang']="en";
        $parameters['metaimage']="build/images/editec/editecMeta.png";
        $parameters['descmeta']="Your Payroll is now on Suyool";
        $parameters['faq']=[
            "ONE"=>[
                "Title"=>"WHAT_IS_SUYOOL_DrogueriePhenicia",
                "Desc"=>"SUYOOL_IS_A_CASHLESS_ECOSYSTEM_THAT_INCORPORATES_DrogueriePhenicia"
            ],
            "TWO"=>[
                "Title"=>"CAN_ANYONE_OPEN_A_SUYOOL_ACCOUNT_DrogueriePhenicia",
                "Desc"=>"ANY_LEBANESE_CITIZEN_CAN_OPEN_A_SUYOOL_ACCOUNT_DrogueriePhenicia"
            ],
            "THREE"=>[
                "Title"=>"WHAT_ARE_THE_BENEFITS_FOR_USJ_EMPLOYEES_EDITEC",
                "Desc"=>"YOU_WILL_BENEFIT_FROM_A_FREE_PLATINUM_MASTERCARD_EDITEC"
            ],
            "FOUR"=>[
                "Title"=>"IS_THERE_ANY_FEE_TO_GET_MY_SUYOOL_PLATINUM_MASTERCARD_DrogueriePhenicia",
                "Desc"=>"YOUR_SUYOOL_MASTERCARD_WILL_BE_FREE_OF_CHARGE_ADDICTS_EDITEC"
            ],
            "FIVE"=>[
                "Title"=>"WHERE_CAN_I_USE_MY_SUYOOL_PLATINUM_MASTERCARD_DrogueriePhenicia",
                "Desc"=>"YOU_CAN_USE_YOUR_SUYOOL_MASTERCARD_AT_ANY_POS_DrogueriePhenicia"
            ],
            "SIX"=>[
                "Title"=>"WHERE_CAN_I_WITHDRAW_MY_SALARY_IN_CASH_DrogueriePhenicia",
                "Desc"=>"USERS_CAN_ACCESS_THEIR_MONEY_FROM_MORE_THAN_700_DrogueriePhenicia"
            ],
        ];

        $parameters['title']="Editec | Suyool";
        $parameters['desc']="Facing today’s financial challenges, we moved our payroll to Suyool. You will get your own digital dual-currency account,a Platinum Debit Card & a payment tool with the best rates available.";
        $infoSection = [
            'title' => '6 Ways To Use Your Money',
            'items' => [
                [
                    'image' => 'build/images/alfa_employee/card1.svg',
                    'description' => 'Free Platinum Debit Card',
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
        $parameters['infoSection']= $infoSection;

        return $this->render('merchants/editec.html.twig',$parameters);
    }
}
