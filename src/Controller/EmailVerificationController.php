<?php

namespace App\Controller;

use App\Service\SuyoolServices;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EmailVerificationController extends AbstractController
{
    /**
     * @Route("/emailVerification/code={code}", name="app_email_verification")
     */
    public function index($code, SuyoolServices $suyoolServices): Response
    {
        if (empty($code)) {
            return $this->renderError('Invalid verification code.');
        }

        // Fetch verification response
        $response = $suyoolServices->ValidateEmail($code);

        if ($response == null) {
            return $this->renderError('Verification service unavailable.');
        }

        $title = '';
        $description = '';
        $image = '';
        $class = '';

        if ($response['flagCode'] === 1) {
            $title = 'You have verified your email';
            $description = "You have successfully verified your email.<br> You will start receiving communication emails from Suyool.";
            $image = "email-verified.png";
            $class = "verified";
        } elseif ($response['flagCode'] === -1) {
            $title = 'Email verification failed';
            $description = "Your email address couldn’t be verified.<br> Kindly request a new verification link from your Suyool app.";
            $image = "unverified-msg.png";
            $class = "unverified";
        }

        return $this->render('emailVerification/index.html.twig', [
            'suyoolLogo' => 'suyool-final-logo.png',
            'title' => $title,
            'description' => $description,
            'image' => $image,
            'class' => $class,
        ]);
    }

    private function renderError($message): Response
    {
        // Log the error here if necessary
        return $this->render('ExceptionHandlingEmail.html.twig', ['errorMessage' => $message]);
    }
}
