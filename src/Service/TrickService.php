<?php

namespace App\Service;


use App\Entity\Image;
use App\Entity\Video;
use App\Entity\Trick;
use App\helper\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;


class TrickService
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var Request */
    private $request;

    /** @var TokenStorageInterface */
    private $tokenStorage;

    /** FileUploader */
    /** @var FileUploader */
    private $FileUploader;

    /** @var SessionInterface */
    private $session;

    public function __construct(

        TokenStorageInterface $tokenStorage,
        EntityManagerInterface $entityManager,
        RequestStack $request,
        FileUploader $FileUploader,
        SessionInterface $session

    ) {

        $this->entityManager = $entityManager;
        $this->request = $request;
        $this->tokenStorage = $tokenStorage;
        $this->FileUploader = $FileUploader;
        $this->session = $session;
    }

    /**
     * 
     */
    public function handle(Trick $trick, FormInterface $form)
    {

        $form->handleRequest($this->request->getCurrentRequest());

        if ($form->isSubmitted() && $form->isValid()) {

            $image = $form->get('mainImage')->getData();

            if ($image === null) {
                $image = 'image1.jpg';
                $trick->setMainImage($image);
            } else {
                $newFilename = $this->FileUploader->upload($image);

                $trick->setMainImage($newFilename);
            }

            // On récupère les images transmises
            $images = $form->get('images')->getData();

            // On boucle sur les images
            foreach ($images as $image) {

                $newFilename = $this->FileUploader->upload($image);
                // On crée l'image dans la base de données
                $img = new Image();
                $img->setName($newFilename);
                $trick->addImage($img);
            }

            $videos = $form->get('video')->getData();

            //On vérifie si la vidéo éxiste avant de l'ajouter
            if ($videos !== null) {
                if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $videos, $match)) {
                    $video = new Video();
                    $video_id = $match[1];
                    $video->setUrl('https://www.youtube.com/embed/' . $video_id);
                    $video->setTrick($trick);

                    $this->entityManager->persist($video);
                }
            }


            $trick->setCreatedat(new \DateTime('now'));
            $trick->setAuthor($this->tokenStorage->getToken()->getUser());

            $this->entityManager->persist($trick);
            $this->entityManager->flush();
        }
        return false;
    }



    public function update(Trick $trick, FormInterface $form)
    {

        $form->handleRequest($this->request->getCurrentRequest());

        if ($form->isSubmitted() && $form->isValid()) {

            // encode the plain password
            $image = $form->get('mainImage')->getData();

            if ($form->get('mainImage')->getData() !== null) {
                // this is needed to safely include the file name as part of the URL
                $newFilename = $this->FileUploader->upload($image);

                $trick->setMainImage($newFilename);
            } else {
                $image = 'image1.jpg';
                $trick->setMainImage($image);
            }

            // On récupère les images transmises
            $images = $form->get('images')->getData();

            // On boucle sur les images
            foreach ($images as $image) {
                $newFilename = $this->FileUploader->upload($image);

                // On crée l'image dans la base de données
                $img = new Image();
                $img->setName($newFilename);
                $img->setTrick($trick);

                $this->entityManager->persist($img);
            }

            $videos = $form->get('video')->getData();

            if ($videos !== null) {
                if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $videos, $match)) {
                    $video = new Video();
                    $video_id = $match[1];
                    $video->setUrl('https://www.youtube.com/embed/' . $video_id);
                    $video->setTrick($trick);

                    $this->entityManager->persist($video);
                }
            }

            $trick->setUpdatedAt(new \DateTime('now'));
            $trick->setAuthor($this->tokenStorage->getToken()->getUser());

            $this->entityManager->persist($trick);
            $this->entityManager->flush();


            return true;
        }

        return false;
    }
}
