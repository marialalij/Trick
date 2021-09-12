<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\TrickType;
use App\Entity\Trick;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\TrickRepository;
use App\Service\CommentService;

class TrickController extends AbstractController
{

    /**
     * @var TrickRepository
     */
    private $trickRepository;

    /**
     * @var CommentService
     */
    private $commentService;



    public function __construct(TrickRepository $trickRepository, CommentService $commentService)
    {
        $this->trickRepository = $trickRepository;
        $this->commentService = $commentService;
    }

    /**
     * 
     * @Route("/trick", name="trick")
     */
    public function index(): Response
    {
        return $this->render('trick/index.html.twig', [
            'controller_name' => 'TrickController',
        ]);
    }

    /**
     * @Route("/trick/new", name="trick_new")
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $trick = new Trick();
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($trick);
            $em->flush();

            return $this->redirectToRoute('home');
        }
        return $this->render('trick/new.html.twig', [
            "form" => $form->createView()
        ]);
    }

    /**
     * Handle trick page and new comment creation.
     *
     * @Route("/trick{id}", name="trick.show")
     */
    public function show(Trick $trick, Request $request): Response
    {
        $trick = $this->trickRepository->find($trick->getId());
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->commentService->handleNewComment($comment, $trick, $this->getUser());
            $this->addFlash('successComment', 'Your comment is posted !');

            return $this->redirect($this->generateUrl('trick.show', [
                'id' => $trick->getId(),

            ]));
        }

        return $this->render('show/index.html.twig', [
            'trick' => $trick,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("trick/{id}/edit", name="trick_edit")
     * @param Trick $trick
     * @param Request $request
     * @return Response
     */

    public function edit(Trick $trick, Request $request): Response
    {
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->redirectToRoute('home');
        }
        return $this->render("trick/edit.html.twig", [
            "form" => $form->createView()
        ]);
    }

    /**
     * @Route("/trick/{id}/delete", name="trick_delete")
     * @param Trick $trick
     * @return RedirectResponse
     */
    public function delete(Trick $trick): RedirectResponse
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($trick);
        $em->flush();

        return $this->redirectToRoute("home");
    }
}
