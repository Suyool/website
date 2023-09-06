<?php

namespace App\Controller;

use App\Utils\Helper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class UnsubscribeMarketingController extends AbstractController
{
    /**
     * @Route("/unsubscribeMarketing", name="unsubscribe_marketing")
     *
     */
    public function index(Request $request)
    {
        //To test this page /unsubscribeMarketing?uniqueCode=AyW5pahXmzYRBBVf&Flag=1
        $code = $request->query->get('uniqueCode');
        $flag = $request->query->get('Flag');

        if ($code != '') {

            if ($flag == 1) {
                //Set the API URL
                $params['url'] = 'MarketingException/UnsubscribeMarketing?UniqueCode=' . $code . '&flag=' . $flag;
                $params['type'] = 'post';

                //Call the API
                $result = Helper::send_curl($params);

                //Get the response
                $response = json_decode($result, true);
            }
            $response['RespCode'] = 1;
            //If the Email is unsubscriped and the user is not registered
            if ($response['RespCode'] == 1) {
                $title = 'You have been unsubscribed';
                // $url = "/UnsubscribeMarketing?uniqueCode=" . $code . "&Flag=1";
                $description = 'You have been successfully removed from this list. <span class="error-check">If you did this in error</span> <br>
                                <button type="button" class="openModel" data-bs-toggle="modal" data-bs-target="#myModal" id="resubscribe" onclick="resubscribe('.$code.', '.$flag.')">
                                Re-Subscribe
                                </button>';
                $image = "unverified-msg.png";
                $class = "unverified";
                //If the Email is Failed
            } else if ($response['RespCode'] == -1) {
                $title = 'Unsubscribe Request Failed ';
                $description = 'We are unable to process your request right now. Please try again later ';
                $image = "fail_icon.gif";
                $class = "unverified";
            }
        }

        return $this->render('unsubscribe_marketing/index.html.twig', [
            'suyoolLogo' => 'suyool-final-logo.png',
            'title' => $title,
            'description' => $description,
            'image' => $image,
            'class' => $class,
        ]);
    }
    /**
     * @Route("/unsubscribeMarketing/resubscribe", name="resubscribe")
     */
    public function resubscribe(Request $request): JsonResponse
    {
        $code = $request->query->get('uniqueCode');
        $flag = $request->query->get('Flag');
        $params['url'] = 'MarketingException/subscribeMarketing?UniqueCode=' . $code . '&flag=' . $flag;
        $params['type'] = 'post';
        $result = Helper::send_curl($params);
        $response = json_decode($result, true);

        return new JsonResponse($response);
    }
}
