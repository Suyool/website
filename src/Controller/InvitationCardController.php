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

    public function __construct(string $hash_algo, string $certificate,translation $trans)
    {
        $this->hash_algo = $hash_algo;
        $this->certificate = $certificate;
        $this->trans=$trans;
    }

    /**
     * @Route("/{code}", name="invitation_card")
     */
    public function index($code, Request $request,TranslatorInterface $translator): Response
    {
        // Clean the code parameter
        $code = Helper::clean($code);

        // Get the language from the cookie or the session
        $parameters=$this->trans->translation($request,$translator);
        // Calculate the hash
        $hash = base64_encode(hash($this->hash_algo, $code . $parameters['lang'] . $this->certificate, true));

        // Prepare the form data
        $form_data = [
            'Code' => $code,
            'lang' => $parameters['lang'],
            'Hash' => $hash,
        ];
        $params['data'] = json_encode($form_data);
        $params['url'] = 'Incentive/CardInvitationDetails';
        // Send the first curl request
        $response = Helper::send_curl($params);
        // $arr = array('RespCode' => 0, 'RespDesc' => 'Successful', 'RespTitle' => '{Name} invited you to start paying less with sKash for your daily transactions just like he is.', 'InviterName' => 'Gregorios Georgiou');
        // dd($arr);
        // $parameters['Invitation'] = json_decode($response, true);
        // dd($parameters);
        // $response = json_encode($arr);
        // Decode the response
        $invitation_card_details_response = json_decode($response, true);
        // Replace the inviter name in the response title
        $invitation_card_details_response = str_replace("{Name}", $invitation_card_details_response['InviterName'], $invitation_card_details_response['RespTitle']);
        // Add the code in the URL to validate it in the invitation page
        //$invitation_card_details_response['code'] = $code;

        // Render the template with the response data
        return $this->render('invitationCard/index.html.twig', [
            'inviterDetails' => $invitation_card_details_response,
            'code' => $code,
        ]);

    }

    /**
     * @Route("/invitationCard/submitInvitationCard", name="submit_mobile", methods={"POST"})
     */
    public function submitMobile(Request $request)
    {   //dd($request);
        // Get the mobile number from the form submission
        $mobile = Helper::clean($request->get('mobileNumber'));
        $code = Helper::clean($request->get('code'));
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
            /*** Call the api ***/
            $response = Helper::send_curl($params);

            /*** Decode the result ***/
            $result = json_decode($response, true);
            //Success is false by default
            $result['success'] = false;

            if($result['RespCode'] == 0){
                //Set Flag to know that we are in the success mode
                $result['success'] = true;
            }

        // Return a response to the user
        return $this->json($result);
    }
}

