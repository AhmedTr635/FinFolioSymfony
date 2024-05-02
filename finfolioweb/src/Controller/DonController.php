<?php

namespace App\Controller;

use App\Entity\Don;
use App\Entity\Evennement;
use App\Entity\User;
use App\Form\DonType;
use App\Repository\DonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mime\Email;

#[Route('/don')]
class DonController extends AbstractController
{
    #[Route('/', name: 'app_don_index', methods: ['GET'])]
    public function index(DonRepository $donRepository): Response
    {
        return $this->render('don/index.html.twig', [
            'dons' => $donRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_don_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $don = new Don();
        $form = $this->createForm(DonType::class, $don);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($don);
            $entityManager->flush();

            return $this->redirectToRoute('app_don_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('don/new.html.twig', [
            'don' => $don,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_don_show', methods: ['GET'])]
    public function show(Don $don): Response
    {
        return $this->render('don/show.html.twig', [
            'don' => $don,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_don_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Don $don, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DonType::class, $don);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_don_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('don/edit.html.twig', [
            'don' => $don,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_don_delete', methods: ['POST'])]
    public function delete(Request $request, Don $don, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$don->getId(), $request->request->get('_token'))) {
            $entityManager->remove($don);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_don_index', [], Response::HTTP_SEE_OTHER);
    }


    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/give-donation/{id}', name: 'app_don_give_donation', methods: ['POST'])]
    public function giveDonation(Request $request, Evennement $evennement, MailerInterface $mailer): Response
    {
        // Retrieve the submitted donation amount from the request
        $amount = $request->request->get('montant');

        // Get the user by ID (replace 118 with the ID you choose)
        $userId = 116;
        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository(User::class)->find($userId);

        // Create a new Don entity and set its properties
        $donation = new Don();
        $donation->setMontantUser($amount);
        $donation->setEvenementId($evennement);

        // Set the user for the donation
        $donation->setUserId($user);

        // Persist the donation entity to the database
        $entityManager->persist($donation);
        $entityManager->flush();

        $transport = Transport::fromDsn('smtp://finfoliofinfolio@gmail.com:txzoffvmvmoiuyzw@smtp.gmail.com:587');

// Create a Mailer object
        $mailer = new Mailer($transport);
        // Send the donation confirmation email to the user
        $email = (new Email())
            ->from('finfoliofinfolio@gmail.com') // Replace with your organization's email address
            ->to($user->getEmail())
            ->subject('Thank You for Your Donation!')
            ->html($this->renderView(
                'don/donation_confirmation_email.html.twig',
                ['user' => $user, 'donation' => $donation, 'evennement' => $evennement]
            ));



        $mailer->send($email);

        // Optionally, redirect the user to a different page
        return $this->redirectToRoute('app_evennement_index');
    }







    #[Route('/donations', name: 'donations')]
    public function donations(DonRepository $donationRepository): Response
    {
        // Get donations by month from the repository
        $donationsByMonth = $donationRepository->getDonationsByMonth();

        // Calculate total donations
        $totalDonations = array_sum($donationsByMonth['total']);

        // Pass the data to the template
        return $this->render('evennement/admindash.html.twig', [
            'donationsByMonth' => $donationsByMonth,
            'totalDonations' => $totalDonations,
        ]);
    }


    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    #[Route('/progress', name: 'progress')]

    public function yourAction($eventId, Evennement $evennement, DonRepository $donRepository): Response
    {
        // If event is not found, handle accordingly (e.g., show an error message)
        if (!$evennement) {
            throw $this->createNotFoundException('Event not found');
        }

        // Get the total amount needed from the Evennement entity
        $totalAmountNeeded = $evennement->getMontant(); // Assuming getMontant() is a method in your Evennement entity

        // Get the total sum of montantuser for the event using repository function
        $totalMontant = $donRepository->getTotalMontantForEvennement($evennement);

        // Render the Twig template and pass the collected amount and total amount needed to it
        return $this->render('evennement/show.html.twig', [
            'totalMontant' => $totalMontant,
            'totalAmountNeeded' => $totalAmountNeeded,
        ]);
    }
}
