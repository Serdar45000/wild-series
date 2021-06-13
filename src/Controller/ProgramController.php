<?php

// src/Controller/ProgramController.php

namespace App\Controller;

use App\Entity\Season;
use App\Entity\Program;
use App\Entity\Episode;
use App\Entity\Comment;
use App\Service\Slugify;
use App\Entity\Category;
use App\Form\ProgramType;
use App\Form\CommentType;
use App\Repository\ProgramRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

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
    public function index(Request $request, ProgramRepository $programRepository): Response
    {
        $programs = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findAll();
        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $search = $form->getData();
            $programs = $programRepository->findLikeName($search);
        } else {
            $programs = $programRepository->findAll();
        }

        return $this->render(
            'program/index.html.twig', [
                'programs' => $programs,
                'form' => $form->createView(),
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
        $program->setOwner($this->getUser());
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
        $comment->setEpisode($episode);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $comment->setAuthor($this->getUser());
                $entityManager->persist($comment);
                $entityManager->flush();
                return $this->redirect($request->getUri());
            } else {
                throw new AccessDeniedException('Seul les membres peuvent enregsitrer un nouveau programme');
            }
        }

        $slug = $slugify->generate($program->getTitle());
        $program->setSlug($slug);
        return $this->render('program/episode_show.html.twig', [
            'program' => $program,
            'season' => $season,
            'episode' => $episode,
            'form' => $form->createView(),
        ]);
    }

        /**
     * @Route("/{slug}/edit", name="edit", methods={"GET","POST"})
     * @ParamConverter("program", class="App\Entity\Program", options={"mapping": {"slug": "slug"}})
     */
    public function edit(Request $request, Program $program): Response
    {
        if (!($this->getUser() == $program->getOwner()) && !in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
            // If not the owner, throws a 403 Access Denied exception
            throw new AccessDeniedException('Only the owner can edit the program!');
        }

        $form = $this->createForm(ProgramType::class, $program);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('program_index');
        }

        return $this->render('program/edit.html.twig', [
            'season' => $program,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/comment/{id}", name="delete_comment", methods={"POST"})
     */
    public function deleteComment(Request $request, Comment $comment): Response
    {
        /** @var Episode */
        $episode = $comment->getEpisode();
        /** @var Season */
        $season = $episode->getSeason();
        /** @var Program */
        $program = $season->getProgram()->getSlug();

        if ($this->isCsrfTokenValid('delete' . $comment->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($comment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('program_episode_show', [
            'programSlug' => $program,
            'seasonId' => $season->getId(),
            'episodeSlug' => $episode->getSlug(),
        ]);
    }
}
