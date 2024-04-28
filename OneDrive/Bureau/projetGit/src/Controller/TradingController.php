<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TradingController extends AbstractController
{
    #[Route('/trading', name: 'app_trading')]
    public function index(): Response
    {
        return $this->render('trading/index.html.twig', [
            'controller_name' => 'TradingController',
        ]);
    }

}
