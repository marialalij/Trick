<?php

namespace App\Helper;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class UploaderHelper
{

    private $targetDirectory;
    private $slugger;

    /**
     * @var String
     */
    private $uploadsPath;

    public function __construct(string $uploadsPath, $targetDirectory, SluggerInterface $slugger)
    {
        $this->uploadsPath = $uploadsPath;
        $this->targetDirectory = $targetDirectory;
        $this->slugger = $slugger;
    }

    /**
     * Handle uploading image file and move to proper directory.
     */
    public function uploadFile(File $file, string $type, string $folderName): string
    {
        $destination = $this->uploadsPath . $type . '/' . $folderName;
        $newFileName = md5(uniqid()) . '.' . $file->guessExtension();
        $file->move($destination, $newFileName);

        return $newFileName;
    }


    public function upload(UploadedFile $file)
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $fileName = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

        try {
            $file->move($this->getTargetDirectory(), $fileName);
        } catch (FileException $e) {
        }

        return $fileName;
    }

    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }
}
