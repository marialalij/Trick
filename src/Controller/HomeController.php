<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Trick;
use App\Form\CommentType;
use App\Entity\Comment;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends AbstractController
{
    /**
     * @Route("/home", name="home")
     */
    /**
     * @Route("/", name="home")
     */

    public function index(): Response
    {

        $rep = $this->getDoctrine()->getRepository(Trick::class);
        $tricks = $rep->findAll();

        return $this->render("pages/home.html.twig", [
            'tricks' => $tricks,

        ]);
    }

    /**
     * @Route("/show/{id}", name="show")
     */

    public function show($id, Request $request): Response
    {
        $rep = $this->getDoctrine()->getRepository(Trick::class);
        $trick = $rep->find($id);
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($comment);
            $em->flush();

            return $this->redirectToRoute('home');
        }
        return $this->render('show/index.html.twig', [
            'trick' => $trick,
            'form' => $form->createView(),

        ]);
    }
}
