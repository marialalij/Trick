<?php

namespace App\Controller;

use App\Entity\Video;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class VideoController extends AbstractController
{
    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(

        EntityManagerInterface $entityManager

    ) {

        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/delete_video/{id}", name="delete_video")
     * 
     */
    public function deleteVideo($id)
    {

        $video = $this->entityManager->getRepository(Video::class)->find($id);

        $this->entityManager->remove($video);
        $this->entityManager->flush();

        $trick = $video->getTrick();
        $slug = $trick->getSlug();

        $this->addFlash('success', 'Video bien supprimé !');
        return $this->redirectToRoute('update_trick', array(
            'slug' => $slug
        ));
    }
}
