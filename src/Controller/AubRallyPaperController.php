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
      * @Route("/{code}", name="aub_invitation")
      */
     public function index($code, Request $request): Response
     {
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

         return $this->render('aubRallyPaper/invitation.html.twig', [
//             'inviterDetails' => $invitation_card_details_response,
             'code' => $code,
         ]);
     }

    /**
     * @Route("/aub-rally-paper-ranking", name="app_aub_rally_paper_ranking")
     */
    public function aubRallyPaperRanking(Request $request)
    {

        return $this->render('aubRallyPaper/rank.html.twig');
    }
}