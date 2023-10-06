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
        $parameters['desc']="Facing today’s financial challenges, we moved our payroll to Suyool. You will get your own digital dual-currency account,a Platinum Mastercard & a payment tool with the best rates available.";
        $infoSection = [
            'title' => '6 Ways To Use Your Money',
            'items' => [
                [
                    'image' => 'build/images/alfa_employee/card1.svg',
                    'description' => 'Free Platinum MasterCard',
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
                    'image' => 'build/images/alfa_employee/internationally.svg',
                    'description' => 'Send Money Internationally',
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
        with the best rates and a Platinum Mastercard linked to the account.";
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
        with the best rates and a Platinum Mastercard linked to the account.";
        return $this->render('merchants/elnashra.html.twig',$parameters);
    }
}
