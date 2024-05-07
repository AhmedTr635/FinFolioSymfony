<?php

namespace App\Controller;
use App\Entity\TotalTax;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\NotSupported;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;

use App\Entity\Credit;
use App\Entity\Depense;
use App\Entity\Don;
use App\Entity\Investissement;
use App\Entity\Tax;
use App\Entity\User;
use App\Form\DepenseType;
use App\Repository\CreditRepository;
use App\Repository\DepenseRepository;
use App\Repository\TotalTaxRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Knp\Component\Pager\PaginatorInterface;
use League\Csv\Writer;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use function PHPUnit\Framework\returnArgument;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Dompdf\Dompdf;
use Dompdf\Options;

#[Route('/depense')]
class DepenseController extends AbstractController
{
    private $taxController;
    private $totalTaxRepository;
    public function __construct(TaxController $taxController, TotalTaxRepository $totalTaxRepository)
    {
        $this->taxController = $taxController;
        $this->totalTaxRepository = $totalTaxRepository;


    }
    /**
     * @throws NonUniqueResultException
     */
    #[Route('/', name: 'app_depense_index', methods: ['GET', 'POST'])]
    public function index(PaginatorInterface $paginator, Request $request, TotalTaxRepository $totalTaxRepository, DepenseRepository $depenseRepository, TaxController $taxC): Response
    {

        $totalTax = $taxC->sommeTax();
        $currentTaxTotale = $totalTax + $totalTaxRepository->getTotalTaxValue();
        $depenseByType= $depenseRepository->getExpensesByDepenseType();

//       dd($this->getUser()->getId()+1);
        // Get the hint message based on the user's actions
        $hintMessage = $this->getTaxHintMessage($this->getUser()->getId());
        // Retrieve the updated total tax sum from the request parameters
        $updatedTotalTax = $request->query->get('updatedTotalTax');
        $expensesByMonth = $depenseRepository->getExpensesByMonth();

        // Prepare data for the chart
        $labels = [];
        $data1 = [];
        foreach ($expensesByMonth['month'] as $month) {
            $labels[] = $month; // Month names
        }
        foreach ($expensesByMonth['total'] as $total) {
            $data1[] = $total; // Total expenses for each month
        }

        // Create your form
        $form = $this->createForm(DepenseType::class);
        $data = $depenseRepository->findDepensesByUserId($this->getUser()->getId()); // Fetch your data (e.g., from Doctrine)
        $depenses = $paginator->paginate(
            $data,
            $request->query->getInt('page', 1),
            3
        );
        $paginationTemplate = '@KnpPaginator/Pagination/twitter_bootstrap_v4_pagination.html.twig';
        // Calculate total montant
        $totalMontant = $this->sommeDep();
        $depenseByType = [];

        // Calculate total by month
        $totalByMonth = $this->sommeDepByMonth();
        $chartData = [
            'labels' => json_encode($labels), // Convert labels array to JSON
            'data' => json_encode($data1) // Convert data array to JSON
        ];

        return $this->render('depense/index.html.twig', [
            ''=> $depenseByType,
            'paginationTemplate' => $paginationTemplate,
            'hintMessage' => $hintMessage,
            'depenses' => $depenses, // Pass the pagination object instead of the raw data
            'totalMontant' => $totalMontant,
            'totalByMonth' => $totalByMonth,
            'expensesByMonth' => $expensesByMonth,
            'chartData' => $chartData,
            'totalTax' => $currentTaxTotale,

            'form' => $form->createView()

        ]);
    }
    #[Route( name: 'app_depense_search', methods: ['POST'])]

    public function search(Request $request, DepenseRepository $depenseRepository): Response
    {
        $query = $request->query->get('query');

        // Query depenses based on the search query
        $depenses = $depenseRepository->searchByType($query); // Use your repository method to search by type

        return $this->render('depense/_depense_list.html.twig', [
            'depenses' => $depenses,
        ]);
    }

    /**
     * @throws NonUniqueResultException
     */
    #[Route('/new', name: 'app_depense_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository,TaxController $taxController,TotalTaxRepository $totalTaxRepository): Response
    {
        $depense = new Depense();

        $user = $userRepository->find($this->getUser()->getId());

        $depense->setUser($user);
// Now you can use this $user instance to perform operations like persisting it to the database

        $form = $this->createForm(DepenseType::class, $depense);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tax = new Tax();
            $tax->setMontant($depense->getMontant() * 0.14);
            $tax->setType("Depense");
            $tax->setOptimisation("Depense");

            $entityManager->persist($tax);
            $entityManager->flush();
            $depense->setTax($tax);
            $entityManager->persist($depense);

            $entityManager->flush();

            $totalTax = $entityManager->getRepository(TotalTax::class)->findOneBy([]);
            if (!$totalTax) {
                $totalTax = new TotalTax();
                $totalTax->setTotal(0);
            }
            $totalTax->setTotal($totalTax->getTotal() + $tax->getMontant() );
            $entityManager->persist($totalTax);
            $entityManager->flush();

            return $this->redirectToRoute('app_depense_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('depense/new.html.twig', [
            'depense' => $depense,
            'form' => $form,
        ]);
    }
    #[Route('/generate-excel', name: 'generate_excel', methods: ['GET','POST'])]
    public function generateExcel(DepenseRepository $depenseRepository): Response
    {
        // Retrieve expense data from your database or any other source
        $entityManager = $this->getDoctrine()->getManager();
        $expenses = $depenseRepository->findDepensesByUserId($this->getUser()->getId()); // Assuming Depense is your entity class

        // Create a new Spreadsheet object
        $spreadsheet = new Spreadsheet();

        // Add headers to the spreadsheet
        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', 'type')
            ->setCellValue('B1', 'montant')
            ->setCellValue('C1', 'date');

        // Add expense data to the spreadsheet
        $row = 2; // Start from row 2 (after the headers)
        foreach ($expenses as $expense) {
            $spreadsheet->getActiveSheet()
                ->setCellValue('A' . $row, $expense->getDate()->format('Y-m-d')) // Assuming 'date' is a property of your Depense entity
                ->setCellValue('B' . $row, $expense->getMontant()) // Assuming 'montant' is a property of your Depense entity
                ->setCellValue('C' . $row, $expense->getType()); // Assuming 'type' is a property of your Depense entity
            $row++; // Move to the next row
        }

        // Create a writer
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');

        // Write the spreadsheet to a stream
        $stream = fopen('php://temp', 'r+');
        $writer->save($stream);
        rewind($stream);

        // Return a response with the Excel file
        return new Response(stream_get_contents($stream), 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="expenses.xlsx"',
        ]);
    }
    #[Route('/{id}', name: 'app_depense_show', methods: ['GET'])]
    public function show(Depense $depense): Response
    {
        return $this->render('depense/show.html.twig', [
            'depense' => $depense,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_depense_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Depense $depense, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DepenseType::class, $depense);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_depense_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('depense/edit.html.twig', [
            'depense' => $depense,
            'form' => $form,

        ]);
    }

    #[Route('/{id}', name: 'app_depense_delete', methods: ['POST'])]
    public function delete(Request $request, Depense $depense, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $depense->getId(), $request->request->get('_token'))) {
            $entityManager->remove($depense);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_depense_index', [], Response::HTTP_SEE_OTHER);
    }

    public function sommeDep(): float
    {
        $repository = $this->getDoctrine()->getRepository(Depense::class);
        $depenses = $repository->findDepensesByUserId($this->getUser()->getId());

        // Calculer la somme totale des montants de toutes les dépenses
        $totalMontant = 0;
        foreach ($depenses as $depense) {
            $totalMontant += $depense->getMontant();
        }

        return $totalMontant;
    }

    public function sommeDepByMonth(): float
    {
        // Get the current month and year
        $currentMonth = date('m');
        $currentYear = date('Y');

        // Get the start and end dates of the current month
        $startDate = new \DateTime("{$currentYear}-{$currentMonth}-01");
        $endDate = new \DateTime("{$currentYear}-{$currentMonth}-" . date('t', strtotime($startDate->format('Y-m-d'))));

        $repository = $this->getDoctrine()->getRepository(Depense::class);

        // Query expenses for the current month
        $queryBuilder = $repository->createQueryBuilder('d');
        $queryBuilder->select('SUM(d.montant) as totalMontant')
            ->where('d.date >= :start_date')
            ->andWhere('d.date <= :end_date')
            ->andWhere('d.user = :userid') // Add this line
            ->setParameter('start_date', $startDate)
            ->setParameter('end_date', $endDate)
            ->setParameter('userid', $this->getUser()->getId()); // Make sure to set the parameter value


        $totalMontant = $queryBuilder->getQuery()->getSingleScalarResult();

        // If there are no expenses for the current month, set the total to 0
        if ($totalMontant === null) {
            $totalMontant = 0;
        }

        return $totalMontant;
    }



//    public function generateExcel(): Response
//    {
//        // Retrieve expense data from your database (dummy data for demonstration)
//        $expenses = [
//            ['Date', 'Description', 'Amount'],
//            ['2024-03-01', 'Office Supplies', '50'],
//            ['2024-03-05', 'Travel Expenses', '100'],
//            ['2024-03-10', 'Client Dinner', '150'],
//            // Add more expense records as needed
//        ];
//
//        // Create a CSV writer object
//        $csv = Writer::createFromString('');
//        $csv->insertAll($expenses);
//
//        // Write CSV data to a file
//        $csvFilePath = 'expenses.csv';
//        $csv->output($csvFilePath);
//
//        // Convert CSV to Excel (XLSX) format using PhpSpreadsheet
//        $spreadsheet = new Spreadsheet();
//        $sheet = $spreadsheet->getActiveSheet();
//        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Csv');
//        $spreadsheet = $reader->load($csvFilePath);
//
//        // Save the Excel file
//        $excelFilePath = 'expenses.xlsx';
//        $writer = new Xlsx($spreadsheet);
//        $writer->save($excelFilePath);
//
//        // Create a Symfony BinaryFileResponse to serve the Excel file for download
//        $response = new BinaryFileResponse($excelFilePath);
//        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
//        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'expenses.xlsx');
//
//        return $response;
//    }


//    #[Route(name: 'app_depense_search', methods: ['POST'])]
//    public function search(Request $request, DepenseRepository $depenseRepository): Response
//    {
//        $query = $request->query->get('query');
//
//        // Query depenses based on the search query
//        $depenses = $depenseRepository->searchByType($query); // Use your repository method to search by type
//
//        return $this->render('depense/_depense_list.html.twig', [
//            'depenses' => $depenses,
//        ]);
//    }

    public function hasMadeDonation($userId): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        // Retrieve the user's donation record from the database
        $donRepository = $entityManager->getRepository(Don::class);
        $userDep = $donRepository->findOneBy(['user_id' => $userId]);

        // Check if the user has made a donation
        $hasMadeDon = ($userDep !== null);

        return $this->json(['hasMadedon' => $hasMadeDon]);
    }
    public function hasMadeCredit($userId): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        // Retrieve the user's donation record from the database
        $donRepository = $entityManager->getRepository(Credit::class);
        $userCredit = $donRepository->findOneBy(['user_id' => $userId]);

        // Check if the user has made a donation
        $hasMadeCredit = ($userCredit !== null);

        return $this->json(['hasMadeCredit' => $hasMadeCredit]);
    }
//    public function hasMadeInv($userId): Response
//    {
//        $entityManager = $this->getDoctrine()->getManager();
//
//        // Retrieve the user's donation record from the database
//        $donRepository = $entityManager->getRepository(Investissement::class);
//        $userInv = $donRepository->findOneBy(['user_id' => $userId]);
//
//        // Check if the user has made a donation
//        $hasMadeInv = ($userInv !== null);
//
//        return $this->json(['hasMadeCredit' => $hasMadeInv]);
//    }

// Partie hints
    /**
     * @throws NonUniqueResultException
     */
    private function getTaxHintMessage($userId): string
    {

        $totalTax = $this->taxController->sommeTax();
        $currentTaxTotal = $totalTax + $this->totalTaxRepository->getTotalTaxValue();
        // Vérifiez si la taxe totale est supérieure à 1000
        if ($currentTaxTotal > 1000) {
            // Check if the user has made a donation
            $response = $this->hasMadeDonation($userId);
            $response2 = $this->hasMadeCredit($userId);

            $content = json_decode($response->getContent(), true);
            $hasMadeDon = $content['hasMadedon'];

            $content2 = json_decode($response2->getContent(), true);
            $hasMadeCredit = $content2['hasMadeCredit'];

            // Déterminez le message d'indice en fonction des actions de l'utilisateur
            if (!$hasMadeDon && !$hasMadeCredit) {
                return 'Vous pouvez penser à faire un don ou un crédit pour diminuer votre taxe.';
            } else if (!$hasMadeCredit) {
                return 'Vous pouvez penser à faire un crédit pour diminuer votre taxe.';
            } else if (!$hasMadeDon) {
                return "Vous pouvez penser à faire un don pour diminuer votre taxe.";
            }
        }

        return ""; // Return an empty string if no condition is met
    }
    #[Route(name: 'app_depense_search', methods: ['GET'])]

    public function searchDepense(Request $request)
    {
        $searchTerm = $request->query->get('term');

        // Perform search logic here and return results as JSON response
        $entityManager = $this->getDoctrine()->getManager();
        $depenses = $entityManager->getRepository(Depense::class)->findByTerm($searchTerm);

        $response = [];
        foreach ($depenses as $depense) {
            $response[] = [
                'id' => $depense->getId(),
                'name' => $depense->getName(),
                // Add other properties you want to return
            ];
        }

        return new JsonResponse($response);
    }

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/send-mail', name: 'app_send_mail', methods: ['POST'])]
    public function sendMail(MailerInterface $mailer): Response
    {
        // Create an empty email
        $email = (new Email())
            ->from('finfoliofinfolio@gmail.com')
            ->to('benouaghrem.miriam@gmail.com')
            ->subject('Empty Mail')
            ->text('This is an empty email.');

        // Send the email
        $mailer->send($email);

        // Add a flash message to indicate that the email has been sent
        $this->addFlash('success', 'Empty email sent successfully.');

        // Redirect back to the page where the button was clicked
        return $this->redirectToRoute('depense/index.html.twig');
    }
    /**
     * Generates PDF content for the given Depense entity.
     */
    private function generatePdfContentForUser(User $user): string
    {
        // Retrieve depenses for the user from the database or any other source
        $depenses = $this->getDoctrine()->getRepository(Depense::class)->findBy(['user' => $user]);

        // Example HTML content for the PDF
        $html = '<h1>List of Depenses for User ' . $user->getNom() . '</h1>';
        $html .= '<table border="1"><tr><th>Date</th><th>Amount</th><th>Description</th></tr>';

        // Loop through each depense and add it to the HTML content
        foreach ($depenses as $depense) {
            $html .= '<tr><td>' . $depense->getDate()->format('Y-m-d') . '</td>';
            $html .= '<td>' . $depense->getAmount() . '</td>';
            $html .= '<td>' . $depense->getDescription() . '</td></tr>';
        }

        $html .= '</table>';

        // Initialize Dompdf
        $dompdf = new Dompdf();

        // Load HTML content
        $dompdf->loadHtml($html);

        // Set paper size and orientation
        $dompdf->setPaper('A4', 'portrait');

        // Render PDF
        $dompdf->render();

        // Get PDF content as string
        return $dompdf->output();
    }
}


