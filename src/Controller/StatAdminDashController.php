<?php

namespace App\Controller;

use App\Repository\DigitalCoinsRepository;
use App\Repository\InvestissementRepository;
use App\Repository\RealEstateRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StatAdminDashController extends AbstractController
{
    #[Route('/stat/admin/dash', name: 'app_stat_admin_dash')]
    public function index(DigitalCoinsRepository $digitalCoinsRepository,InvestissementRepository $investissementRepository,RealEstateRepository $realEstateRepository): Response
    {
        return $this->render('stat_admin_dash/index.html.twig', [
            'digital_coins' => $digitalCoinsRepository->findAll(),
            'investissements' => $investissementRepository->findAll(),
            'real_estates' => $realEstateRepository->findAll(),
            'realEstateRepository' => $realEstateRepository,
        ]);
    }
}
