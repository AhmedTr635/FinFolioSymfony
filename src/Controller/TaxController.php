<?php

namespace App\Controller;

use App\Entity\Tax;
use App\Form\TaxType;
use App\Repository\TaxRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/tax')]
class TaxController extends AbstractController
{
    #[Route('/', name: 'app_tax_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager,TaxRepository $taxRepository): Response
    {
        $taxByType = $taxRepository->getExpensesByTaxType();
        $sommeTax=$this->sommeTax();

        $labels = [];
        $data1 = [];
        foreach ($taxByType['tax_type'] as $type) {
            $labels[] = $type; // Month names
        }
        foreach ($taxByType['total'] as $total) {
            $data1[] = ($total * 100)/$sommeTax; // Total expenses for each month
        }

        // Create your form
        $form = $this->createForm(TaxType::class);
        $data = $taxRepository->findAll(); // Fetch your data (e.g., from Doctrine)
        $chartData = [
            'labels' => json_encode($labels), // Convert labels array to JSON
            'data' => json_encode($data1) // Convert data array to JSON
        ];
        ///
        $taxes = $taxRepository->findAll();

        // Initialize an array to store taxes grouped by type
        $taxesByType = [];

        // Iterate through each tax and group them by type
        foreach ($taxes as $tax) {
            $type = $tax->getType();
            if (!isset($taxesByType[$type])) {
                // If type is not yet in the array, initialize it
                $taxesByType[$type] = [];
            }
            // Add the tax to the corresponding type
            $taxesByType[$type][] = $tax;
        }

        // Calculate the sum of taxes for each type
        $sumsByType = [];
        foreach ($taxesByType as $type => $taxList) {
            $sum = 0;
            foreach ($taxList as $tax) {
                $sum += $tax->getMontant();
            }
            $sumsByType[$type] = $sum;
        }
        // Calculate total montant

        // Calculate total by month

//        $taxes = $entityManager->getRepository(Tax::class)->findAll();
//
//
//        // Analyser les données
//        $stats = [];
//        foreach ($taxes as $tax) {
//            $type = $tax->getType();
//            $amount = $tax->getMontant();
//
//            if (!isset($stats[$type])) {
//                $stats[$type] = 0;
//            }
//
//            $stats[$type] += $amount;
//        }
//
//        // Préparer les données pour le graphique
//        $chartData = [
//            'labels' => array_keys($stats),
//            'data' => array_values($stats)
//        ];

        return $this->render('tax/index.html.twig', [
            'taxes' => $taxRepository->findAll(),
            'sommeTax' => $sommeTax,
            'chartData' => $chartData,// Pass the chart data directly
            'taxesByType' => $taxesByType,
            'sumsByType' => $sumsByType,


        ]);
    }

    #[Route('/new', name: 'app_tax_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $tax = new Tax();
        $form = $this->createForm(TaxType::class, $tax);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($tax);
            $entityManager->flush();

            return $this->redirectToRoute('app_tax_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('tax/new.html.twig', [
            'tax' => $tax,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_tax_show', methods: ['GET'])]
    public function show(Tax $tax): Response
    {
        return $this->render('tax/show.html.twig', [
            'tax' => $tax,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_tax_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Tax $tax, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TaxType::class, $tax);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_tax_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('tax/edit.html.twig', [
            'tax' => $tax,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_tax_delete', methods: ['POST'])]
    public function delete(Request $request, Tax $tax, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tax->getId(), $request->request->get('_token'))) {
            $entityManager->remove($tax);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_tax_index', [], Response::HTTP_SEE_OTHER);
    }
    public function sommeTax(): float
    {
        $repository = $this->getDoctrine()->getRepository(Tax::class);
        $taxes = $repository->findAll();

        // Calculer la somme totale des montants de toutes les dépenses
        $totalMontantTax = 0;
        foreach ($taxes as $tax) {
            $totalMontantTax += $tax->getMontant();
        }

        return $totalMontantTax;
    }
}
