<?php
/**
 * Created by PhpStorm.
 * User: Rafidion Michael
 * Date: 30/11/2014
 * Time: 03:40
 */

namespace Mykees\MediaBundle\Manager;

use Doctrine\ORM\EntityManager;
use Mykees\MediaBundle\Entity\Media;
use Mykees\MediaBundle\Interfaces\Mediable;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Mykees\MediaBundle\Util\Reflection;

class MediaManager extends AbstractManager
{

    public $em;
    public $container;

    public function __construct( EntityManager $entityManager, ContainerInterface $container )
    {
        $this->em = $entityManager;
        $this->container = $container;
    }

    public function getService( $id )
    {
        return $this->container->get($id);
    }

    public function webroot()
    {
        return $this->getService('kernel')->getRootDir().'/../web/img/';
    }

    /**
     * Find medias by model name and model id
     * @param $model_name
     * @param $model_id
     */
    public function findMediasByModelAndId($model_name, $model_id)
    {
        return $this->em->getRepository('MykeesMediaBundle:Media')->findForModelAndId($model_name, $model_id);
    }

    /**
     * Find medias for an array of objects
     * @param array $models
     */
    public function findMediasByArrayObject(array $models)
    {
        $model_info = $this->getModelInfos($models);
        $queryResult = $this->em->getRepository("MykeesMediaBundle:Media")->findForArrayModels($model_info);

        return $this->refreshMediasArray($queryResult,$models);
    }

    /**
     * Find all medias by model name
     * @param $model_name
     */
    public function findMediasByModel($model_name)
    {
        return $this->em->getRepository('MykeesMediaBundle:Media')->findForModel($model_name);
    }

    /**
     * Find all medias for Mediable object
     * @param Mediable $obj
     */
    public function findMedias(Mediable $obj)
    {
        $model = Reflection::getClassShortName($obj);
        $model_id = $obj->getId();

        $queryResult = $this->em->getRepository('MykeesMediaBundle:Media')->findForModelAndId($model,$model_id);

        return $this->refreshMedias($queryResult,$obj);
    }

    /**
     * Get query for model name and model id
     * @param $model_name
     * @param $model_id
     */
    public function getQueryByModelAndId($model_name, $model_id)
    {
        return $this->em->getRepository('MykeesMediaBundle:Media')->queryForModelAndId($model_name,$model_id);
    }

    /**
     * Get query for model name
     * @param $model_name
     */
    public function getQueryByModel($model_name)
    {
        return $this->em->getRepository('MykeesMediaBundle:Media')->queryForModel($model_name);
    }

    /**
     * Get query for Mediable object
     * @param Mediable $obj
     */
    public function getQuery(Mediable $obj)
    {
        $model = Reflection::getClassShortName($obj);
        $model_id = $obj->getId();

        return $this->em->getRepository('MykeesMediaBundle:Media')->queryForModelAndId($model,$model_id);
    }

    public function unlink($model, Media $media)
    {
        $resize_option = $this->container->getParameter('mykees.media.resize');
        $info  = pathinfo($media->getFile());

        if(!empty($resize_option[$model]['size']))
        {
            $sizes = $resize_option[$model]['size'];

            foreach($sizes as $k=>$size)
            {
                $w = $size['width'];
                $h = $size['height'];
                $resizedFile = $this->webroot() . $info['dirname'] . '/' . $info['filename'] . "_$w" . "x$h" . '.jpg';

                if(file_exists($resizedFile))
                {
                    unlink($resizedFile);
                }
            }
        }

        if(file_exists($this->webroot() . $media->getFile()))
        {
            return unlink($this->webroot() . $media->getFile());
        }

        return false;
    }

    public function remove(Media $media)
    {
        $this->em->remove($media);
        $this->em->flush();
        return true;
    }

    public function removeAllMediasForModel( Mediable $model )
    {
        $model_name = Reflection::getClassShortName($model);
        $model_id = $model->getId();

        if(method_exists($model,'getThumb') && $model->getThumb() != null)
        {
            $model->setThumb(null);
        }
        $medias = $this->em->getRepository('MykeesMediaBundle:Media')->findBy([
            'model'=>$model_name,
            'modelId'=>$model_id
        ]);
        foreach($medias as $k=>$media)
        {
            $this->unlink($model_name, $media);
            $this->remove($media);
        }
    }

    /**
     * Remodel ALL Medias
     */
    public function removeAll()
    {

    }
} 