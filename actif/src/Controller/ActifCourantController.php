<?php

namespace App\Controller;

use App\Entity\ActifCourant;
use App\Form\ActifCourantType;
use App\Repository\ActifCourantRepository;
use App\Repository\ActifNonCourantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/actif/courant')]
class ActifCourantController extends AbstractController
{
    #[Route('/', name: 'app_actif_courant_index', methods: ['GET'])]
    public function index(ActifCourantRepository $actifCourantRepository,ActifNonCourantRepository $actifNonCourantRepository): Response
    {
        return $this->render('actif_courant/index.html.twig', [
            'actif_courants' => $actifCourantRepository->findAll(),
            'actif_non_courants' => $actifNonCourantRepository->findAll(),
        ]);
    }
    #[Route('/psdf', name: 'test', methods: ['GET'])]
    public function index2(ActifCourantRepository $actifCourantRepository,ActifNonCourantRepository $actifNonCourantRepository, OffreRepository $offreRepository,DepenseRepository $depenseRepository): Response
    {



        return $this->render('actif_courant/pdf_template.html.twig', [
            'actif_courants' => $actifCourantRepository->findAll(),
            'actif_non_courants' => $actifNonCourantRepository->findAll(),
            'offres'=>$offreRepository->findAll(),
            'depenses'=>$depenseRepository->findAll(),
        ]);
    }
    #[Route('/new', name: 'app_actif_courant_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {

        $actifCourant = new ActifCourant();
        $actifCourant->setUserId(106);

        $form = $this->createForm(ActifCourantType::class, $actifCourant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($actifCourant);
            $entityManager->flush();

            return $this->redirectToRoute('app_actif_courant_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('actif_courant/new.html.twig', [
            'actif_courant' => $actifCourant,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_actif_courant_show', methods: ['GET'])]
    public function show(ActifCourant $actifCourant): Response
    {
        return $this->render('actif_courant/show.html.twig', [
            'actif_courant' => $actifCourant,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_actif_courant_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ActifCourant $actifCourant, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ActifCourantType::class, $actifCourant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_actif_courant_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('actif_courant/edit.html.twig', [
            'actif_courant' => $actifCourant,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_actif_courant_delete', methods: ['POST'])]
    public function delete(Request $request, ActifCourant $actifCourant, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$actifCourant->getId(), $request->request->get('_token'))) {
            $entityManager->remove($actifCourant);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_actif_courant_index', [], Response::HTTP_SEE_OTHER);
    }
}
