<?php

namespace App\Controller;
use Psr\Log\LoggerInterface;
use App\Entity\Credit;
use App\Form\Credit1Type;
use App\Repository\CreditRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Repository\UserRepository;


#[Route('/credit')]
class CreditController extends AbstractController
{
    #[Route('/', name: 'app_credit_index', methods: ['GET'])]
    public function index(CreditRepository $creditRepository): Response
    {
        $credits = $creditRepository->findAll(); // Fetch credits from repository

        return $this->render('templateController/listofCredit.html.twig', [
            'credits' => $credits, // Pass credits to the template
        ]);
    }

    #[Route('/CreditRequests', name: 'app_credit_index1', methods: ['GET'])]
    public function index1(CreditRepository $creditRepository): Response
    {
        $credits = $creditRepository->findAll(); // Fetch credits from repository

        return $this->render('templateController/index.html.twig', [
            'credits' => $credits, // Pass credits to the template
        ]);
    }


    #[Route('/new', name: 'app_credit_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $credit = new Credit();


        $form = $this->createForm(Credit1Type::class, $credit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($credit);
            $entityManager->flush();

            return $this->redirectToRoute('app_credit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('templateController/new.html.twig', [
            'credit' => $credit,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_credit_show', methods: ['GET'])]
    public function show(Credit $credit): Response
    {
        return $this->render('templateController/show.html.twig', [
            'credit' => $credit,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_credit_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Credit $credit, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(Credit1Type::class, $credit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_credit_index1', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('templateController/edit.html.twig', [
            'credit' => $credit,

            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_credit_delete', methods: ['POST'])]
    public function delete(Request $request, Credit $credit, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$credit->getId(), $request->request->get('_token'))) {
            $entityManager->remove($credit);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_credit_index1', [], Response::HTTP_SEE_OTHER);
    }

    // Import the Credit entity class if not already imported





    #[Route('/{user_id}/{credit_id}', name: 'app_chat', methods: ['GET'])]
    public function chat($user_id, $credit_id, UserRepository $userRepository): Response
    {
        // Set user_id to 1
        $user1 = $userRepository->find($user_id);

        if (!$user1) {
            throw $this->createNotFoundException('User not found');
        }

        // Fetch the Credit entity based on the provided credit_id
        $credit = $this->getDoctrine()->getRepository(Credit::class)->find($credit_id);

        if (!$credit) {
            throw $this->createNotFoundException('Credit not found');
        }

        // Fetch the second user associated with the Credit entity
        $user2 = $credit->getUserId(); // Assuming you have a method to get the user associated with the credit

        // Log the IDs of the two users


        // Render the template with the alert message
        $content = $this->renderView('templateController/chat.html.twig', [
            'user1' => $user1,
            'user2' => $user2,
            'controller_name' => 'DefaultController',
        ]);

        return new Response($content);
    }













}