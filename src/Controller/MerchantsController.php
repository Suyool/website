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
        return $this->render('merchants/alfa.html.twig',$parameters);
    }

    /**
     * @Route("/usj", name="app_usj")
     */
    public function usj(Request $request,TranslatorInterface $translatorInterface): Response
    {
        $parameters = $this->trans->translation($request, $translatorInterface);
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
        return $this->render('merchants/usj.html.twig',$parameters);
    }
}
