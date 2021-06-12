<?php


namespace App\Controller;


use App\Repository\ProgramRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="app_index")
     */
    public function index(ProgramRepository $programRepository) :Response
    {

        $programs = $programRepository->findBy([], ['id' => 'ASC'], 3);

        return $this->render('index.html.twig', [
            'programs' => $programs,
        ]);
    }

}