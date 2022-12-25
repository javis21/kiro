<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\Image;
use App\Form\AnnonceType;
use App\Repository\GameRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use function Sodium\add;

class GamerController extends AbstractController
{
    /**
     * @Route("/games", name="games_index")
     */
    public function index(GameRepository $repo)
    {
        $games = $repo->findAll();

        return $this->render('game/index.html.twig', [
            'games' => $games,
        ]);
    }

    /**
     *
     * @Route("/games/new", name="games_create")
     *
     * @return Response
     */
    public function create(Request $request, EntityManagerInterface $manager)
    {
        // creating new game 
        $game = new Game();

        $form = $this->createForm(AnnonceType::class, $game);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            foreach ($game->getImages() as $image)
            {
                $image->setgame($game);
                $manager->persist($image);
            }

                $game->setAuthor($this->getUser());

            $manager->persist($game);
            $manager->flush();

            $this->addFlash(
                'success',
                " <b>{$game->getTitle()}</b> saved !"
            );

            return $this->redirectToRoute('games_show', [
                'slug' => $game->getSlug()
            ]);
        }

        return $this->render('game/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
    
     * @Route("/games/{slug}/edit", name="games_edit")
     *
     * @return Response
     */
    public function edit(Game $game, Request $request, EntityManagerInterface $manager)
    {
        $form = $this->createForm(AnnonceType::class, $game);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            foreach ($game->getImages() as $image)
            {
                $image->setGame($game);
                $manager->persist($image);
            }

            $manager->persist($game);
            $manager->flush();

            $this->addFlash(
                'success',
                "modifications <b>{$game->getTitle()}</b> has been saved"
            );

            return $this->redirectToRoute('games_show', [
                'slug' => $game->getSlug()
            ]);
        }

        return$this->render('game/edit.html.twig', [
            'form' => $form->createView(),
            'game' => $game
        ]);
    }

    /**
    
     * @Route("/games/{slug}", name="games_show")
     *
     * @return Response
     */
    public function show(Game $game)
    {
        return $this->render('game/show.html.twig',
            [
               'game' => $game
            ]);
    }
}
