<?php

namespace App\Controller;

use App\Entity\Investissement;
use App\Entity\RealEstate;
use App\Form\InvestissementType;
use App\Repository\InvestissementRepository;
use App\Repository\RealEstateRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/investissement')]
class InvestissementController extends AbstractController
{
    #[Route('/', name: 'app_investissement_index', methods: ['GET'])]
    public function index(InvestissementRepository $investissementRepository): Response
    {
        return $this->render('investissement/index.html.twig', [
            'investissements' => $investissementRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_investissement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $investissement = new Investissement();
        $form = $this->createForm(InvestissementType::class, $investissement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($investissement);
            $entityManager->flush();

            return $this->redirectToRoute('app_investissement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('investissement/new.html.twig', [
            'investissement' => $investissement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_investissement_show', methods: ['GET'])]
    public function show(Investissement $investissement): Response
    {
        return $this->render('investissement/show.html.twig', [
            'investissement' => $investissement,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_investissement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Investissement $investissement, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(InvestissementType::class, $investissement);
        $form->handleRequest($request);
        $realEstate = $entityManager->getRepository(RealEstate::class)->findRealEstateById($investissement->getReId());

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_investissement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('investissement/edit.html.twig', [
            'investissement' => $investissement,
            'form' => $form,
            'real_estate' => $realEstate,
        ]);
    }


    #[Route('/{id}', name: 'app_investissement_delete', methods: ['POST'])]
    public function delete(Request $request, Investissement $investissement, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$investissement->getId(), $request->request->get('_token'))) {
            $entityManager->remove($investissement);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_invest_user_dash', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/real_estate_page', name: 'app_real_estate_page', methods: ['GET'])]
    public function realEstatePage(RealEstateRepository $realEstateRepository): Response
    {
        $realEstates = $realEstateRepository->findAll();

        return $this->render('dashInvestUser.html.twig', [
            'realEstates' => $realEstates,
        ]);
    }







}
