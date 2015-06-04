<?php
/**
 * Created by PhpStorm.
 * User: Rafidion Michael
 * Date: 30/11/2014
 * Time: 18:22
 */

namespace Mykees\MediaBundle\EventListener;


use Doctrine\Common\Persistence\ManagerRegistry;
use Mykees\MediaBundle\Helper\ResizeHelper;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Mykees\MediaBundle\Event\MediaUploadEvents;
use Mykees\MediaBundle\Event\UploadEvent;
use Mykees\MediaBundle\Entity\Media;
use Mykees\MediaBundle\Util\Urlizer;
use Symfony\Component\HttpFoundation\Response;

class UploadSubscriber implements EventSubscriberInterface {

    public $em;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->em = $managerRegistry->getManager();
    }


    public static function getSubscribedEvents()
    {
        return [
            MediaUploadEvents::UPLOAD_FILE => 'uploadProcess'
        ];
    }


    public function uploadProcess( UploadEvent $event )
    {
        $file  = $event->getFile()->all();
        $fileUploaded = $file['file'];
        $model   = $event->getMediableModel();
        $model_id = $event->getMediableId();
        $resize_option = $event->getContainer()->getParameter('mykees.media.resize');
        $extension = pathinfo($fileUploaded->getClientOriginalName(), PATHINFO_EXTENSION);
        $DS = DIRECTORY_SEPARATOR;


        if( $this->isValidExtension($extension,$event) )
        {
            //create dir
            $webroot = $event->getRootDir().'/../web';
            $dir = $webroot.'/img';

            if(!file_exists($dir)){mkdir($dir,0777);}

            $dir .= $DS.date('Y');
            if(!file_exists($dir)){mkdir($dir,0777);}

            $dir .= $DS.date('m');
            if(!file_exists($dir)){mkdir($dir,0777);}

            //clean and define path filename
            $filename = $this->cleanFilename( $fileUploaded->getClientOriginalName(), $extension, $webroot );
            //test duplicate
            $name = $this->mediaExist( $filename,$webroot );

            $filePath = date('Y').'/'.date('m').'/'.$name;
            $save_media = $this->saveMedia($filePath, $name, $model, $model_id);

            if($save_media)
            {
                //upload
                $fileUploaded->move($dir,$name);
                $event->setMedia($save_media);

                if(!empty($resize_option[$model]))
                {
                    $resize = new ResizeHelper($resize_option[$model], $webroot);
                    $resize->resize($save_media->getFile());
                }

                return true;
            }else{

                return new Response();
            }
        }else{

            return new Response();
        }
    }

    /**
     * Check if file extension is valid
     * @param $extension
     * @return bool
     */
    private function isValidExtension($extension,$event)
    {
        $extensions = $event->getContainer()->getParameter('mykees.media.extension');
        $valid_extensions = !empty($extensions) ? $extensions : ['jpg','jpeg','JPG','JPEG'];

        return in_array($extension,$valid_extensions);
    }

    /**
     * clean the filename
     * @param $filename
     * @param $extension
     * @param $webroot
     * @return string
     */
    private function cleanFilename( $filename, $extension, $webroot )
    {
        $f = explode('.',$filename);
        $cleanFilename = Urlizer::urlize(implode('.',array_slice($f,0,-1)));

        return $cleanFilename.'.'.$extension;
    }

    /**
     * Check for double file
     * @param $filename
     * @param $webroot
     * @param int $count
     * @return string
     */
    private function mediaExist( $filename, $webroot, $count=0 )
    {
        $file = $filename;
        $filePath = false;
        if($count > 0){
            $f = explode('.',$filename);
            $file = $f[0].'_'.$count.'.'.end($f);
            $filePath = $webroot.'/img/'.date('Y').'/'.date('m').'/'.$file;
        }
        $filePath = !$filePath ? $webroot.'/img/'.date('Y').'/'.date('m').'/'.$file : $filePath;
        if(file_exists($filePath))
        {
            $count++;

            return $this->mediaExist( $filename, $webroot, $count );
        }else{
            $filename =  $file;

            return $filename;
        }
    }


    /**
     * @param $filePath
     * @param $filename
     * @param $model
     * @param $model_id
     * @return Media
     */
    private function saveMedia($filePath, $filename, $model, $model_id)
    {
        $media = new Media();
        $fn = explode('.',$filename);
        $media->setName($fn[0]);
        $media->setMediableModel($model);
        $media->setMediableId($model_id);
        $media->setFile($filePath);

        $this->em->persist($media);
        $this->em->flush();

        return $media;
    }
}
