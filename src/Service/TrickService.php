<?php

namespace App\Service;

use DateTime;
use App\Entity\User;
use App\Entity\Trick;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class TrickService
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var VideoService
     */
    private $videoService;

    /**
     * @var ImageService
     */
    private $imageService;

    public function __construct(EntityManagerInterface $entityManager, VideoService $videoService, ImageService $imageService)
    {
        $this->entityManager = $entityManager;
        $this->videoService = $videoService;
        $this->imageService = $imageService;
    }

    /**
     * Handle trick creation or update in database.
     *
     * @return Trick $trick
     */
    public function handleCreateOrUpdate(Trick $trick, FormInterface $form, User $author)
    {
        try {
            $trick->setAuthor($author);
            $this->imageService->handleMainImage($trick, $form);
            $this->imageService->handleImages($trick, $form);
            $this->videoService->handleNewVideos($trick, $form);

            if (null !== $trick->getId()) {
                $trick->setUpdatedat(new \DateTime());
            }

            $this->entityManager->persist($trick);
            $this->entityManager->flush();


            return $trick;
        } catch (\Exception $exception) {
            throw $exception;
        }
    }
}
