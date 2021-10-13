<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Trick;
use App\Entity\Comment;
use App\Entity\Image;
use App\Entity\Video;
use App\Form\CommentType;
use App\Form\TrickType;
use App\Repository\TrickRepository;
use App\Repository\ImageRepository;
use App\Service\CommentService;
use App\Service\TrickService;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;



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
        $this->commentService = $commentService;
        $this->entityManager = $entityManager;
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
     * Handle new trick creation.
     *
     * @Route("/user/trick/new", name="user.trick.new")
     */
    public function trickAdd()
    {

        $trick = new Trick();
        $form = $this->createForm(TrickType::class, $trick);

        if ($this->trickService->handle($trick, $form) === true) {

            return $this->redirectToRoute('home');
        }

        return $this->render('trick/new.html.twig', [
            'trick' => $trick,
            'form' => $form->createView(),
        ]);
    }


    /**
     * Handle trick page and new comment creation.
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
            $this->addFlash('successComment', 'Your comment is posted !');

            return $this->redirect($this->generateUrl('home', [
                'id' => $trick->getId(),
                'slug' => $trick->getName(),
            ]));
        }
        $images = $this->entityManager->getRepository(Image::class)->findBy(['trick' => $trick]);
        $videos = $this->entityManager->getRepository(Video::class)->findBy(['trick' => $trick]);
        $comment = $this->entityManager->getRepository(Comment::class)->findBy(['trick' => $trick], ['id' => 'DESC']);

        return $this->render('show/index.html.twig', [
            'trick' => $trick,
            'images' => $images,
            'videos' => $videos,
            'comments' => $comment,
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
     * @Route("/update_trick/{id}", name="update_trick")
     */
    public function trickUpdate(Trick $trick, Request $request)

    {

        $imageView = $this->entityManager->getRepository(Image::class)->findBy(['trick' => $trick]);
        $videoView = $this->entityManager->getRepository(Video::class)->findBy(['trick' => $trick]);

        $form = $this->createForm(TrickType::class, $trick);

        if ($this->trickService->update($trick, $form) === true) {
            return $this->redirectToRoute('trick.show', [
                'id' => $trick->getId(),
                'slug' => $trick->getSlug(),
            ]);
        }
        return $this->render('trick/edit.html.twig', [
            'form' => $form->createView(),
            'trick' => $trick,
            'imageView' => $imageView,
            'videoView' => $videoView,
        ]);
    }
    /**
     * @Route("/delete_trick/{id}", name="delete_trick")
     */
    public function deleteTrick(Trick $id)
    {

        $this->entityManager->remove($id);
        $this->entityManager->flush();

        $this->addFlash('success', 'Le Trick a bien était supprimé');
        return $this->redirectToRoute('home');
    }
}
