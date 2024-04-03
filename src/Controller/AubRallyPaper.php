<?php


namespace App\Controller;


use App\Utils\Helper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AubRallyPaper extends AbstractController
{

    /**
     * @Route("/aub-rally-paper", name="app_aub_rally_paper")
     */
    public function aubRallyPaper(Request $request,$success)
    {

        return $this->render('aubRallyPaper/index.html.twig');
    }

    /**
     * @Route("/{code}", name="aub_invitation")
     */
    public function index($code, Request $request): Response
    {
        $hash = base64_encode(hash($this->hash_algo, $code  . $this->certificate, true));

        $form_data = [
            'Code' => $code,
            'Hash' => $hash,
        ];
        $params['data'] = json_encode($form_data);
        $params['url'] = 'Incentive/CardInvitationDetails';
        $response = Helper::send_curl($params);
        $invitation_card_details_response = json_decode($response, true);
        $invitation_card_details_response = str_replace("{Name}", $invitation_card_details_response['InviterName'], $invitation_card_details_response['RespTitle']);

        return $this->render('aubRallyPaper/invitation.html.twig', [
            'inviterDetails' => $invitation_card_details_response,
            'code' => $code,
        ]);
    }

    /**
     * @Route("/aub-rally-paper-ranking", name="app_aub_rally_paper_ranking")
     */
    public function aubRallyPaperRanking(Request $request,$success)
    {

        return $this->render('aubRallyPaper/rank.html.twig');
    }
}