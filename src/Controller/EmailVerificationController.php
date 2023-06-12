<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Utils\Helper;

class EmailVerificationController extends AbstractController
{
    /**
     * @Route("/emailVerification/code={code}", name="app_email_verification")
     */
    public function index($code)
    {
        $parameters['currentPage'] = "generate_Code";
        if ($code != '') {
            // Set the API URL
            $params['url'] = 'User/ValidateEmail?Data=' . $code;
            $params['type'] = 'post';

            // Call the API
            $result = Helper::send_curl($params);
            // Get the response
            $response = json_decode($result, true);
            // Default value of the notification

            $response['RespCode'] = 1;
            // If the Email is Verified and the user is not registered
            if ($response['RespCode'] == 1 || $response['RespCode'] == 0) {
                $title = 'You have verified your email';
                $description = "You have successfully verified your email.<br> You will start receiving communication emails from Suyool.";
                $image = "email-verified.png";
                $class = "verified";
            } else if ($response['RespCode'] == -1) {
                $title = 'Email verification failed';
                $description = "Your email address couldn’t be verified.<br> Kindly request a new verification link from your Suyool app.";
                $image = "unverified-msg.png";
                $class = "unverified";
            }
        }
        return $this->render('emailVerification/index.html.twig', [
            'suyoolLogo' => 'suyool-final-logo.png',
            'title' => $title,
            'description' => $description,
            'image' => $image,
            'class' => $class,
        ]);
    }
}
