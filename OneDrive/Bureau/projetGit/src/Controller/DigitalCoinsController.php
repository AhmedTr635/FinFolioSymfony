<?php

namespace App\Controller;

use App\Entity\DigitalCoins;
use App\Form\DigitalCoinsType;
use App\Repository\DigitalCoinsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
        $form = $this->createForm(DigitalCoinsType::class, $digitalCoin);

        return $this->render('digital_coins/index.html.twig', [
            'digital_coins' => $digitalCoinsRepository->findAll(),
            'digital_coin' => $digitalCoin,
            'form' => $form->createView(), // Pass the form view to the template
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
    #[Route('/buy', name: 'app_digital_coins_buy', methods: ['POST'])]
    public function buy(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Retrieve data from the form
        $montant = $request->request->get('montant');
        $stopLoss = $request->request->get('stopLoss');
        $leverage = $request->request->get('leverage');
        $coinCode = $request->request->get('coin-select');

        // Fetch the last open price of the coin from the API (You need to implement this logic)
        $lastOpenPrice = 60000;

        // Create a new digital coin entity with initial values
        $digitalCoin = new DigitalCoins();
        $digitalCoin->setDateAchat(new \DateTime());
        $digitalCoin->setMontant($montant);
        $digitalCoin->setCode($coinCode);
        $digitalCoin->setTax($montant * 0.08);
        $digitalCoin->setROI(0);
        $digitalCoin->setRecentValue(0);
        $digitalCoin->setPrixAchat($lastOpenPrice); // Set to last open price
        $digitalCoin->setLeverage($leverage);
        $digitalCoin->setStopLoss($stopLoss);

        // Persist the digital coin entity
        $entityManager->persist($digitalCoin);
        $entityManager->flush();

        // Redirect to the index page
        return $this->redirectToRoute('app_digital_coins_index');
    }



}
