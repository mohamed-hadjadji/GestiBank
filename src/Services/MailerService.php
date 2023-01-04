<?php

namespace App\Services;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailerService
{
    public function sendEmail(MailerInterface $mailer, $msg, $email):Response
    {
        $email = (new Email())
            ->from('gk@smart-it-partner.com')
            ->to($email)
            ->subject('Validation Création de compte')
            ->html($msg);
        $mailer->send($email);
        return new Response("Email Send");
    }
}