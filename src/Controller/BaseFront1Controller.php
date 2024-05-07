<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BaseFront1Controller extends AbstractController
{
    #[Route('/base/front1', name: 'app_base_front1')]
    public function index(): Response
    {
        return $this->render('base_front1/index.html.twig', [
            'controller_name' => 'BaseFront1Controller',
        ]);
    }
}
