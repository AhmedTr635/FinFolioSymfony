<?php

namespace App\Controller;

use App\Entity\RealEstate;
use App\Form\InvestissementType;
use App\Repository\RealEstateRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Investissement;
class TemplateTestController extends AbstractController
{
    #[Route('/template/test', name: 'app_template_test')]
    #[Route('/real_estate_page', name: 'app_real_estate_page', methods: ['GET'])]
    public function realEstatePage(RealEstateRepository $realEstateRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Fetch real estate data
        $realEstates = $realEstateRepository->findAll();

        // Create a new instance of the Investissement entity
        $investissement = new Investissement();

        // Create the form
        $form = $this->createForm(InvestissementType::class, $investissement);

        // Handle form submission
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Persist the investissement to the database
            $entityManager->persist($investissement);
            $entityManager->flush();

            // Redirect to the same page after successful submission
            return $this->redirectToRoute('app_template_test');
        }

        // Render the template with the form
        $realEstate = new RealEstate();
        return $this->render('template_test/index.html.twig', [
            'realEstates' => $realEstates,
            'form' => $form->createView(), // Pass the form to the template
            'real_estate' => $realEstate,
        ]);
    }
}
