<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class templateController extends AbstractController
{
    #[Route('/tem', name: 'app_your_controller_name')]
    public function index(): Response
    {
        return $this->render('templateController/listofCredit.html.twig', [

        ]);
    }

}
