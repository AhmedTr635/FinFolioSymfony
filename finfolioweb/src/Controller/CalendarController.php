<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
#[Route('/calendar')]

class CalendarController extends AbstractController
{
    #[Route('/cal', name: 'app_event_calendar')]

    public function index(): Response
    {
        return $this->render('evennement/calendar.html.twig');
    }
}