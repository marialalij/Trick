<?php

namespace App\Controller;

use App\Entity\Comment;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\CommentType;

class CommentController extends AbstractController
{

    /**
     * Display loggued user comment.
     *
     * @Route("user/comments", name="user.comments")
     */
    public function comments(): Response
    {

        $rep = $this->getDoctrine()->getRepository(Comment::class);
        $comments = $rep->findAll();

        return $this->render("comment/comment.html.twig", [
            'comments' => $comments,

        ]);
    }
}
