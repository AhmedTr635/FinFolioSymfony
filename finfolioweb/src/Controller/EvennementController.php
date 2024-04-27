<?php

namespace App\Controller;

use App\Entity\Don;
use App\Entity\Evennement;
use App\Entity\User;
use App\Form\EvennementType;
use App\Repository\DonRepository;
use App\Repository\EvennementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;


#[Route('/event')]
class EvennementController extends AbstractController
{
    /**
     * @throws NonUniqueResultException
     */
    #[Route('/', name: 'app_evennement_index', methods: ['GET'])]
    public function index(EvennementRepository $evennementRepository, Request $request): Response

    {

        dump($request->getLocale());
        // Fetch all events
        $evennements = $evennementRepository->findAll();

        // Fetch upcoming event
        $upcomingEvent = $evennementRepository->findUpcomingEvent();



        // Render the template with both the list of events and the upcoming event
        return $this->render('evennement/index.html.twig', [
            'evennements' => $evennements,
            'upcomingEvent' => $upcomingEvent,


        ]);
    }






    #[Route('/fetch', name: 'app_evennement_fetch', methods: ['GET'])]
    public function fetch(EvennementRepository $evennementRepository): JsonResponse
    {
        // Fetch all events
        $events = $evennementRepository->findAll();

        // Format events for FullCalendar
        $formattedEvents = [];
        foreach ($events as $event) {
            $formattedEvents[] = [
                'title' => $event->getNomEvent(),
                'start' => $event->getDate()->format('Y-m-d'),
                // Add other event properties as needed
            ];
        }

        // Return events as JSON response
        return new JsonResponse($formattedEvents);
    }






    #[Route('/admin', name: 'app_evennement_admin', methods: ['GET'])]
    public function adminsash(EvennementRepository $evennementRepository, DonRepository $donRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $totalDonationsByEventId = $evennementRepository->getTotalDonationsByEventId();
        $totalDonations = $donRepository->calculateTotalDonations();

        $evennement = new Evennement();
        $form = $this->createForm(EvennementType::class, $evennement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {  $imageFile = $form->get('imageData')->getData();
            if ($imageFile) {
                $imageName = md5(uniqid()) . '.' . $imageFile->guessExtension();
                $imageFile->move(
                    $this->getParameter('images_directory'),
                    $imageName
                );
                // Set the image filename in the entity
                $evennement->setImageData('uploads/images/' . $imageName);
            }
            $entityManager->persist($evennement);
            $entityManager->flush();

            return $this->redirectToRoute('app_evennement_index', [], Response::HTTP_SEE_OTHER);
        }

        // Render the template with event data
        return $this->render('evennement/admindash.html.twig', [
            'evennements' => $evennementRepository->findAll(),
            'totalDonationsByEventId' => $totalDonationsByEventId,
            'form' => $form->createView(),
            'totalDonations' => $totalDonations,


        ]);
    }

    #[Route('/new', name: 'app_evennement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $evennement = new Evennement();
        if ($evennement->getDate() === null) {
            $evennement->setDate(new \DateTime());
        }

        $form = $this->createForm(EvennementType::class, $evennement);
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
                $evennement->setImageData('uploads/images/' . $imageName);
            }
            $entityManager->persist($evennement);
            $entityManager->flush();

            return $this->redirectToRoute('app_evennement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('evennement/new.html.twig', [
            'evennement' => $evennement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_evennement_show', methods: ['GET'])]
    public function show(Evennement $evennement): Response
    {
        return $this->render('evennement/show.html.twig', [
            'evennement' => $evennement,
        ]);
    }

//    #[Route('/{id}/edit', name: 'app_evennement_edit', methods: ['GET', 'POST'])]
//    public function edit(Request $request, Evennement $evennement, EntityManagerInterface $entityManager): Response
//    {
//        $form = $this->createForm(EvennementType::class, $evennement);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $entityManager->flush();
//
//            return $this->redirectToRoute('app_evennement_index', [], Response::HTTP_SEE_OTHER);
//        }
//
//        return $this->renderForm('evennement/edit.html.twig', [
//            'evennement' => $evennement,
//            'form' => $form,
//        ]);
//    }


    #[Route('/{id}/edit', name: 'app_evennement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Evennement $evennement, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EvennementType::class, $evennement);
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
                $evennement->setImageData('uploads/images/' . $imageName);
            }
            $entityManager->flush();

            return $this->redirectToRoute('app_evennement_admin', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('evennement/edit.html.twig', [
            'evennement' => $evennement,
            'form' => $form->createView(), // Pass the form variable to the template
        ]);
    }


    #[Route('/{id}', name: 'app_evennement_delete', methods: ['POST'])]
    public function delete(Request $request, Evennement $evennement, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $evennement->getId(), $request->request->get('_token'))) {
            $entityManager->remove($evennement);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_evennement_admin', [], Response::HTTP_SEE_OTHER);
    }


    //progressBar
    #[Route('/evennement/progress-bar/{id}', name: 'evennement_progress_bar')]
    public function progressBar(Evennement $evennement, DonRepository $donRepository): Response
    {
        // Retrieve the total donation amount for the given event
        $totalDonationAmount = $donRepository->getTotalDonationForEvent();

        $montant = $evennement->getMontant();

        // Calculate the progress percentage
        $percentage = $totalDonationAmount / $montant * 100;

        // Render a template fragment containing the progress bar
        return $this->render('evennement/progress_bar.html.twig', [
            'percentage' => $percentage,
        ]);
    }

    #[Route('/my-donated-events', name: 'my_donated_events')]
    public function myDonatedEvents(): Response
    {
        // Fetch the user by ID (replace '118' with the actual user ID)
        $userId = 118;
        $user = $this->getDoctrine()->getRepository(User::class)->find($userId);

        // Get the donations made by the user
        $donations = $user->getDons();

        // Extract the event IDs from the donations
        $eventIds = [];
        foreach ($donations as $donation) {
            $eventId = $donation->getEvenementId()->getId();
            $eventIds[] = $eventId;

        }

        // Fetch the events associated with the event IDs
        $events = $this->getDoctrine()->getRepository(Evennement::class)->findBy(['id' => $eventIds]);

        // Render the template to display the list of events
        return $this->render('evennement/index.html.twig', [
            'events' => $events,
        ]);
    }



}
