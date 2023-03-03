<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

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
            $params['url'] = 'Incentive/ValidateEmail?Data=' . $code;
            $params['type'] = 'get';

            // Call the API
            $result = $this->send_curl($params);
            // Get the response
            $response = json_decode($result, true);
            // Default value of the notification

            //$response['RespCode'] = 1;
            // If the Email is Verified and the user is not registered
            if ($response['RespCode'] == 1 || $response['RespCode'] == 0) {
                $title = 'You have verified your email';
                $description = "You have successfully verified your email.<br> You will start receiving communication emails from Suyool.";
                $image = "email-verified.png";
                $class = "green";
            } else if ($response['RespCode'] == -1) {
                $title = 'Email verification failed';
                $description = "Your email address couldn’t be verified.<br> Kindly request a new verification link from your Suyool app.";
                $image = "unverified-msg.png";
                $class = "red";
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
    function send_curl($params) {
        $host = 'https://hey-pay.mobi/';
        if (isset($params['url']) || isset($params['data'])) {
            $ch = curl_init();
            //Set the options
            curl_setopt($ch, CURLOPT_URL, $host. $params['url']);

            //Set the data
            (isset($params['data'])) ? $data = $params['data'] : $data = "";
            //If the request type is not get, add the CURL postfield data
            (!isset($params['type']) || $params['type'] != 'get') ? curl_setopt($ch, CURLOPT_POSTFIELDS, $data) : '';
            //If type of the request is post add it
            (isset($params['type']) && $params['type'] == 'post') ? curl_setopt($ch, CURLOPT_POST, true) : '';
            //
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Content-Type: application/json',
                    'Connection: Keep-Alive',
                ]
            );

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_VERBOSE, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $ret = curl_exec($ch);
            curl_close($ch);
            return $ret;

        }
    }
}
