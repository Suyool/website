<?php


namespace App\Controller;


use App\Utils\Helper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class AubRallyPaperController extends AbstractController
{
    private $hash_algo;
    private $certificate;
    private $helper;
    private $client;


    public function __construct()
    {
        $this->hash_algo = $_ENV['ALGO'];
        $this->certificate = $_ENV['CERTIFICATE'];
        $this->client = HttpClient::create();
        $this->helper = new Helper($this->client);

    }

    /**
     * @Route("/aub-rally-paper", name="app_aub_rally_paper")
     */
    public function aubRallyPaper(Request $request)
    {

        return $this->render('aubRallyPaper/index.html.twig');
    }

     /**
      * @Route("/aubInvitation", name="aub_invitation")
      */
     public function aubInvitation(Request $request): Response
     {
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
//          $hash = base64_encode(hash($this->hash_algo, $code  . $this->certificate, true));
//
//         $form_data = [
//             'Code' => $code,
//             'Hash' => $hash,
//         ];
//
//         $response = $this->helper->clientRequest($this->METHOD_POST, "{$this->SUYOOL_API_HOST}Utilities/PushUtilityPayment",  $form_data);

//         $invitation_card_details_response = json_decode($response, true);
//         $invitation_card_details_response = str_replace("{Name}", $invitation_card_details_response['InviterName'], $invitation_card_details_response['RespTitle']);


         return $this->render('aubRallyPaper/invitation.html.twig', $parameters);

     }

    /**
     * @Route("/aub-rally-paper-ranking", name="app_aub_rally_paper_ranking")
     */
    public function aubRallyPaperRanking(Request $request)
    {

        return $this->render('aubRallyPaper/rank.html.twig');
    }
}