<?php

namespace App\Controller;

use App\Entity\Investissement;
use App\Entity\Tax;
use App\Entity\TotalTax;
use App\Repository\InvestissementRepository;
use App\Repository\RealEstateRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/investissement/user')]
class InvestDashUserController extends AbstractController
{
    #[Route('/', name: 'app_investdashuser_index', methods: ['GET'])]
    public function index(RealEstateRepository $realEstateRepository, InvestissementRepository $investissementRepository): Response
    {
        // Get all real estates
        $realEstates = $realEstateRepository->findAll();
        $userId = 22;
        $userInvestments = $investissementRepository->findByUserId($userId);

        // Calculate investments sum for each real estate
        $investmentsSumByRealEstate = [];
        foreach ($realEstates as $realEstate) {
            $realEstateId = $realEstate->getId();
            $investmentsSumByRealEstate[$realEstateId] = $investissementRepository->getSumByRealEstateId($realEstateId);
        }

        return $this->render('invest_dash_user/index.html.twig', [
            'realEstates' => $realEstates,
            'investmentsSumByRealEstate' => $investmentsSumByRealEstate,
            'userInvestments' => $userInvestments,
        ]);
    }
    #[Route('/make_investment/{id}', name: 'app_make_investment', methods: ['POST'])]
    public function makeInvestment(Request $request, EntityManagerInterface $entityManager, RealEstateRepository $realEstateRepository, $id,TaxController $taxC): Response
    {
        // Retrieve the real estate entity
        $realEstate = $realEstateRepository->find($id);

        // Create a new investment
        $investissement = new Investissement();

        // Set the investment details
        $investissement->setDateAchat(new \DateTime());
        $investissement->setPrixAchat($realEstate->getValeur());
        $investissement->setROI($realEstate->getROI());
        $investissement->setReId($realEstate->getId());
        $investissement->setUserId(22);

        // Other fields like montant and tax will be filled by the user in the form
        // Calculate the tax (assuming it's 8% of montant)
        $montant = $request->request->get('montant');
        $tax = $montant * 0.08;
        $investissement->setMontant($montant);
        $investissement->setTax($tax);

        // Save the investment to the database
        $entityManager->persist($investissement);
        $entityManager->flush();
        $tax2 = new Tax();
        $tax2->setMontant($investissement->getMontant() * 0.08);
        $tax2->setType("Investissement");
        $tax2->setOptimisation("Investissement");

        $entityManager->persist($tax2);
        $entityManager->flush();
        $totalTax = $entityManager->getRepository(TotalTax::class)->findOneBy([]);
        if (!$totalTax) {
            $totalTax = new TotalTax();
            $totalTax->setTotal(0);
        }
        $totalTax->setTotal($totalTax->getTotal() + $tax );
        $entityManager->persist($totalTax);
        $entityManager->flush();
        // Redirect the user back to the real estate page
        return $this->redirectToRoute('app_investdashuser_index');
    }
    #[Route('/update-investment', name: 'update_investment', methods: ['POST'])]
    public function updateInvestment(Request $request, InvestissementRepository $investissementRepository): JsonResponse
    {
        // Retrieve data from the request
        $requestData = json_decode($request->getContent(), true);
        $realEstateId = $requestData['realEstateId'];

        // Fetch the updated investment data from the database
        $totalInvestment = $investissementRepository->getTotalInvestmentByRealEstateId($realEstateId);
        $realEstateValue = $investissementRepository->getRealEstateValueById($realEstateId);
        $progressPercentage = $realEstateValue != 0 ? $totalInvestment / $realEstateValue * 100 : 0;

        // Return the updated investment data as JSON response
        return new JsonResponse([
            'totalInvestment' => $totalInvestment,
            'progressPercentage' => $progressPercentage
        ]);
    }








}