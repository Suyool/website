<?php
namespace App\Controller;

use App\Service\Sms;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


class SecurityController extends AbstractController
{
    #[Route(path: '/admin_login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('Admin/security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @Route(
     *     "admin/sendAuthCode", name="sms_auth_code",
     * )
     */
    public function sendAuthCode(Sms $sms)
    {
        $success = $this->getUser()->sendAuthCode($sms);
        $message = ($success)?'_LOGIN_AUTHENTICATION_SENT_MESSAGE_':'_COULDNT_SEND_THE_CODE_';
        return $this->json(["success"=>$success,"messages"=>($message)]);
    }
}