<?php

namespace App\Controller;

use App\Entity\Comment;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
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

    /**
     * Delete comment for user
     * @Route("/profile/comment/delete/{id}", name="comment_delete")
     * @param EntityManagerInterface $manager
     * @param Comment $comment
     *
     * @return Response
     */
    public function delete(EntityManagerInterface $manager, Comment $comment): Response
    {
        $manager->remove($comment);
        $manager->flush();
        $this->addFlash('success', 'Le commentaire a été supprimé.');
        return $this->redirectToRoute('user.comments');
    }
}
