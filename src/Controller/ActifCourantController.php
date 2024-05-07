<?php

namespace App\Controller;

use App\Entity\ActifCourant;
use App\Form\ActifCourantType;
use App\Repository\ActifCourantRepository;
use App\Repository\ActifNonCourantRepository;
use App\Repository\DepenseRepository;
use App\Repository\OffreRepository;
use App\Repository\UserRepository;
use App\Service\PDFGeneratorService;
use Doctrine\ORM\EntityManagerInterface;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Snappy\Pdf;

#[Route('/actif/courant')]
class ActifCourantController extends AbstractController
{
    #[Route('/', name: 'app_actif_courant_index', methods: ['GET'])]
    public function index(ActifCourantRepository $actifCourantRepository,ActifNonCourantRepository $actifNonCourantRepository): Response
    {       $actifCourants = $this->getDoctrine()->getRepository(ActifCourant::class)->findAll();



        return $this->render('actif_courant/index.html.twig', [
            'actif_courants' => $actifCourantRepository->findACByUserId($this->getUser()->getId()),
            'actif_non_courants' => $actifNonCourantRepository->findACNByUserId($this->getUser()->getId()),
        ]);
    }
    #[Route('/pdf', name: 'app_actif_courant_pdf',methods: ['POST','GET'])]
    public function generatePDF(Pdf $snappyPdf ,ActifCourantRepository $actifCourantRepository,ActifNonCourantRepository $actifNonCourantRepository, OffreRepository $offreRepository, DepenseRepository $depenseRepository): Response
    {
        // Render HTML content using Twig template and pass data to the template
        $imagePath = 'C:/Users/PC/Desktop/SymfonyFinFolio/public/logo.png';
        $imageData = base64_encode(file_get_contents($imagePath));
        // Generate QR code content (e.g., URL)
        $qrCodeContent = 'http://127.0.0.1:8000/actif/courant/'; // Replace with your QR code content

        // Generate QR code image binary data
        $qrCodeBinary = $this->generateQrCodeImage($qrCodeContent);
        $htmlContent = $this->renderView('actif_courant/pdf_template.html.twig', [
            'imageData' => $imageData,
            'actif_courants' => $actifCourantRepository->findAll(),
            'actif_non_courants' => $actifNonCourantRepository->findAll(),
            'offres'=>$offreRepository->findAll(),
            'depenses'=>$depenseRepository->findAll(),
            'qrCodeBinary' => $qrCodeBinary,
            // Pass any additional variables needed by your Twig template
        ]);



        // Generate PDF output from HTML content
        $pdfOutput = $snappyPdf->getOutputFromHtml($htmlContent, array(
            'images' => true,
            // Enable image processing
        ));
        $transport = Transport::fromDsn('smtp://finfoliofinfolio@gmail.com:txzoffvmvmoiuyzw@smtp.gmail.com:587');
        $mailer=new Mailer($transport);
        // Generate PDF content
        //$pdfContent = $this->generatePDF($snappyPdf,$actifCourantRepository,$actifNonCourantRepository,$offreRepository);

// Create an Email object

        // Create an Email message
        $email = (new Email())
            ->from(new Address('finfoliofinfolio@gmail.com'))
            ->to('mahmoud.zhiri@gmail.com')
            ->subject('PDF Report')
            ->text('Please find attached the PDF report.')
            ->attach($pdfOutput, 'actif_courant_report.pdf', 'application/pdf');

        // Send the email
        $mailer->send($email);
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
    private function generateQrCodeImage(string $content): string
    {
        $qrCode = new QrCode($content);
        $writer = new PngWriter();
        $qrCode->setSize(200); // Adjust size as needed
        $qrCode->setMargin(10); // Add margin around the QR code

        $qrCodeResult = $writer->write($qrCode);

        // Get the binary data (image content) from the result object
        $qrCodeBinary = $qrCodeResult->getString();

        // Specify the file path to save the QR code image
        // Return the binary image data
        return base64_encode($qrCodeBinary);
    }
    #[Route('/email', name: 'app_actif_courant_email',methods: ['POST','GET'])]
    public function sendEmailWithAttachment(MailerInterface $mailer, Pdf $snappyPdf,ActifCourantRepository $actifCourantRepository,ActifNonCourantRepository $actifNonCourantRepository, OffreRepository $offreRepository): Response
    {
        $transport = Transport::fromDsn('smtp://anouar.jebri@gmail.com:umqvgleqwbbekqrd@smtp.gmail.com:587');
        $mailer=new Mailer($transport);
        // Generate PDF content
        $pdfContent = $this->generatePDF($snappyPdf,$actifCourantRepository,$actifNonCourantRepository,$offreRepository);

        // Create an Email message
        $email = (new Email())
            ->from(new Address('anouar.jebri@gmail.com','Finfolio'))
            ->to('mahmoud.zhiri@gmail.com')
            ->subject('PDF Report')
            ->text('Please find attached the PDF report')
            ->html('<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDF Report</title>
    <style>
        /* Customize email styles */
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        h1 {
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 8px;
        }
    </style>
</head>')
            ->attach($pdfContent, 'actif_courant_report.pdf', 'application/pdf');

        // Send the email
        $mailer->send($email);

        // Return a response (e.g., a success message)
        return new Response('Email sent with PDF attachment.');
    }
    #[Route('/psdf', name: 'test', methods: ['GET'])]
    public function index2(ActifCourantRepository $actifCourantRepository,ActifNonCourantRepository $actifNonCourantRepository, OffreRepository $offreRepository,DepenseRepository $depenseRepository): Response
    {



        return $this->render('actif_courant/pdf_template.html.twig', [
            'actif_courants' => $actifCourantRepository->findAll(),
            'actif_non_courants' => $actifNonCourantRepository->findAll(),
            'offres'=>$offreRepository->findAll(),
            'depenses'=>$depenseRepository->findAll(),
        ]);
    }


    #[Route('/new', name: 'app_actif_courant_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager,UserRepository $userRepository): Response
    {

        $actifCourant = new ActifCourant();
        $user = $userRepository->find($this->getUser()->getId());

        $actifCourant->setUserId($user);

        $form = $this->createForm(ActifCourantType::class, $actifCourant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($actifCourant);
            $entityManager->flush();


            return $this->redirectToRoute('app_actif_courant_index', [], Response::HTTP_SEE_OTHER);
        }


        return $this->render('actif_courant/new.html.twig', [
            'actif_courant' => $actifCourant,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_actif_courant_show', methods: ['GET'])]
    public function show(ActifCourant $actifCourant): Response
    {
        return $this->render('actif_courant/show.html.twig', [
            'actif_courant' => $actifCourant,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_actif_courant_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ActifCourant $actifCourant, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ActifCourantType::class, $actifCourant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_actif_courant_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('actif_courant/edit.html.twig', [
            'actif_courant' => $actifCourant,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_actif_courant_delete', methods: ['POST'])]
    public function delete(Request $request, ActifCourant $actifCourant, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$actifCourant->getId(), $request->request->get('_token'))) {
            $entityManager->remove($actifCourant);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_actif_courant_index', [], Response::HTTP_SEE_OTHER);
    }





    #[Route('/ps', name: 'ps', methods: ['GET','POST'])]
    public function ps(ActifCourantRepository $actifCourantRepository,ActifNonCourantRepository $actifNonCourantRepository): Response
    {
        return $this->render('actif_courant/pdf_template.html.twig', [
            'actif_courants' => $actifCourantRepository->findAll(),
            'actif_non_courants' => $actifNonCourantRepository->findAll(),
        ]);
    }



    public function actifCourantChartData()
    {
        $actifCourants = $this->getDoctrine()->getRepository(ActifCourant::class)->findAll();

        // Initialize type counts
        $typeCounts = [];

        // Count occurrences of each type
        foreach ($actifCourants as $actifCourant) {
            $type = $actifCourant->getType();
            if (array_key_exists($type, $typeCounts)) {
                $typeCounts[$type]++;
            } else {
                $typeCounts[$type] = 1;
            }
        }

        // Prepare data for chart

        $seriesData = [10, 20, 30, 40];
        $labels = ['Type A', 'Type B', 'Type C', 'Type D'];
        return $this->render('actif_courant/index.html.twig', [
            'seriesData' => json_encode($seriesData),
            'labels' => json_encode($labels)
        ]);
    }
}
