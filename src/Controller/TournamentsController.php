<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TournamentsController extends AbstractController{
    #[Route('/tournaments', name: 'app_tournaments')]
    public function index(): Response
    {
        return $this->render('tournaments/tournaments.html.twig', [
            'controller_name' => 'TournamentsController',
        ]);
    }
}
