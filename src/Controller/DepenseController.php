<?php

namespace App\Controller;

use App\Entity\Depense;
use App\Entity\Tax;
use App\Entity\User;
use App\Form\DepenseType;
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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use function PHPUnit\Framework\returnArgument;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

#[Route('/depense')]
class DepenseController extends AbstractController
{
    /**
     * @throws NonUniqueResultException
     */
    #[Route('/', name: 'app_depense_index', methods: ['GET', 'POST'])]
    public function index(PaginatorInterface $paginator,Request $request,TotalTaxRepository $totalTaxRepository, DepenseRepository $depenseRepository,TaxController $taxC): Response
    {
        $totalTax = $taxC->sommeTax();
        $currentTaxTotale =$totalTax + $totalTaxRepository->getTotalTaxValue();


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
        $data = $depenseRepository->findAll(); // Fetch your data (e.g., from Doctrine)
        $depenses= $paginator->paginate(
            $data,
            $request->query->getInt('page',1)  ,
            5
        );
        $paginationTemplate = '@KnpPaginator/Pagination/twitter_bootstrap_v4_pagination.html.twig';
        // Calculate total montant
        $totalMontant = $this->sommeDep();

        // Calculate total by month
        $totalByMonth = $this->sommeDepByMonth();
        $chartData = [
            'labels' => json_encode($labels), // Convert labels array to JSON
            'data' => json_encode($data1) // Convert data array to JSON
        ];
//        dump($chartData);
//        die();
        // Render the template with paginated data
        return $this->render('depense/index.html.twig', [
            'paginationTemplate' => $paginationTemplate,

            'depenses' => $depenses, // Pass the pagination object instead of the raw data
            'totalMontant' => $totalMontant,
            'totalByMonth' => $totalByMonth,
            'expensesByMonth' => $expensesByMonth,
            'chartData' => $chartData,
            'totalTax' =>$currentTaxTotale,
//            'totalTax' => $currentTaxTotale,

            'form' => $form->createView()

        ]);
    }

    #[Route('/new', name: 'app_depense_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager,UserRepository $userRepository): Response
    {
        $depense = new Depense();

        $user = $userRepository->find(106);

        $depense->setUser($user);
// Now you can use this $user instance to perform operations like persisting it to the database

        $form = $this->createForm(DepenseType::class, $depense);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tax= new Tax();
            $tax->setMontant($depense->getMontant()*0.14);
            $tax->setType("Depense");
            $tax->setOptimisation("Depense");

       $entityManager->persist($tax);
        $entityManager->flush();
            $depense->setTax($tax);
            $entityManager->persist($depense);

            $entityManager->flush();

            return $this->redirectToRoute('app_depense_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('depense/new.html.twig', [
            'depense' => $depense,
            'form' => $form,
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
        if ($this->isCsrfTokenValid('delete'.$depense->getId(), $request->request->get('_token'))) {
            $entityManager->remove($depense);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_depense_index', [], Response::HTTP_SEE_OTHER);
    }

    public function sommeDep(): float
    {
        $repository = $this->getDoctrine()->getRepository(Depense::class);
        $depenses = $repository->findAll();

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
            ->setParameter('start_date', $startDate)
            ->setParameter('end_date', $endDate);

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
    #[Route('/generate-excel', name: 'generate_excel', methods: ['GET'])]
    public function generateExcel(): Response
    {
        // Retrieve expense data from your database or any other source
        $entityManager = $this->getDoctrine()->getManager();
        $expenses = $entityManager->getRepository(Depense::class)->findAll(); // Assuming Depense is your entity class

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

    #[Route( '/', name: 'pagination_route', methods: ['GET'])]

    public function paginationAction(Request $request, YourEntityRepository $repository)
    {
        // Récupérer les paramètres de pagination depuis la requête
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);

        // Récupérer les données paginées depuis le repository
        $pagination = $repository->findPaginatedData($page, $limit);

        // Renvoyer les données paginées au format JSON
        return new JsonResponse([
            'data' => $pagination->getItems(),
            'totalItems' => $pagination->count(),
            // Vous pouvez également inclure d'autres métadonnées de pagination ici
        ]);
    }
}
