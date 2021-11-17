<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Image;
use App\Entity\Trick;
use App\Entity\User;
use App\Entity\Video;
use App\Form\CommentType;
use App\Form\TrickType;
use App\Form\VideoType;
use App\Repository\CommentRepository;
use App\Repository\ImageRepository;
use App\Repository\TrickRepository;
use App\Service\TrickService;
use App\Service\CommentService;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;



class TrickController extends AbstractController
{

    /**
     * @var TrickRepository
     */
    private $trickRepository;

    /**
     * @var TrickService
     */
    private $trickService;

    /**
     * @var CommentService
     */
    private $commentService;


    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(TrickRepository $trickRepository, TrickService $trickService, CommentService $commentService, EntityManagerInterface $entityManager)
    {
        $this->trickRepository = $trickRepository;
        $this->trickService = $trickService;
        $this->entityManager = $entityManager;
        $this->commentService = $commentService;
    }


    /** 
     * @Route("/trick", name="trick")
     */
    public function index(): Response
    {
        return $this->render('trick/index.html.twig', [
            'controller_name' => 'TrickController',
        ]);
    }



    /**
     * Handle trick page and new comment creation.
     *
     * @Route("/trick{id}/{slug}", name="trick.show")
     */
    public function show(Trick $trick, Request $request): Response
    {
        $trick = $this->trickRepository->find($trick->getId());
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->commentService->handleNewComment($comment, $trick, $this->getUser());
            $this->addFlash('successComment', 'ton commentaire est posté!');

            return $this->redirect($this->generateUrl('trick.show', [
                'id' => $trick->getId(),
                'slug' => $trick->getName(),
            ]));
        }

        return $this->render('show/index.html.twig', [
            'trick' => $trick,
            'form' => $form->createView(),
        ]);
    }


    /**
     * Handle new trick creation.
     *
     * @Route("/user/trick/new", name="user.trick.new")
     */
    public function new(Request $request): Response
    {
        $trick = new Trick();
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trick = $this->trickService->handleCreateOrUpdate($trick, $form, $this->getUser());
            $this->addFlash('success', 'Ton trick est posté');

            return $this->redirectToRoute('trick.show', [
                'id' => $trick->getId(),
                'slug' => $trick->getSlug(),
            ]);
        }

        return $this->render('trick/new.html.twig', [
            'trick' => $trick,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Handle trick edition.
     *
     * @Route("/user/trick/edit{id}", name="user.trick.edit")
     */
    public function edit(Trick $trick, Request $request): Response
    {
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trick = $this->trickService->handleCreateOrUpdate($trick, $form, $trick->getAuthor());
            $this->addFlash('success', 'Ton trick est modifié');

            return $this->redirectToRoute('trick.show', [
                'id' => $trick->getId(),
                'slug' => $trick->getSlug(),
            ]);
        }

        return $this->render('trick/edit.html.twig', [
            'trick' => $trick,
            'form' => $form->createView(),
        ]);
    }


    /**
     * Display loggued user tricks.
     *
     * @Route("user/tricks", name="user.tricks")
     */
    public function tricks(): Response
    {

        $rep = $this->getDoctrine()->getRepository(Trick::class);
        $tricks = $rep->findAll();

        return $this->render("trick/tricks.html.twig", [
            'tricks' => $tricks,

        ]);
    }


    /**
     * @Route("/delete_trick{id}", name="delete_trick")
     */
    public function delete(EntityManagerInterface $manager, Trick $trick): Response
    {
        $manager->remove($trick);
        $manager->flush();
        $this->addFlash('success', 'Le trick a été supprimé.');
        return $this->redirectToRoute('home');
    }
}
