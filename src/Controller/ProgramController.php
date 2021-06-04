<?php

// src/Controller/ProgramController.php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Season;
use App\Entity\Program;
use App\Entity\Episode;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


/**
 * @Route("/programs", name="program_")
 */
class ProgramController extends AbstractController
{
    /**
     * Show all rows from Program’s entity
     *
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
     * @Route("/show/{id<^[0-9]+$>}", name="show")
     * @return Response
     */
    public function show(int $id): Response
    {
        $program = $this->getDoctrine()
            ->getRepository(Program::class)
            ->find($id);

        $seasons = $this->getDoctrine()
            ->getRepository(Season::class)
            ->findBy([
                'program' => $id
            ]);

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
     * @Route("/{programId}/seasons/{seasonId}", name="season_show")
     * @return Response
     */
    public function showSeason(int $programId, int $seasonId): Response
    {
        $program = $this->getDoctrine()
            ->getRepository(Program::class)
            ->find($programId);

        if (!$program) {
            throw $this->createNotFoundException(
                'Programme avec id ' . $programId . ' inexistant dans la base de données.'
            );
        }

        $season = $this->getDoctrine()
            ->getRepository(Season::class)
            ->find($seasonId);


        if (!$season) {
            throw $this->createNotFoundException(
                'Saison avec id ' . $seasonId . ' inexistant dans la base de données.'
            );
        }

        $episodes = $this->getDoctrine()
            ->getRepository(Episode::class)
            ->findBy([
                'season' => $seasonId
            ]);

        return $this->render('program/program_show.html.twig', [
            'program' => $program,
            'season' => $season,
            'episodes' => $episodes,
        ]);
    }

}
