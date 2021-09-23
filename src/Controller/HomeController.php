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
}
