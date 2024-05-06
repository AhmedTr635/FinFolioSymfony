<?php

namespace App\Controller;

use Knp\Snappy\Pdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PdfController extends AbstractController
{
    #[Route('/', name: 'app_pdf')]
    public function index(): Response
    {
        return $this->render('pdf/index.html.twig', [
            'controller_name' => 'PdfController',
        ]);
    }

    #[Route('/pdf', name: 'app_Pdf_pdf')]
    public function pdf(Pdf $snappyPdf ): Response
    {   print "mahmoud";
        // Render HTML content using Twig template and pass data to the template
        $htmlContent = '<html><body><p>This is a simple PDF generated using Symfony and SnappyBundle.</p></body></html>'    ;

        // Generate PDF output from HTML content
        $pdfOutput = $snappyPdf->getOutputFromHtml($htmlContent);
        // Return PDF as response
        return new Response(
            $pdfOutput,
            Response::HTTP_OK,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="actif_courant.pdf"',
            ]
        );
    }
}
