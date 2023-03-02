<?php

namespace App\AdminBundle\EventListener;

use App\Entity\Albums;
use App\Entity\Pictures;
use App\Utils\Helper;
use Doctrine\ORM\EntityManagerInterface;
use Oneup\UploaderBundle\Event\PostPersistEvent;
use Liip\ImagineBundle\Imagine\Data\DataManager;
use Liip\ImagineBundle\Imagine\Filter\FilterManager;

class UploadListener
{
    /**
     * @var ObjectManager
     */
    private $em;

    public function __construct(EntityManagerInterface $em, DataManager $dataManager, FilterManager $filterManager, $picturesDirectory)
    {
        $this->em = $em;
        $this->dataManager = $dataManager;
        $this->filterManager = $filterManager;
        $this->picturesDirectory = $picturesDirectory;
    }

    public function onUpload(PostPersistEvent $event)
    {
        $fileName = $event->getFile()->getFileName();

        //file_put_contents("tes.txt",print_r($album,true));
        $picture = new Pictures();
        $picture->setPath($event->getFile()->getFileName());
        $picture->setAlbum($this->em->getReference("\App\Entity\Albums", $event->getRequest()->get('albumId')));

        $this->em->persist($picture);
        $this->em->flush();

        if($event->getRequest()->get('watermark')) {
            $image = $this->dataManager->find("my_watermark_filter", "/uploads/pictures/$fileName");
            $response = $this->filterManager->applyFilter($image, 'my_watermark_filter')->getContent();
            $f = fopen($this->picturesDirectory.$fileName, 'w');
            fwrite($f, $response);
            fclose($f);
        }

        $response = $event->getResponse();
        $response['success'] = true;
        $response['imageUrl'] = (new Helper)->files($fileName,"253_190");
        $response['pictureId'] = $picture->getId();
        return $response;
    }
}