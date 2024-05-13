<?php

namespace App\Controller;

use App\Entity\DigitalCoins;
use App\Form\DigitalCoinsType;
use App\Repository\DigitalCoinsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StatUserCryptoController extends AbstractController
{
    #[Route('/stat/user/crypto', name: 'app_stat_user_crypto')]
    public function index(DigitalCoinsRepository $digitalCoinsRepository): Response
    {
        $digitalCoin = new DigitalCoins();
        $UserId = $this->getUser()->getId();



        return $this->render('stat_user_crypto/index.html.twig', [
            'digital_coins' => $digitalCoinsRepository->findByUserIdAndDateVenteIsNull($UserId),
            'digital_coin' => $digitalCoin,
            'digital_coins_sold' => $digitalCoinsRepository->findByUserIdAndDateVenteIsNotNull($UserId),
            'distribution' => $digitalCoinsRepository->findMontantByUserIdGroupByCode($UserId),
            'totalHolding' => $digitalCoinsRepository->findTotalMontantByUserIdAndDateVenteIsNull($UserId),
            'historicTrades' => $digitalCoinsRepository ->findTotalMontantByUserIdAndDateVenteIsNotNull($UserId),
            'HistoricROI' => $digitalCoinsRepository ->findTotalRoiWhereDateVenteIsNotNull(),
            'totalInvest' => $digitalCoinsRepository ->findTotalMontantByUserId($UserId),// Pass the form view to the template
        ]);
    }
}
