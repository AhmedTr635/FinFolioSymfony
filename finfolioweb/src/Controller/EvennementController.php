<?php

namespace App\Controller;

use App\Entity\Don;
use App\Entity\Evennement;

use App\Entity\Rating;
use App\Entity\User;
use App\Form\EvennementType;

use App\Form\RatingType;
use App\Repository\DonRepository;
use App\Repository\EvennementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Knp\Component\Pager\PaginatorInterface;
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
    public function index(PaginatorInterface $paginator,EvennementRepository $evennementRepository, Request $request): Response

    {

        // Fetch all events
        $evennements = $evennementRepository->findAll();

        // Fetch upcoming event

        $upcomingEvent = $evennementRepository->findUpcomingEvent();

        $neededAmount = $request->query->get('neededAmount');
        $collectedAmount = $request->query->get('collectedAmount');



        $evennements= $paginator->paginate(
            $evennements,
            $request->query->getInt('page',1)  ,
            6
        );
        $paginationTemplate = '@KnpPaginator/Pagination/twitter_bootstrap_v4_pagination.html.twig';

        // Render the template with both the list of events and the upcoming event
        return $this->render('evennement/index.html.twig', [

            'paginationTemplate' => $paginationTemplate,
            'evennements' => $evennements,
            'upcomingEvent' => $upcomingEvent,
            'neededAmount' => $neededAmount,
            'collectedAmount' => $collectedAmount,



        ]);
    }







    #[Route('/fetch', name: 'app_evennement_fetch', methods: ['GET'])]
    public function fetch(EvennementRepository $evennementRepository): JsonResponse
    {
        // Fetch the user from the database (replace '116' with the desired user ID)
        $userId = 116;
        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository(User::class)->find($userId);

        // Fetch the user's donations
        $donations = $user->getDons();

        // Initialize an empty array to store event IDs
        $eventIds = [];

        // Extract event IDs from the user's donations
        foreach ($donations as $donation) {
            $eventIds[] = $donation->getEvenementId()->getId();
        }

        // Fetch events associated with the extracted event IDs
        $events = $evennementRepository->findBy(['id' => $eventIds]);

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
    public function adminsash(PaginatorInterface $paginator,EvennementRepository $evennementRepository, DonRepository $donRepository, Request $request, EntityManagerInterface $entityManager): Response
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

       $evennements= $evennementRepository->findAll();
        $evennements= $paginator->paginate(
            $evennements,
            $request->query->getInt('page',1)  ,
            4
        );
        $paginationTemplate = '@KnpPaginator/Pagination/twitter_bootstrap_v4_pagination.html.twig';

        // Render the template with event data
        return $this->render('evennement/admindash.html.twig', [
            'paginationTemplate' => $paginationTemplate,
            'evennements' => $evennements,
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

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    #[Route('/{id}', name: 'app_evennement_show', methods: ['GET'])]
    public function show(Evennement $evennement, DonRepository $donRepo): Response
    {

        // If event is not found, handle accordingly (e.g., show an error message)
        if (!$evennement) {
            throw $this->createNotFoundException('Event not found');
        }

        // Get the total amount needed from the Evennement entity
        $totalAmountNeeded = $evennement->getMontant(); // Assuming getMontant() is a method in your Evennement entity

        // Get the total sum of montantuser for the event using repository function
        $totalMontant = $donRepo->getTotalDonationForEvent($evennement);


   $donation = $donRepo->findAll();
        $latitude = $evennement->getLatitude();
        $longitude = $evennement->getLongitude();

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


        return $this->render('evennement/show.html.twig', [
            'donation' => $donation,
            'evennement' => $evennement,
            'googleMapsLink' => $googleMapsLink,
            'totalMontant' => $totalMontant,
            'totalAmountNeeded' => $totalAmountNeeded,
        ]);
    }






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


//    #[Route('/my-donated-events', name: 'my_donated_events')]
//    public function myDonatedEvents(): Response
//    {
//        $userId = 116;
//        $entityManager = $this->getDoctrine()->getManager();
//        $user = $entityManager->getRepository(User::class)->find($userId);
//
//        // Get the donations made by the user
//        $donations = $user->getDons();
//
//        // Extract the event IDs from the donations
//        $eventIds = [];
//        foreach ($donations as $donation) {
//            $eventId = $donation->getEvenementId()->getId();
//            $eventIds[] = $eventId;
//
//        }
//
//        // Fetch the events associated with the event IDs
//        $events = $this->getDoctrine()->getRepository(Evennement::class)->findBy(['id' => $eventIds]);
//
//
//
//        // Render the template to display the list of events
//        return $this->render('evennement/index.html.twig', [
//            'events' => $events,
//
//        ]);
//    }



}
