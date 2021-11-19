<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Trick;
use App\Helper\UploaderHelper;
use App\Helper\ImageFileDeletor;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;

class ImageService
{
    /**
     * @var UploaderHelper
     */
    private $uploaderHelper;

    /**
     * @var ImageFileDeletor
     */
    private $imageFileDeletor;

    /**
     * @var Filesystem
     */
    private $fileSystem;

    /**
     * @var string
     */
    private $trickDirectory;

    public function __construct(UploaderHelper $uploaderHelper, ImageFileDeletor $imageFileDeletor, Filesystem $fileSystem, string $trickDirectory)
    {
        $this->uploaderHelper = $uploaderHelper;
        $this->imageFileDeletor = $imageFileDeletor;
        $this->fileSystem = $fileSystem;
        $this->trickDirectory = $trickDirectory;
    }

    /**
     * Handle new main image upload.
     *
     * @return void
     */
    public function handleMainImage(Trick $trick, FormInterface $form)
    {
        try {
            $mainImage = $form->get('mainImage')->getData();
            if (!empty($mainImage)) {
                $mainImageName = $this->uploaderHelper->uploadFile($mainImage, 'tricks', 'trick_' . $trick->getId());
                $trick->setMainImage($mainImageName);
            }
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * Handle new images upload.
     *
     * @return void
     */
    public function handleImages(Trick $trick, FormInterface $form)
    {
        try {
            $images = $form->get('images')->getData();
            foreach ($images as $image) {
                if (null !== $image->getFile()) {
                    $imageName = $this->uploaderHelper->uploadFile($image->getFile(), 'tricks', 'trick_' . $trick->getId());

                    $image->setName($imageName);
                    $trick->addImage($image);
                }
            }
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * Handle image file uploads deletion after trick edition if deleted.
     *
     * @return void
     */
    public function handleImageFiles(Trick $trick)
    {
        try {
            $trickImages = [$trick->getMainImage()];
            foreach ($trick->getImages() as $image) {
                array_push($trickImages, $image->getName());
            }
            $this->imageFileDeletor->deleteFile('trick', $trick->getId(), $trickImages);
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * handle folder name edition after trick persistence.
     *
     * @return void
     */
    public function handleImageFolderRename(Trick $trick)
    {
        if ($this->fileSystem->exists($this->trickDirectory)) {
            $this->fileSystem->rename($this->trickDirectory, $this->trickDirectory . $trick->getId());
        }
    }

    /**
     * Handle image file uploads folder deletion after trick edition.
     *
     * @return void
     */
    public function handleImageFolderDeletion(Trick $trick)
    {
        $directory = $this->trickDirectory . $trick->getId();
        if ($this->fileSystem->exists($directory)) {
            $this->fileSystem->remove($directory);
        }
    }
}
