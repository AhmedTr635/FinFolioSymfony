<?php

namespace App\Controller;

use App\Entity\Investissement;
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
            'totalInvestment' => $totalInvestment,
            'totalROIMontant' => $totalROIMontant,
            'percentageROITotal' => $percentageROITotal,
            'totalTax' => $totalTax,
        ]);
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
