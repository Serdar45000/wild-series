<?php

namespace App\Controller;

use App\Entity\Episode;
use App\Form\EpisodeType;
use App\Repository\EpisodeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\Slugify;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

/**
 * @Route("/episode")
 */
class EpisodeController extends AbstractController
{
    /**
     * @Route("/", name="episode_index", methods={"GET"})
     */
    public function index(EpisodeRepository $episodeRepository): Response
    {
        return $this->render('episode/index.html.twig', [
            'episodes' => $episodeRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="episode_new", methods={"GET","POST"})
     */
    public function new(Request $request, Slugify $slugify, MailerInterface $mailer): Response
    {
        $episode = new Episode();
        $form = $this->createForm(EpisodeType::class, $episode);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $slug = $slugify->generate($episode->getTitle());
            $episode->setSlug($slug);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($episode);
            $entityManager->flush();

            $email = (new Email())
            ->from($this->getParameter('mailer_from'))
            ->to('serdar45000@gmail.com')
            ->subject('Un nouvel épisode vient d\'être publiée !')
            ->html($this->renderView('email/emailSendEpisode.html.twig', ['episode' => $episode]));

        $mailer->send($email);

            return $this->redirectToRoute('episode_index');
        }

        return $this->render('episode/new.html.twig', [
            'episode' => $episode,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{slug}", name="episode_show", methods={"GET"})
     */
    public function show(Episode $episode, Slugify $slugify): Response
    {
        $slug = $slugify->generate($episode->getTitle());
        $episode->setSlug($slug);
        return $this->render('episode/show.html.twig', [
            'episode' => $episode,
        ]);
    }

    /**
     * @Route("/{slug}/edit", name="episode_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Episode $episode, Slugify $slugify): Response
    {
        $form = $this->createForm(EpisodeType::class, $episode);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Épisode mise à jour');
            return $this->redirectToRoute('episode_index');
        }
        $slug = $slugify->generate($episode->getTitle());
        $episode->setSlug($slug);
        return $this->render('episode/edit.html.twig', [
            'episode' => $episode,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{slug}", name="episode_delete", methods={"POST"})
     */
    public function delete(Request $request, Episode $episode, Slugify $slugify): Response
    {
        if ($this->isCsrfTokenValid('delete'.$episode->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($episode);
            $entityManager->flush();
            $this->addFlash('warning', 'Épisode supprimé');
        }
        $slug = $slugify->generate($episode->getTitle());
        $episode->setSlug($slug);
        return $this->redirectToRoute('episode_index');
    }
}
