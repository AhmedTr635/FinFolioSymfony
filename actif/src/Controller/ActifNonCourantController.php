<?php

namespace App\Controller;

use App\Entity\ActifNonCourant;
use App\Form\ActifNonCourantType;
use App\Repository\ActifNonCourantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/actif/non/courant')]
class ActifNonCourantController extends AbstractController
{
   /* #[Route('/', name: 'app_actif_non_courant_index', methods: ['GET'])]
    public function index(ActifNonCourantRepository $actifNonCourantRepository): Response
    {
        return $this->render('actif_courant/index.html.twig', [
            'actif_non_courants' => $actifNonCourantRepository->findAll(),
        ]);
    }*/

    #[Route('/new', name: 'app_actif_non_courant_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $actifNonCourant = new ActifNonCourant();
        $actifNonCourant->setUserId(106);
        $form = $this->createForm(ActifNonCourantType::class, $actifNonCourant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($actifNonCourant);
            $entityManager->flush();

            return $this->redirectToRoute('app_actif_non_courant_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('actif_non_courant/new.html.twig', [
            'actif_non_courant' => $actifNonCourant,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_actif_non_courant_show', methods: ['GET'])]
    public function show(ActifNonCourant $actifNonCourant): Response
    {
        return $this->render('actif_non_courant/show.html.twig', [
            'actif_non_courant' => $actifNonCourant,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_actif_non_courant_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ActifNonCourant $actifNonCourant, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ActifNonCourantType::class, $actifNonCourant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_actif_non_courant_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('actif_non_courant/edit.html.twig', [
            'actif_non_courant' => $actifNonCourant,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_actif_non_courant_delete', methods: ['POST'])]
    public function delete(Request $request, ActifNonCourant $actifNonCourant, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$actifNonCourant->getId(), $request->request->get('_token'))) {
            $entityManager->remove($actifNonCourant);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_actif_courant_index', [], Response::HTTP_SEE_OTHER);
    }
}
