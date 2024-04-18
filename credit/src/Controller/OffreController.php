<?php

namespace App\Controller;

use App\Entity\Credit;
use App\Entity\Offre;
use App\Form\Credit1Type;
use App\Form\Offre1Type;
use App\Repository\CreditRepository;
use App\Repository\OffreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\NumberType; // Import NumberType for montant and interet fields


#[Route('/offre')]
class OffreController extends AbstractController
{
    #[Route('/', name: 'app_offre_index', methods: ['GET'])]
    public function index(OffreRepository $offreRepository): Response
    {
        return $this->render('offre/myoffres.html.twig', [
            'offres' => $offreRepository->findAll(),
        ]);
    }


    #[Route('/new', name: 'app_offre_new', methods: ['POST'])]
    public function new(Request $request): Response
    {
        // Retrieve data from request
        $data = json_decode($request->getContent(), true);

        // Set default values if data is null or missing
        $montant = $data['montant'] ?? 10; // Default to 10 if missing
        $interet = $data['interet'] ?? 10; // Default to 10 if missing
        $credit_id = $data['credit_id']; // Assuming credit_id is always present in the request
        $user_id = $data['user_id']; // Assuming user_id is always present in the request

        // Execute SQL query to insert new offer into database
        $conn = $this->getDoctrine()->getConnection();
        $sql = 'INSERT INTO offre (montant, interet, credit_id, user_id) VALUES (:montant, :interet, :credit_id, :user_id)';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['montant' => $montant, 'interet' => $interet, 'credit_id' => $credit_id, 'user_id' => $user_id]);

        $alertHtml = '<div class="alert alert-success alert-dismissible" role="alert">';
        $alertHtml .= '<h6 class="alert-heading d-flex align-items-center mb-1">Well done :)</h6>';
        $alertHtml .= '<p class="mb-0">Offer added successfully.</p>';
        $alertHtml .= '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
        $alertHtml .= '</div>';
        return $this->json(['message' => 'Offer added successfully']);
    }


    #[Route('/OffreRequests', name: 'app_credit_index2', methods: ['GET'])]
    public function index1(OffreRepository $offreRepository): Response
    {
        $offres = $offreRepository->findAll(); // Fetch credits from repository

        return $this->render('offre/myoffres.html.twig', [
            'offres' => $offres, // Pass credits to the template
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
        if ($this->isCsrfTokenValid('delete'.$offre->getId(), $request->request->get('_token'))) {
            $entityManager->remove($offre);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_offre_index', [], Response::HTTP_SEE_OTHER);
    }
}
