<?php

// src/Controller/ProgramController.php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Season;
use App\Entity\Program;
use App\Entity\Episode;
use App\Entity\Comment;
use App\Form\ProgramType;
use App\Form\CommentType;
use App\Service\Slugify;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;


/**
 * @Route("/programs", name="program_")
 */
class ProgramController extends AbstractController
{
    /**
     * Show all rows from Program’s entity
     * @Route("/", name="index")
     * @return Response A response instance
     */
    public function index(): Response
    {
        $programs = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findAll();

        return $this->render(
            'program/index.html.twig', [
                'programs' => $programs
            ]);
    }

     /**
     * @Route("/new", name="new")
     * @return Response
     */
    public function new(Request $request, Slugify $slugify) : Response
    {
        $program = new Program();
        $form = $this->createForm(ProgramType::class, $program);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
        $slug = $slugify->generate($program->getTitle());
        $program->setSlug($slug);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($program);
        $entityManager->flush();
        return $this->redirectToRoute('program_index');
        }
        return $this->render('program/new.html.twig', ["form" => $form->createView()]);
    }

    /**
     * @Route("/{slug}", name="show")
     * @return Response
     */
    public function show(Program $program, Slugify $slugify): Response
    {
        $seasons = $this->getDoctrine()
            ->getRepository(Season::class)
            ->findBy([
                'program' => $program
            ]);

        $slug = $slugify->generate($program->getTitle());
        $program->setSlug($slug);

        if (!$program) {
            throw $this->createNotFoundException(
                'Programme avec id ' . $programId . ' inexistant dans la base de données.'
            );
        }
        return $this->render('program/show.html.twig', [
            'program' => $program,
            'seasons' => $seasons,
        ]);
    }


    /**
     * @Route("/{slug}/seasons/{seasonId}", name="season_show")
     * @ParamConverter("program", class="App\Entity\Program", options={"mapping": {"slug": "slug"}})
     * @ParamConverter("season", class="App\Entity\Season", options={"mapping": {"seasonId": "id"}})
     * @return Response
     */
    public function showSeason(Program $program, Season $season): Response
    {

        if (!$program) {
            throw $this->createNotFoundException(
                'Programme avec id  ' . $program->getId() . ' inexistant dans la base de données.'
            );
        }

        if (!$season) {
            throw $this->createNotFoundException(
                'Saison avec id  ' . $season->getId() . ' inexistant dans la base de données.'
            );
        }

        $episodes = $this->getDoctrine()
            ->getRepository(Episode::class)
            ->findBy([
                'season' => $season->getId()
            ]);

        return $this->render('program/program_show.html.twig', [
            'program' => $program,
            'season' => $season,
            'episodes' => $episodes,
        ]);
    }

    /**
     * @Route("/{programSlug}/seasons/{seasonId}/episodes/{episodeSlug}", name="episode_show")
     * @ParamConverter("program", class="App\Entity\Program", options={"mapping": {"programSlug": "slug"}})
     * @ParamConverter("season", class="App\Entity\Season", options={"mapping": {"seasonId": "id"}})
     * @ParamConverter("episode", class="App\Entity\Episode", options={"mapping": {"episodeSlug": "slug"}})
     * @return Response
     */
    
    public function showEpisode(Request $request, Program $program, Season $season, Episode $episode, EntityManagerInterface $entityManager, Slugify $slugify): Response
    {

        if (!$program) {
            throw $this->createNotFoundException(
                'Programme avec id ' . $program->getId() . ' inexistant dans la base de données.'
            );
        }
        if (!$season) {
            throw $this->createNotFoundException(
                'Saison avec id : ' . $season->getId() . ' inexistant dans la base de données.'
            );
        }
        if (!$episode) {
            throw $this->createNotFoundException(
                'Épisode avec id : ' . $episode->getId() . ' inexistant dans la base de données.'
            );
        }

        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setEpisode($episode);
            $comment->setAuthor($this->getUser());
            $entityManager->persist($comment);
            $entityManager->flush();
            return $this->redirect($request->getUri());
        }

        $slug = $slugify->generate($program->getTitle());
        $program->setSlug($slug);
        return $this->render('program/episode_show.html.twig', [
            'program' => $program,
            'season' => $season,
            'episode' => $episode,
            'form' => $form->createView(),
            'button_label' => 'Poster',
        ]);
    }
}
