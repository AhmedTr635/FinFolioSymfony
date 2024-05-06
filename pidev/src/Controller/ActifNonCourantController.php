<?php

namespace App\Controller;

use App\Entity\ActifNonCourant;
use App\Form\ActifNonCourantType;
use App\Repository\ActifNonCourantRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Snappy\Pdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/actif/non/courant')]
class ActifNonCourantController extends AbstractController
{
    #[Route('/', name: 'app_actif_non_courant_index', methods: ['GET'])]
    public function index(ActifNonCourantRepository $actifNonCourantRepository): Response
    {
        return $this->render('actif_courant/index.html.twig', [
            'actif_non_courants' => $actifNonCourantRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_actif_non_courant_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository): Response
    {
        $actifNonCourant = new ActifNonCourant();
        $user = $userRepository->find(106);

        $actifNonCourant->setUserId($user);
        $form = $this->createForm(ActifNonCourantType::class, $actifNonCourant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($actifNonCourant);
            $entityManager->flush();

            return $this->redirectToRoute('app_actif_courant_index', [], Response::HTTP_SEE_OTHER);
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

            return $this->redirectToRoute('app_actif_courant_index', [], Response::HTTP_SEE_OTHER);
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

    #[Route('/pdf', name: 'app_actif_non_courant_pdf')]
    public function pdf(Pdf $snappyPdf ): Response
    {
        // Render HTML content using Twig template and pass data to the template
        $htmlContent = '<html><body><p>This is a simple PDF generated using Symfony and SnappyBundle.</p></body></html>'    ;

        // Generate PDF output from HTML content
        $pdfOutput = $snappyPdf->getOutputFromHtml($htmlContent);
        // Return PDF as response
        return new Response(
            $pdfOutput,
            Response::HTTP_OK,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="actif_courant.pdf"',
            ]
        );
    }
}
