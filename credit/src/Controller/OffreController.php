<?php

namespace App\Controller;

use App\Entity\Credit;
use App\Entity\Offre;
use App\Entity\User;
use App\Form\Credit1Type;
use App\Form\Offre1Type;
use App\Repository\CreditRepository;
use App\Repository\OffreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\NumberType; // Import NumberType for montant and interet fields


#[Route('/offre')]
class OffreController extends AbstractController
{
    #[Route('/myoffres', name: 'app_offre_index', methods: ['GET'])]
    public function index(OffreRepository $offreRepository): Response
    {
        return $this->render('offre/myoffres.html.twig', [
            'offres' => $offreRepository->findAll(),
        ]);
    }


    #[Route('/new', name: 'app_offre_new', methods: ['POST'])]
    public function new(Request $request): Response
    {

        $data = json_decode($request->getContent(), true);


        $montant = $data['montant'] ?? 10;
        $interet = $data['interet'] ?? 10;
        $credit_id = $data['credit_id'];
        $user_id = $data['user_id'];


        $conn = $this->getDoctrine()->getConnection();
        $sql = 'INSERT INTO offre (montant, interet, credit_id, user_id) VALUES (:montant, :interet, :credit_id, :user_id)';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['montant' => $montant, 'interet' => $interet, 'credit_id' => $credit_id, 'user_id' => $user_id]);

        $alertHtml = '<div class="alert alert-success alert-dismissible" role="alert">';
        $alertHtml .= '<h6 class="alert-heading d-flex align-items-center mb-1">Well done :)</h6>';
        $alertHtml .= '<p class="mb-0">Offer added successfully.</p>';
        $alertHtml .= '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
        $alertHtml .= '</div>';
//        return $this->json(['message' => 'Offer added successfully']);
        return new Response($alertHtml);
    }


    #[Route('/OffreRequests', name: 'app_credit_index2', methods: ['GET'])]
    public function index1(OffreRepository $offreRepository): Response
    {
        // Retrieve the User entity from the database based on the provided ID (106)
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

        // Fetch offres from repository
        $offres = $offreRepository->findAll();
        $solde = number_format($solde, 2, '.', ',');

        return $this->render('offre/seeOffres.html.twig', [
            'offres' => $offres,
            'solde' => $solde, // Pass the solde to the template
        ]);
    }

    #[Route('/{id}/edit', name: 'app_offre_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Offre $offre, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(offre1Type::class, $offre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_credit_index2', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('offre/edit.html.twig', [
            'offre' => $offre,

            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_offre_delete', methods: ['POST'])]
    public function delete(Request $request, Offre $offre, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $offre->getId(), $request->request->get('_token'))) {
            $entityManager->remove($offre);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_offre_index', [], Response::HTTP_SEE_OTHER);
    }





    #[Route('/accept-offer/{offerId}', name: 'accept_offer', methods: ['GET'])]
    public function acceptOffer(int $offerId, EntityManagerInterface $entityManager): Response
    {
        // Retrieve the offer entity by its ID
        $offer = $this->getDoctrine()->getRepository(Offre::class)->find($offerId);

        if (!$offer) {
            throw $this->createNotFoundException('Offer not found');
        }

        // Retrieve the user associated with the offer
        $user = $offer->getUserId();

        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        // Check if the user has enough credit
        if ($user->getNbcredit() <= 0) {
            // If the user has no credits left, return a response with an alert message
            $alertMessage = 'You have insufficient credits to accept this offer.';
            $this->addFlash('warning', $alertMessage); // This adds a flash message for display

            // Redirect the user to a page (you may replace this with your desired route)
            return $this->redirectToRoute('app_credit_index2');
        }

        // Update the user's balance by adding the offer amount
        $solde = $user->getSolde() ?? 0; // Get current solde or set to 0 if null
        $newSolde = $solde + $offer->getMontant();
        $user->setSolde($newSolde);

        // Decrease the number of credits by 1
        $user->setNbcredit($user->getNbcredit() - 1);

        // Save the updated user entity
        $entityManager->persist($user);
        $entityManager->flush();

        // Optionally, redirect the user to a success page or render a success message
        return $this->redirectToRoute('app_credit_index2');
    }


}