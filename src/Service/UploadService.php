<?php
/**
 * Created by PhpStorm.
 * User: langanfin
 * Date: 03/12/2019
 * Time: 15:05
 */

namespace App\Service;


use Psr\Container\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadService
{

    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }


    public function upload(UploadedFile $file, $targetDirectory,$reference =null)
    {


        if ($reference)  {
            $fileName  =  $reference.'_sonorise.'.$file->guessExtension();
        }else {
            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
            $fileName = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();
        }


        try {
            $file->move($targetDirectory, $fileName);
        } catch (FileException $e) {
            dump($e);
            die;
            // ... handle exception if something happens during file upload
        }

        return $fileName;
    }


    public function deleteFile($targerBase, $fileName)
    {
        try {

            $fs = new Filesystem();
            $fs->remove($targerBase . "/$fileName");
            return true;
        } catch (FileException $exception) {
            return false;
        }

    }


}