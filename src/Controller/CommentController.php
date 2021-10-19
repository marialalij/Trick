<?php

namespace App\Controller;

use App\Entity\Comment;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
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

    /**
     * Handle comment deletion.
     *
     * @Route("/user/comment/delete{id}", name="user.comment.delete", methods="DELETE")
     * @IsGranted("delete", subject="comment", message="You are not allowed to delete other users comments")
     */
    public function delete(Request $request, Comment $comment): Response
    {
        if ($this->isCsrfTokenValid('comment_deletion_' . $comment->getId(), $request->get('_token'))) {
            $this->commentService->handleDeleteComment($comment);

            if ($comment->getAuthor() === $this->getUser()) {
                $this->addFlash('successComment', 'Your comment has been deleted !');
            } else {
                $this->addFlash('successComment', $comment->getAuthor()->getUsername() . '\'s comment has been deleted !');
            }
        }

        return $this->redirectToRoute('trick.show', [
            'id' => $comment->getTrick()->getId(),
            'slug' => $comment->getTrick()->getSlug(),
        ]);
    }
}
