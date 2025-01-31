<?php

namespace App\Controller;

use App\Entity\Combat;
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

    public function generateCombat(Tournament $tournament, EntityManagerInterface $entityManager)
    {
        $players = $tournament->getUser(); // Récupération des joueurs
        
        if (count($players) < 2) {
            throw new \Exception("Il faut au moins 2 joueurs pour générer des matchs !");
        }

        // Supprimer les anciens combat
        foreach ($tournament->getCombat() as $combat) {
            $entityManager->remove($combat);
        }

        // Génération selon le type de tournoi
        if ($tournament->getType()->getName() === 'Élimination directe') {
            shuffle($player);
            $round = 1;
            for ($i = 0; $i < count($players); $i += 2) {
                if (isset($players[$i + 1])) {
                    $combat = new Combat();
                    $combat->setPlayer1($players[$i]);
                    $combat->setPlayer2($players[$i + 1]);
                    $combat->setTournament($tournament);
                    $combat->setRound($round);
                    $entityManager->persist($combat);
                }
            }
        } else if ($tournament->getType()->getName() === 'Ligue') {
            $round = 1;
            foreach ($players as $player1) {
                foreach ($players as $player2) {
                    if ($player1 !== $player2) {
                        $combat = new Combat();
                        $combat->setPlayer1($player1);
                        $combat->setPlayer2($player2);
                        $combat->setTournament($tournament);
                        $combat->setRound($round);
                        $entityManager->persist($combat);
                    }
                }
                $round++;
            }
        }

        $entityManager->flush();
    }

    #[Route('/tournament/{id}/generate-combat', name: 'generate_combat')]
    public function generate(Tournament $tournament, EntityManagerInterface $entityManager): Response
    {
        $this->generateCombat($tournament, $entityManager);

        $this->addFlash('success', 'Les combat ont été générés avec succès !');
        return $this->redirectToRoute('modify_tournaments', ['id' => $tournament->getId()]);
    }

    #[Route('/combat/update/{id}', name: 'update_combat', methods: ['POST'])]
    public function updateMatch(Request $request, Combat $combat, EntityManagerInterface $entityManager)
    {
        $score = $request->request->get('score');
        if ($score) {
            $combat->setScore($score);
            $entityManager->flush();
            $this->addFlash('success', 'Score mis à jour !');
        }

        return $this->redirectToRoute('modify_tournaments', ['id' => $combat->getTournament()->getId()]);
    }



}
