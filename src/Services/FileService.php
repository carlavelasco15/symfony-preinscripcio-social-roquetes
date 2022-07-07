<?php
namespace App\Services;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;   


class FileService {

    private $targetDirectory;

    public function __construct(String $targetDirectory) {
        $this->targetDirectory = $targetDirectory;
    }

    public function upload(UploadedFile $file, $uniqueName = true) {

        $fichero = $uniqueName ? uniqid() . '.' . $file->guessExtension() : $file->getClientOriginalName();

        try {
            $file->move($this->targetDirectory, $fichero);
        } catch(FileException $e) {
            return NULL;
        }
        return $fichero;
    }


    public function delete(String $file) {
        $filesystem = new Filesystem();
        $filesystem->remove($this->targetDirectory.'/'.$file);
    }

    public function replace(UploadedFile $newFile, ?string $oldFile = NULL, $uniqueName = true) {
        if($oldFile) 
            $this->delete($oldFile);
        return $this->upload($newFile, $uniqueName);
    }

    /**
     * Set the value of targetDirectory
     *
     * @return  self
     */ 
    public function setTargetDirectory($targetDirectory)
    {
        $this->targetDirectory = $targetDirectory;

        return $this;
    }
}