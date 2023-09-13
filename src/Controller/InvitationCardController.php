<?php

namespace App\Controller;

use App\Translation\translation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Utils\Helper;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Contracts\Translation\TranslatorInterface;

class InvitationCardController extends AbstractController
{

    private $hash_algo;
    private $certificate;
    private $trans;

    public function __construct(string $hash_algo, string $certificate, translation $trans)
    {
        $this->hash_algo = $hash_algo;
        $this->certificate = $certificate;
        $this->trans = $trans;
    }

    /**
     * @Route("/{code}", name="invitation_card")
     */
    public function index($code, Request $request, TranslatorInterface $translator): Response
    {
        $parameters = $this->trans->translation($request, $translator);
        $hash = base64_encode(hash($this->hash_algo, $code . $parameters['lang'] . $this->certificate, true));

        $form_data = [
            'Code' => $code,
            'lang' => $parameters['lang'],
            'Hash' => $hash,
        ];
        $params['data'] = json_encode($form_data);
        $params['url'] = 'Incentive/CardInvitationDetails';
        $response = Helper::send_curl($params);
        $invitation_card_details_response = json_decode($response, true);
        $invitation_card_details_response = str_replace("{Name}", $invitation_card_details_response['InviterName'], $invitation_card_details_response['RespTitle']);

        return $this->render('invitationCard/index.html.twig', [
            'inviterDetails' => $invitation_card_details_response,
            'code' => $code,
        ]);
    }

    /**
     * @Route("/invitationCard/submitInvitationCard", name="submit_mobile", methods={"POST"})
     */
    public function submitMobile(Request $request)
    {
        $mobile = $request->get('mobileNumber');
        $code = $request->get('code');
        $lang = 'en';
        $dateSent = date("ymdHis");

        $Hash = base64_encode(hash($this->hash_algo, $code . $mobile . $lang . $dateSent . $this->certificate, true));
        $form_data = [
            'Code' => $code,
            "MobileNo" => $mobile,
            "lang" => $lang,
            "DateSent" => $dateSent,
            'Hash' => $Hash,
        ];
        $params['data'] = json_encode($form_data);
        $params['url'] = 'Incentive/CardContact';
        $response = Helper::send_curl($params);
        $result = json_decode($response, true);
        $result['success'] = false;

        if ($result['RespCode'] == 0) {
            $result['success'] = true;
        }

        return $this->json($result);
    }
}
