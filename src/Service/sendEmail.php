<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class sendEmail
{

    private $mailerInterface;

    public function __construct(MailerInterface $mailerInterface)
    {
        $this->mailerInterface = $mailerInterface;
    }

    public function sendEmail($from, $to,$cc, $subject, $text)
    {
        $email = (new TemplatedEmail())
            ->from($from)
            ->to($to)
            ->cc($cc)
            ->subject($subject)
            ->text($text);
        $this->mailerInterface->send($email);

        return true;
    }
}
