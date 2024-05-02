<?php

namespace App\Controller;

use App\Entity\Investissement;
use App\Entity\RealEstate;
use App\Repository\InvestissementRepository;
use App\Repository\RealEstateRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InvestUserDashController extends AbstractController
{
    #[Route('/invest/user/dash', name: 'app_invest_user_dash')]
    public function index(InvestissementRepository $investissementRepository , RealEstateRepository $realEstateRepository,EntityManagerInterface $entityManager): Response
    {
        $userId=22;
        $totalInvestment = $entityManager->getRepository(Investissement::class)->getTotalInvestmentByUserId($userId);
        $totalROIMontant = $entityManager->getRepository(Investissement::class)->getTotalRoiInvestmentsByUserId($userId);
        $percentageROITotal = ($totalROIMontant / $totalInvestment) * 100;
        $totalTax = $entityManager->getRepository(Investissement::class)->totalTaxByUserId($userId);

        return $this->render('invest_dash_user/investUserDash.html.twig', [
            'investissements' => $investissementRepository->findByUserId(22),
            'real_estates' => $realEstateRepository->findAll(),
            'realEstateRepository' => $realEstateRepository,
            'investissementRepository' => $investissementRepository,
            'totalInvestment' => $totalInvestment,
            'totalROIMontant' => $totalROIMontant,
            'percentageROITotal' => $percentageROITotal,
            'totalTax' => $totalTax,
        ]);
    }
    #[Route('/investissement/details/{id}', name: 'app_investissement_user_details', methods: ['GET'])]
    public function details($id,InvestissementRepository $investissementRepository , RealEstateRepository $realEstateRepository,EntityManagerInterface $entityManager): JsonResponse
    {
        // Fetch investissement details based on the provided ID
        $investissement = $entityManager->getRepository(Investissement::class)->find($id);
        $realEstate = $entityManager->getRepository(RealEstate::class)->findRealEstateById($investissement->getReId());


        if (!$investissement) {
            // Return a 404 response if investissement is not found
            return new JsonResponse(['error' => 'Investissement not found'], Response::HTTP_NOT_FOUND);
        }

        // Serialize the investissement details into JSON format
        $data = [
            'id' => $investissement->getId(),
            'montant' => $investissement->getMontant(),
            'reID' => $investissement->getReId(),
            'roi' => $realEstate->getROI(),
            'name' => $realEstate->getName(),
            'realEstate' => $realEstate,

            // Add more investissement details here as needed
        ];

        // Return the investissement details as JSON response
        return new JsonResponse($data);
    }
    /*#[Route('/{id}', name: 'app_investissement_delete', methods: ['POST'])]
    public function delete(Request $request, Investissement $investissement, EntityManagerInterface $entityManager): JsonResponse
    {
        if ($this->isCsrfTokenValid('delete'.$investissement->getId(), $request->request->get('_token'))) {
            $entityManager->remove($investissement);
            $entityManager->flush();

            // Return JSON response indicating successful deletion
            return new JsonResponse(['message' => 'Investissement deleted successfully']);
        }

        // Return JSON response for invalid CSRF token
        return new JsonResponse(['error' => 'Invalid CSRF token'], Response::HTTP_BAD_REQUEST);
    }*/



}
