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




    public function __construct(UploaderHelper $uploaderHelper)
    {
        $this->uploaderHelper = $uploaderHelper;
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
}
