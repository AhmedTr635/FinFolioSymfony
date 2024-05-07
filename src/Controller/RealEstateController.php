<?php

namespace App\Controller;

use App\Entity\RealEstate;
use App\Form\RealEstateType;
use App\Repository\InvestissementRepository;
use App\Repository\RealEstateRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/real/estate')]
class RealEstateController extends AbstractController
{
    #[Route('/', name: 'app_real_estate_index', methods: ['GET'])]
    public function index(RealEstateRepository $realEstateRepository): Response
    {
        return $this->render('real_estate/index.html.twig', [
            'real_estates' => $realEstateRepository->findAll(),
        ]);
    }
    #[Route('/real/estate/new', name: 'app_realEstate_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $realEstate = new RealEstate();
        $form = $this->createForm(RealEstateType::class, $realEstate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageData')->getData();
            if ($imageFile) {
                $imageName = md5(uniqid()) . '.' . $imageFile->guessExtension();
                $imageFile->move(
                    $this->getParameter('images_directory'),
                    $imageName
                );
                // Set the image filename in the entity
                $realEstate->setImageData('uploads/images/' . $imageName);
            }
            $entityManager->persist($realEstate);
            $entityManager->flush();

            return $this->redirectToRoute('app_real_estate_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('real_estate/new.html.twig', [
            'real_estate' => $realEstate,
            'form' => $form->createView(),
        ]);
    }

   /* #[Route('/new', name: 'app_real_estate_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $realEstate = new RealEstate();
        $form = $this->createForm(RealEstateType::class, $realEstate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageData')->getData();
            if ($imageFile) {
                $imageName = md5(uniqid()).'.'.$imageFile->guessExtension();
                $imageFile->move(
                    $this->getParameter('images_directory'),
                    $imageName
                );
                // Set the image filename in the entity
                $realEstate->setImageData('uploads/images/'.$imageName);
            }
            $entityManager->persist($realEstate);
            $entityManager->flush();

            return $this->redirectToRoute('app_real_estate_index', [], Response::HTTP_SEE_OTHER);
        }

        /*return $this->renderForm('real_estate/real_estate_form_custom.html.twig', [
            'real_estate' => $realEstate,
            'form' => $form,
        ]);*/
        /*return $this->render('real_estate/real_estate_form_custom.html.twig', [
            'form' => $form->createView(),
        ]);

    }*/

    #[Route('/{id}', name: 'app_real_estate_show', methods: ['GET'])]
    public function show(RealEstate $realEstate , InvestissementRepository $investissementRepository): Response
    {
       $invtot = $investissementRepository->getTotalInvestmentByRealEstateId($realEstate->getId());

        return $this->render('real_estate/show.html.twig', [
            'real_estate' => $realEstate,
            'invtot' => $invtot,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_real_estate_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, RealEstate $realEstate, EntityManagerInterface $entityManager, InvestissementRepository $investissementRepository): Response
    {
        $form = $this->createForm(RealEstateType::class, $realEstate);


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle file upload
            $imageFile = $form->get('imageData')->getData();
            if ($imageFile) {
                $imageName = md5(uniqid()).'.'.$imageFile->guessExtension();
                $imageFile->move(
                    $this->getParameter('images_directory'),
                    $imageName
                );
                // Set the image filename in the entity
                $realEstate->setImageData('uploads/images/'.$imageName);
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_real_estate_index', [], Response::HTTP_SEE_OTHER);
        }

        $invtot = $investissementRepository->getTotalInvestmentByRealEstateId($realEstate->getId());

        return $this->renderForm('real_estate/edit.html.twig', [
            'real_estate' => $realEstate,
            'form' => $form,
            'invtot' => $invtot,

        ]);

    }


    #[Route('/{id}', name: 'app_real_estate_delete', methods: ['POST'])]
    public function delete(Request $request, RealEstate $realEstate, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$realEstate->getId(), $request->request->get('_token'))) {
            $entityManager->remove($realEstate);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_real_estate_index', [], Response::HTTP_SEE_OTHER);
    }
    // Add this method to your RealEstateController

    #[Route('/{id}/click', name: 'app_real_estate_click', methods: ['POST'])]
    public function incrementClickCount(int $id, RealEstateRepository $realEstateRepository, EntityManagerInterface $entityManager): Response
    {
        $realEstate = $realEstateRepository->find($id);

        if (!$realEstate) {
            throw $this->createNotFoundException('The real estate does not exist');
        }

        $realEstate->setNbrclick($realEstate->getNbrclick() + 1);
        $entityManager->flush();

        return $this->json(['message' => 'Click count updated successfully']);
    }
}
