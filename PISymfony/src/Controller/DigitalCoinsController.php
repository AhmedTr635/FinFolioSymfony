<?php

namespace App\Controller;

use App\Entity\DigitalCoins;
use App\Form\DigitalCoinsType;
use App\Repository\DigitalCoinsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/digital/coins',name: 'app_digital_coins')]
class DigitalCoinsController extends AbstractController
{
    #[Route('/', name: 'app_digital_coins_index', methods: ['GET'])]
    public function index(DigitalCoinsRepository $digitalCoinsRepository): Response
    {
        $digitalCoin = new DigitalCoins();
        $UserId = 22;
        $form = $this->createForm(DigitalCoinsType::class, $digitalCoin);


        return $this->render('digital_coins/index.html.twig', [
            'digital_coins' => $digitalCoinsRepository->findByUserIdAndDateVenteIsNull($UserId),
            'digital_coin' => $digitalCoin,
            'form' => $form->createView(),
            'digital_coins_sold' => $digitalCoinsRepository->findByUserIdAndDateVenteIsNotNull($UserId),// Pass the form view to the template
        ]);
    }

    #[Route('/new', name: 'app_digital_coins_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $digitalCoin = new DigitalCoins();
        $form = $this->createForm(DigitalCoinsType::class, $digitalCoin);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($digitalCoin);
            $entityManager->flush();

            return $this->redirectToRoute('app_digital_coins_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('digital_coins/new.html.twig', [
            'digital_coin' => $digitalCoin,
            'form' => $form->createView(), // Pass the form view to the template
        ]);
    }

    #[Route('/{id}', name: 'app_digital_coins_show', methods: ['GET'])]
    public function show(DigitalCoins $digitalCoin): Response
    {
        return $this->render('digital_coins/show.html.twig', [
            'digital_coin' => $digitalCoin,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_digital_coins_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, DigitalCoins $digitalCoin, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DigitalCoinsType::class, $digitalCoin);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_digital_coins_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('digital_coins/edit.html.twig', [
            'digital_coin' => $digitalCoin,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_digital_coins_delete', methods: ['POST'])]
    public function delete(Request $request, DigitalCoins $digitalCoin, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$digitalCoin->getId(), $request->request->get('_token'))) {
            $entityManager->remove($digitalCoin);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_digital_coins_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/validate_recent_value', name: 'app_digital_coins_validate_recent_value', methods: ['POST'])]
    public function validateRecentValue(Request $request): JsonResponse
    {
        $inputValue = $request->request->get('recentValue');

        // Perform validation
        // Example: Check if the value is numeric
        $isValid = is_numeric($inputValue);
        $response = [
            'valid' => $isValid,
            'message' => $isValid ? '' : 'Invalid recent value. Please enter a numeric value.'
        ];

        return $this->json($response);
    }

    /* #[Route('/validate-recent-value', name: 'app_digital_coins_validate_recent_value', methods: ['POST'])]

     public function validateRecentValue(Request $request): JsonResponse
     {
         $value = $request->request->get('value');
         // Perform validation
         $isValid = $this->isValidRecentValue($value);

         return new JsonResponse(['valid' => $isValid]);
     }*/

    private function isValidRecentValue($value): bool
    {
        // You can add multiple validation rules here
        // Example: Check if value is not empty, is a number, and within a specific range
        return !empty($value) && is_numeric($value) && ($value > 0 && $value < 1000);
    }
    #[Route('/digital/coins/buy', name: 'app_digital_coins_buy', methods: ['POST'])]
    public function buy(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Retrieve data from the form
        $montant = $request->request->get('montant');
        $stopLoss = $request->request->get('stopLoss');
        $leverage = $request->request->get('leverage');
        $coinCode = $request->request->get('coin-select');
        $prixAchat = $request->request->get('prixAchat');
        $userId = $request->request->get('userId');
        $tax = $request->request->get('tax');

        // Create a new digital coin entity with initial values
        $digitalCoin = new DigitalCoins();
        $digitalCoin->setDateAchat(new \DateTime());
        $digitalCoin->setMontant($montant);
        $digitalCoin->setCode($coinCode);
        $digitalCoin->setTax($tax);
        $digitalCoin->setROI(0);
        $digitalCoin->setRecentValue(0);
        $digitalCoin->setPrixAchat($prixAchat); // Set to last open price
        $digitalCoin->setLeverage($leverage);
        $digitalCoin->setStopLoss($stopLoss);
        $digitalCoin->setUserId($userId);

        // Persist the digital coin entity
        $entityManager->persist($digitalCoin);
        $entityManager->flush();

        // Return a JSON response
        return $this->json(['message' => 'Digital coin created successfully.']);
    }
    #[Route('/fetch-last-open-price/{symbol}', name: 'app_fetch_last_open_price', methods: ['GET'])]
    public function fetchLastOpenPrice(string $symbol): JsonResponse
    {
        // Define the API endpoint URL
        $apiUrl = sprintf('https://api.binance.com/api/v3/klines?symbol=%sUSDT&interval=1d', $symbol);

        // Make a GET request to the API
        $httpClient = HttpClient::create();
        $response = $httpClient->request('GET', $apiUrl);

        // Check if the request was successful
        if ($response->getStatusCode() === 200) {
            $data = $response->toArray();

            // Parse the response data to get the last open price
            $lastOpenPrice = $data[count($data) - 1][1]; // Assuming the open price is at index 1

            // Return the last open price as JSON response
            return $this->json(['last_open_price' => $lastOpenPrice]);
        } else {
            // Return an error response if the request was not successful
            return $this->json(['error' => 'Failed to fetch last open price.']);
        }
    }
    #[Route('/digital/coins/sell', name: 'app_digital_coins_sell', methods: ['POST'])]
    public function sell(Request $request, DigitalCoinsRepository $digitalCoinsRepository, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);

        $digitalCoin = $digitalCoinsRepository->find($data['id']);
        if (!$digitalCoin) {
            return new JsonResponse(['error' => 'Digital coin not found'], Response::HTTP_NOT_FOUND);
        }

        // Convert ROI string to float
        $roi = (float) rtrim($data['roi'], '%');

        $digitalCoin->setROI($roi);
        $digitalCoin->setDateVente(new \DateTime());

        $entityManager->persist($digitalCoin);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Digital coin updated successfully']);
    }

    #[Route('/table1-data', name: 'app_digital_coins_table1_data', methods: ['GET'])]
    public function getTable1Data(DigitalCoinsRepository $digitalCoinsRepository): JsonResponse
    {
        // Fetch data for table 1 from the repository or any other source
        $userId = 22;
        $table1Data = $digitalCoinsRepository->findByUserIdAndDateVenteIsNull($userId); // Example: Fetch data from repository method

        // Return the data as JSON response
        return $this->json($table1Data);
    }

    #[Route('/donut-chart-data', name: 'app_donut_chart_data', methods: ['GET'])]
    public function getDonutChartData(DigitalCoinsRepository $digitalCoinsRepository): JsonResponse
    {
        // Fetch data for donut chart
        $userId = 22;
        $coins = $digitalCoinsRepository->findByUserIdAndDateVenteIsNull($userId);

        // Calculate total montant
        $totalMontant = $digitalCoinsRepository->findTotalMontantWhereDateVenteIsNull();

        // Prepare data for chart
        $chartData = [];
        foreach ($coins as $coin) {
            $percentage = ($coin->getMontant() / $totalMontant) * 100;
            $chartData[] = [
                'label' => $coin->getCode(),
                'value' => $percentage,
            ];
        }

        return $this->json($chartData);
    }

    #[Route('/table2-data', name: 'app_digital_coins_table2_data', methods: ['GET'])]
    public function getTable2Data(DigitalCoinsRepository $digitalCoinsRepository): JsonResponse
    {
        // Fetch data for table 2 from the repository or any other source
        $userId = 22;
        $table2Data = $digitalCoinsRepository->findByUserIdAndDateVenteIsNotNull($userId); // Example: Fetch data from repository method

        // Return the data as JSON response
        return $this->json($table2Data);
    }

    #[Route('/total-montant-where-date-vente-is-null', name: 'app_total_montant_where_date_vente_is_null', methods: ['GET'])]
    public function getTotalMontantWhereDateVenteIsNull(DigitalCoinsRepository $digitalCoinsRepository): JsonResponse
    {
        $totalMontant = $digitalCoinsRepository->findTotalMontantWhereDateVenteIsNull();
        return $this->json(['total_montant' => $totalMontant]);
    }

    #[Route('/total-roi-where-date-vente-is-not-null', name: 'app_total_roi_where_date_vente_is_not_null', methods: ['GET'])]
    public function getTotalRoiWhereDateVenteIsNotNull(DigitalCoinsRepository $digitalCoinsRepository): JsonResponse
    {
        $totalRoi = $digitalCoinsRepository->findTotalRoiWhereDateVenteIsNotNull();
        return $this->json(['total_roi' => $totalRoi]);
    }










}
