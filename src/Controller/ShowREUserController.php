<?php

namespace App\Controller;

use App\Entity\RealEstate;
use App\Form\RealEstateType;
use App\Repository\RealEstateRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/real/estate')]
class ShowREUserController extends AbstractController
{
    #[Route('/show/r/e/user', name: 'app_show_r_e_user')]
    public function index(): Response
    {
        return $this->render('show_re_user/index.html.twig', [
            'controller_name' => 'ShowREUserController',
        ]);
    }

    #[Route('/new', name: 'app_real_estate_new', methods: ['GET', 'POST'])]
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

    #[Route('user/re/{id}', name: 'app_real_estate_user_show', methods: ['GET'])]
    public function show(RealEstate $realEstate): Response
    {
        $latitude = $realEstate->getLatitude();
        $longitude = $realEstate->getLongitude();

        // Check if latitude and longitude are available
        if ($latitude !== null && $longitude !== null) {
            // Generate Google Maps link
            $googleMapsLink = sprintf(
                'https://www.google.com/maps/search/?api=1&query=%f,%f',
                $latitude,
                $longitude
            );
        } else {
            // If latitude or longitude is null, set Google Maps link to null
            $googleMapsLink = null;
        }

        return $this->render('show_re_user/index.html.twig', [
            'real_estate' => $realEstate,
            'googleMapsLink' => $googleMapsLink,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_real_estate_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, RealEstate $realEstate, EntityManagerInterface $entityManager): Response
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

        return $this->renderForm('real_estate/edit.html.twig', [
            'real_estate' => $realEstate,
            'form' => $form,
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
}
