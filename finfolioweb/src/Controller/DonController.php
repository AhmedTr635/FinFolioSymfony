<?php

namespace App\Controller;

use App\Entity\Don;
use App\Entity\Evennement;
use App\Form\DonType;
use App\Repository\DonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/don')]
class DonController extends AbstractController
{
    #[Route('/', name: 'app_don_index', methods: ['GET'])]
    public function index(DonRepository $donRepository): Response
    {
        return $this->render('don/index.html.twig', [
            'dons' => $donRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_don_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $don = new Don();
        $form = $this->createForm(DonType::class, $don);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($don);
            $entityManager->flush();

            return $this->redirectToRoute('app_don_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('don/new.html.twig', [
            'don' => $don,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_don_show', methods: ['GET'])]
    public function show(Don $don): Response
    {
        return $this->render('don/show.html.twig', [
            'don' => $don,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_don_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Don $don, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DonType::class, $don);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_don_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('don/edit.html.twig', [
            'don' => $don,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_don_delete', methods: ['POST'])]
    public function delete(Request $request, Don $don, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$don->getId(), $request->request->get('_token'))) {
            $entityManager->remove($don);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_don_index', [], Response::HTTP_SEE_OTHER);
    }


    #[Route('/give-donation/{id}', name: 'app_don_give_donation', methods: ['POST'])]
    public function giveDonation(Request $request, Evennement $evennement): Response
    {
        // Retrieve the submitted donation amount from the request
        $amount = $request->request->get('montant');

        // Create a new Don entity and set its properties
        $donation = new Don();
        $donation->setMontantUser($amount);
        $donation->setEvenementId($evennement);

        // Persist the donation entity to the database
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($donation);
        $entityManager->flush();

        // Optionally, redirect the user to a different page
        return $this->redirectToRoute('app_evennement_index');
    }


    #[Route('/donations', name: 'donations')]
    public function donations(DonRepository $donationRepository): Response
    {
        // Get donations by month from the repository
        $donationsByMonth = $donationRepository->getDonationsByMonth();

        // Calculate total donations
        $totalDonations = array_sum($donationsByMonth['total']);

        // Pass the data to the template
        return $this->render('evennement/admindash.html.twig', [
            'donationsByMonth' => $donationsByMonth,
            'totalDonations' => $totalDonations,
        ]);
    }


}
