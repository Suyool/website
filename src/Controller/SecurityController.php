<?php
namespace App\Controller;

use App\Entity\Rates;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use App\AdminBundle\Service\Sms;
use Symfony\Component\Translation\Translator;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\Managers;

class SecurityController extends AbstractController
{
    /**
     * @Route(
     *     "/login", name="login",
     * )
     */
    public function loginAction(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        $form = $this->createFormBuilder()
            ->add('_username', TextType::class)
            ->add('_password', DateType::class)
            ->add('save', SubmitType::class, ['label' => 'Create Task'])
            ->getForm();

        $currentRate = $this->getDoctrine()
            ->getRepository(Rates::class)
            ->getCurrentRate();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
            'form' => $form->createView(),
            'currentRate' => $currentRate
        ]);
    }

    /**
     * @Route(
     *     "/sendAuthCode", name="sms_auth_code",
     * )
     * @Route(
     *     "/admin/sendAuthCode", name="sms_auth_code_admin",
     * )
     */
    public function sendAuthCode(Sms $sms, TranslatorInterface $translator)
    {
        $success = $this->getUser()->sendAuthCode($sms);
        $message = ($success)?'_LOGIN_AUTHENTICATION_SENT_MESSAGE_':'_COULDNT_SEND_THE_CODE_';
        return $this->json(["success"=>$success,"messages"=>[$translator->trans($message,[],"admin")]]);
    }
}