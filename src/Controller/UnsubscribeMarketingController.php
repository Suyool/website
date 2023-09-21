<?php

namespace App\Controller;

use App\Service\SuyoolServices;
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
    public function index(Request $request,SuyoolServices $suyoolServices)
    {
        //To test this page /unsubscribeMarketing?uniqueCode=AyW5pahXmzYRBBVf&Flag=1
        $code = $request->query->get('uniqueCode');
        $flag = $request->query->get('Flag');

        if ($code != '') {

            if ($flag == 1) {
                //Call the API
                $response = $suyoolServices->UnsubscribeMarketing($code,$flag);

            }
            //If the Email is unsubscriped and the user is not registered
            if ($response['flagCode'] == 1) {
                $title = 'You have been unsubscribed';
                // $url = "/UnsubscribeMarketing?uniqueCode=" . $code . "&Flag=1";
                $description = 'You have been successfully removed from this list. <span class="error-check">If you did this in error</span> <br>
    <button type="button" class="openModel" data-bs-toggle="modal" data-bs-target="#myModal" id="resubscribe" data-code='.$code.' data-flag="'.$flag.'">
    Re-Subscribe
</button>';
                $image = "unverified-msg.png";
                $class = "unverified";
                //If the Email is Failed
            } else if ($response['flagCode'] == -1) {
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
    public function resubscribe(Request $request,SuyoolServices $suyoolServices): JsonResponse
    {
        $code = $request->query->get('uniqueCode');
        $flag = $request->query->get('flag');

        $response = $suyoolServices->resubscribeMarketing($code,$flag);
        return new JsonResponse($response);
    }
}
