<?php

namespace App\Controller;

use App\Repository\DepenseRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\Routing\Annotation\Route;

class EmailController extends AbstractController
{
    private $depenseRepository;

    public function __construct(DepenseRepository $depenseRepository)
    {
        $this->depenseRepository = $depenseRepository;
    }
    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/send-mail', name: 'app_send_mail', methods: ['POST'])]
    public function sendMail(MailerInterface $mailer): Response
    {
        $transport = Transport::fromDsn('smtp://finfoliofinfolio@gmail.com:txzoffvmvmoiuyzw@smtp.gmail.com:587');

// Create a Mailer object
        $mailer = new Mailer($transport);
        // Generate PDF content
        $pdfContent = $this->generatePdfContent();

        // Create an instance of Email
        try {
            // Create an instance of Email
            $email = (new Email())
                ->from('finfoliofinfolio@gmail.com')
                ->to('benouaghrem.miriam@gmail.com')
                ->subject('Your Expenses')
                ->text('Bonjour vous trouverez si join une pièce jointe pdf de toute vos dépenses .')
                ->attach($pdfContent, 'expenses.pdf', 'application/pdf');

            // Send the email
            $mailer->send($email);

            // Add a flash message to indicate that the email has been sent
            $this->addFlash('success', 'Email containing expenses sent successfully.');

            // Return a JSON response with success message
            return $this->json(['success' => true]);
        } catch (TransportExceptionInterface $e) {
            // Return a JSON response with error message
            return $this->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    private function generatePdfContent(): string
    {
        // Fetch all expenses from the repository
        $expenses = $this->depenseRepository->findAll();

        // Create an instance of Dompdf with options
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $dompdf = new Dompdf($options);

        // Render HTML content for expenses
        $html = $this->renderView('pdftep.html.twig', [
            'expenses' => $expenses,
        ]);

        // Load HTML content into Dompdf
        $dompdf->loadHtml($html);

        // Set paper size and orientation
        $dompdf->setPaper('A4', 'portrait');

        // Render PDF
        $dompdf->render();

        // Get PDF content
        return $dompdf->output();
    }
}
