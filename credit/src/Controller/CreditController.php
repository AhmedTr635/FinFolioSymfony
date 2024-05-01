<?php

namespace App\Controller;
use Psr\Log\LoggerInterface;
use App\Entity\Credit;
use App\Form\Credit1Type;
use App\Repository\CreditRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Repository\UserRepository;
use Knp\Snappy\Pdf;


#[Route('/credit')]
class CreditController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/', name: 'app_credit_index', methods: ['GET'])]
    public function index(CreditRepository $creditRepository, UserRepository $userRepository): Response
    {
        $userId = 106; // Assuming the user ID is 106
        $user = $this->getDoctrine()->getRepository(User::class)->find($userId);

        // Check if the user with ID 106 exists
        if ($user) {
            // Get the solde
            $solde = $user->getSolde();
        } else {
            // Handle the case where the user with ID 106 is not found
            // You can redirect to an error page or display an error message
            // For example:
            throw $this->createNotFoundException('User with ID 106 not found');
        }

        $solde = number_format($solde, 2, '.', ',');
        $credits = $creditRepository->findAll();

        // Fetch statistics data
        $statisticsData = $this->getStatisticsData();

        // Merge credit data and statistics data
        // Fetch statistics data
        $statisticsData = $this->getStatisticsData();
        $totalCreditRequestsAmount = $this->getTotalCreditRequestsAmount();

        // Merge credit data and statistics data
        $data = [
            'credits' => $credits,
            'totalCreditRequestsAmount' => $totalCreditRequestsAmount,

            'solde' => $solde,
            'totalCreditRequests' => $statisticsData['totalCreditRequests'],
            'amountStats' => $statisticsData['amountStats'],
            'interestStats' => $statisticsData['interestStats'],
        ];

        return $this->render('templateController/listofCredit.html.twig', $data);
    }


    #[Route('/CreditRequests', name: 'app_credit_index1', methods: ['GET'])]
    public function index1(CreditRepository $creditRepository): Response
    {
        $credits = $creditRepository->findAll(); // Fetch credits from repository

        return $this->render('templateController/index.html.twig', [
            'credits' => $credits, // Pass credits to the template
        ]);
    }


    #[Route('/new', name: 'app_credit_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $credit = new Credit();
        $form = $this->createForm(Credit1Type::class, $credit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($credit);
            $entityManager->flush();

            return $this->redirectToRoute('app_credit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('templateController/new.html.twig', [
            'credit' => $credit,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_credit_show', methods: ['GET'])]
    public function show(Credit $credit): Response
    {
        return $this->render('templateController/show.html.twig', [
            'credit' => $credit,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_credit_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Credit $credit, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(Credit1Type::class, $credit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_credit_index1', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('templateController/edit.html.twig', [
            'credit' => $credit,

            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_credit_delete', methods: ['POST'])]
    public function delete(Request $request, Credit $credit, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $credit->getId(), $request->request->get('_token'))) {
            $entityManager->remove($credit);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_credit_index1', [], Response::HTTP_SEE_OTHER);
    }


    #[Route('/{user_id}/{credit_id}', name: 'app_chat', methods: ['GET'])]
    public function chat($user_id, $credit_id, UserRepository $userRepository): Response
    {
        // Set user_id to 1
        $user1 = $userRepository->find($user_id);

        if (!$user1) {
            throw $this->createNotFoundException('User not found');
        }

        // Fetch the Credit entity based on the provided credit_id
        $credit = $this->getDoctrine()->getRepository(Credit::class)->find($credit_id);

        if (!$credit) {
            throw $this->createNotFoundException('Credit not found');
        }

        // Fetch the second user associated with the Credit entity
        $user2 = $credit->getUserId(); // Assuming you have a method to get the user associated with the credit

        // Log the IDs of the two users


        // Render the template with the alert message
        $content = $this->renderView('templateController/chat.html.twig', [
            'user1' => $user1,
            'user2' => $user2,
            'controller_name' => 'DefaultController',
        ]);

        return new Response($content);
    }

    #[Route('/pdf', name: 'app_pdf', methods: ['POST'])]
    public function generatePdfAction(Pdf $pdf)
    {
        // Render the Twig template to HTML
        $html = $this->renderView('your_template.html.twig', [
            // Pass any necessary variables to the template

        ]);

        // Generate the PDF
        $pdfContent = $pdf->getOutputFromHtml($html);

        // Return a Symfony Response with the PDF content
        return new Response(
            $pdfContent,
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="your_filename.pdf"'
            ]
        );
    }


    private function getStatisticsData(): array
    {
        // Total Number of Credit Requests
        $totalCreditRequests = $this->entityManager
            ->createQuery('SELECT COUNT(c.id) AS total_credit_requests FROM App\Entity\Credit c')
            ->getSingleScalarResult();

        // Average, Minimum, and Maximum Amount Requested
        $amountStats = $this->entityManager
            ->createQuery('SELECT AVG(c.montant) AS average_amount_requested,
                          MIN(c.montant) AS minimum_amount_requested,
                          MAX(c.montant) AS maximum_amount_requested
                     FROM App\Entity\Credit c')
            ->getSingleResult();

        // Average, Minimum, and Maximum Interest Rates
        $interestStats = $this->entityManager
            ->createQuery('SELECT AVG(c.interetMax) AS average_interest_max,
                          AVG(c.interetMin) AS average_interest_min,
                          MIN(c.interetMax) AS minimum_interest_max,
                          MIN(c.interetMin) AS minimum_interest_min,
                          MAX(c.interetMax) AS maximum_interest_max,
                          MAX(c.interetMin) AS maximum_interest_min
                     FROM App\Entity\Credit c')
            ->getSingleResult();

        // Correlation Between Amount Requested and Interest Rates


        return [
            'totalCreditRequests' => $totalCreditRequests,
            'amountStats' => $amountStats,
            'interestStats' => $interestStats,
        ];
    }
    private function getTotalCreditRequestsAmount(): float
    {
        $totalAmount = $this->entityManager
            ->createQuery('SELECT SUM(c.montant) AS total_credit_amount FROM App\Entity\Credit c')
            ->getSingleScalarResult();

        return (float) $totalAmount;
    }

}