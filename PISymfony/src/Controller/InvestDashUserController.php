<?php

namespace App\Controller;

use App\Entity\Investissement;
use App\Repository\InvestissementRepository;
use App\Repository\RealEstateRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/investissement/user')]
class InvestDashUserController extends AbstractController
{
    #[Route('/', name: 'app_investdashuser_index', methods: ['GET'])]
    public function index(RealEstateRepository $realEstateRepository, InvestissementRepository $investissementRepository,PaginatorInterface $paginator,Request $request): Response
    {
        // Get all real estates
        $realEstates = $realEstateRepository->findAll();
        $userId = 22;
        $userInvestments = $investissementRepository->findByUserId($userId);
        $realEstates= $paginator->paginate(
           $realEstates,
            $request->query->getInt('page',1)  ,
            3
        );
        $paginationTemplate = '@KnpPaginator/Pagination/twitter_bootstrap_v4_pagination.html.twig';

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
            'paginationTemplate' => $paginationTemplate,
        ]);
    }
    #[Route('/make_investment/{id}', name: 'app_make_investment', methods: ['POST'])]
    public function makeInvestment(Request $request, EntityManagerInterface $entityManager, RealEstateRepository $realEstateRepository, $id): Response
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

    #[Route('/update-investissement-montant/{id}', name: 'update_investissement_montant', methods: ['POST'])]
    public function updateInvestissementMontant(Request $request, EntityManagerInterface $entityManager, $id): JsonResponse
    {
        // Retrieve the investissement entity by its ID
        $investissement = $entityManager->getRepository(Investissement::class)->find($id);

        if (!$investissement) {
            return new JsonResponse(['error' => 'Investissement not found'], Response::HTTP_NOT_FOUND);
        }

        // Retrieve the new montant value from the request
        $montant = $request->request->get('montant');
        $newMontant = (int)$montant;

        // Update the montant property of the investissement entity
        $investissement->setMontant($newMontant);

        try {
            // Persist the changes to the database
            $entityManager->persist($investissement);
            $entityManager->flush();

            // Return a success response
            return new JsonResponse(['message' => 'Investissement montant updated successfully: ' . $newMontant], Response::HTTP_OK);
        } catch (\Exception $e) {
            // Return an error response if the update fails
            return new JsonResponse(['error' => 'Failed to update investissement montant'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
#[Route('/delete-investment/{id}', name: 'delete_investment', methods: ['POST'])]
    public function deleteInvestment(Request $request, EntityManagerInterface $entityManager, $id): JsonResponse
    {
        // Retrieve the investissement entity by its ID
        $investissement = $entityManager->getRepository(Investissement::class)->find($id);

        if (!$investissement) {
            return new JsonResponse(['error' => 'Investissement not found'], Response::HTTP_NOT_FOUND);
        }

        // Remove the investissement entity
        $entityManager->remove($investissement);

        try {
            // Persist the changes to the database
            $entityManager->flush();
            // Return a success response
            return new JsonResponse(['message' => 'Investissement deleted successfully'], Response::HTTP_OK);
        } catch (\Exception $e) {
            // Return an error response if the deletion fails
            return new JsonResponse(['error' => 'Failed to delete investissement'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
