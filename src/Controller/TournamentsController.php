<?php

namespace App\Controller;

use App\Entity\Tournament;
use App\Form\TournamentType;
use App\Repository\TournamentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class TournamentsController extends AbstractController{
    #[Route('/tournaments/{id?}', name: 'modify_tournaments')]
    #[Route('/tournaments', name: 'app_tournaments')]
    public function index(Tournament $tournament = null, Request $request, EntityManagerInterface $entityManager, TournamentRepository $repository): Response
    {
        if(!$tournament){
            $tournament = new Tournament;
        }

        $form = $this->createForm(TournamentType::class,$tournament);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            if(count($tournament->getUser()) > $tournament->getNbrParticipant()){
                $this->addFlash('error', 'Le nombre de participants sélectionnés dépasse la limite.');
                return $this->redirectToRoute('modify_tournaments', ['id' => $tournament->getId()]);
            }
            $entityManager->persist($tournament);
            $entityManager->flush();

            return $this->redirectToRoute('app_home');
        }
        $tournamentAll = $repository->findAll();
        
        return $this->render('tournaments/tournaments.html.twig', [
            'tournament' => $tournamentAll,
            'tournamentForm' => $form->createView(),
            'isModification' => $tournament->getId() !== null
        ]);
    }

    #[Route('/tournament/remove/{id}', name: 'delete_tournament')]
    public function remove(Tournament $tournament, Request $request, EntityManagerInterface $entityManager): Response
    {
        
        

        if($this->isCsrfTokenValid('SUP'.$tournament->getId(),$request->get('_token'))){
            $entityManager->remove($tournament);
            $entityManager->flush();
            $this->addFlash('success','La suppression à été effectuée');
            return $this->redirectToRoute('app_home');

        }
    }
}
