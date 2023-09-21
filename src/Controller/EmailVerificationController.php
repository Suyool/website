<?php

namespace App\Controller;

use App\Service\SuyoolServices;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Utils\Helper;

class EmailVerificationController extends AbstractController
{
    /**
     * @Route("/emailVerification/code={code}", name="app_email_verification")
     */
    public function index($code, SuyoolServices $suyoolServices)
    {
        $parameters['currentPage'] = "generate_Code";
        if ($code != '') {
            if ($_ENV['APP_ENV'] == "prod") {
                $params['url'] = 'User/ValidateEmail?Data=' . $code;
            } else {
                $params['url'] = 'User/ValidateEmail?Data=' . $code;
            }
//            $result = Helper::send_curl($params);
//            $response = json_decode($result, true);
            $response = $suyoolServices->ValidateEmail($code);

            if ($response == null) {
                return $this->render('ExceptionHandlingEmail.html.twig');
            }

            // If the Email is Verified and the user is not registered
            if ($response['flagCode'] == 1) {
                $title = 'You have verified your email';
                $description = "You have successfully verified your email.<br> You will start receiving communication emails from Suyool.";
                $image = "email-verified.png";
                $class = "verified";
            } else if ($response['flagCode'] == -1) {
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
