<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Utils\Helper;

class InvitationCardController extends AbstractController
{

    private $hash_algo;
    private $certificate;

    public function __construct(string $hash_algo, string $certificate)
    {
        $this->hash_algo = $hash_algo;
        $this->certificate = $certificate;
    }

    /**
     * @Route("/{code}", name="invitation_card")
     */
    public function index(string $code, Request $request): Response
    {
        // Clean the code parameter
        $code = Helper::clean($code);

        // Get the language from the cookie or the session
        if ($request->cookies->has('language')) {
            $lang = $request->cookies->get('language');
        } else {
            $lang = $request->getSession()->get('LANG');
        }
        $lang = 'en';
        // Calculate the hash
        $hash = base64_encode(hash($this->hash_algo, $code . $lang . $this->certificate, true));

        // Prepare the form data
        $form_data = [
            'Code' => $code,
            'lang' => $lang,
            'Hash' => $hash,
        ];
        $params['data'] = json_encode($form_data);
        $params['url'] = 'Incentive/CardInvitationDetails';
        // Send the first curl request
        //$response = Helper::send_curl($params);
        $arr = array('RespCode' => 0, 'RespDesc' => 'Successful', 'RespTitle' => '{Name} invited you to start paying less with sKash for your daily transactions just like he is.', 'InviterName' => 'Gregorios Georgiou');
        $response = json_encode($arr);
        // Decode the response
        $invitation_card_details_response = json_decode($response, true);
        // Replace the inviter name in the response title
        $invitation_card_details_response = str_replace("{Name}", $invitation_card_details_response['InviterName'], $invitation_card_details_response['RespTitle']);
        // Add the code in the URL to validate it in the invitation page
        //$invitation_card_details_response['code'] = $code;

        // Render the template with the response data
        return $this->render('invitationCard/index.html.twig', [
            'inviterDetails' => $invitation_card_details_response,
        ]);

    }
}

