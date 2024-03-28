<?php
namespace App\Controller;

use Swift_Mailer;
use Swift_Message;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Message;
use Symfony\Component\Routing\Annotation\Route;

class MailService extends AbstractController
{


    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }


   /* public function sendEmail(string $to,string $code): void
    {
        /*$htmlContent = '
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification du compte</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #333;
        }
        p {
            color: #666;
        }
        .verification-code {
            background-color: #f0f0f0;
            padding: 10px;
            text-align: center;
            border-radius: 5px;
            margin-top: 20px;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Vérification du compte</h2>
        <p>Merci de vous être inscrit sur notre plateforme. Pour vérifier votre compte, veuillez utiliser le code suivant :</p>
        <div class="verification-code">
            <p style="font-size: 24px; font-weight: bold;">' . $code . '</p>
        </div>
        <p>Entrez ce code dans l\'application pour confirmer votre adresse e-mail et finaliser votre inscription.</p>
        <div class="footer">
            <p>Cordialement,<br>L\'équipe de [Votre entreprise]</p>
        </div>
    </div>
</body>
</html>
';

        $email = (new Email())
            ->from('finfoliofinfolio@gmail.com')
            ->to($to)
            ->subject('Vérification du compte!')
            ->html($htmlContent);

        $this->mailer->send($email);

    }*/
        #[Route('/email')]
    public function sendEmail(MailerInterface $mailer): Response
    {
        $email = (new Email())
            ->from('finfoliofinfolio@gmail.com')
            ->to('trabelsi.ahmed@esprit.tn')

            ->subject('Time for Symfony Mailer!')
            ->text('Sending emails is fun again!')
            ->html('<p>See Twig integration for better HTML integration!</p>');

        $mailer->send($email);

        return $this->render('Dashboard.html.twig');

    }
}

