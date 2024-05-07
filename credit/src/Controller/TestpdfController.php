<?php

namespace App\Controller;
use Knp\Snappy\Pdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestpdfController extends AbstractController
{
    #[Route('/testpdf', name: 'app_testpdf')]
    public function index(): Response
    {
        return $this->render('testpdf/index.html.twig', [
            'controller_name' => 'TestpdfController',
        ]);
    }



    #[Route('/pdf', name: 'app_actif_courant_pdf', methods: ['POST', 'GET'])]
    public function generatePDF(Pdf $snappyPdf): Response
    {
        // Render HTML content using Twig template and pass data to the template

//        $imageData = base64_encode(file_get_contents($imagePath)); taswira

        $htmlContent = $this->renderView('testpdf/index.html.twig', [





            // Pass any additional variables needed by your Twig template
        ]);

        // Generate PDF from HTML content
        $pdf = $snappyPdf->getOutputFromHtml($htmlContent);

        // Create a response with PDF content
        $response = new Response($pdf);

        // Set response headers for PDF content
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'attachment; filename="credit.pdf"');

        return $response;
    }
}
