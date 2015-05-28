<?php
/**
 * Created by PhpStorm.
 * User: Rafidion Michael
 * Date: 30/11/2014
 * Time: 18:21
 */

namespace Mykees\MediaBundle\Event;


use Symfony\Component\EventDispatcher\Event;

class UploadEvent extends Event {

    public $media;
    public $model;
    public $model_id;
    public $file;

    /**
     * @return mixed
     */
    public function getMedia()
    {
        return $this->media;
    }

    /**
     * @param mixed $media
     */
    public function setMedia($media)
    {
        $this->media = $media;
    }

    /**
     * @return mixed
     */
    public function getMediableModel()
    {
        return $this->model;
    }

    /**
     * @param mixed $model
     */
    public function setMediableModel($model)
    {
        $this->model = $model;
    }

    /**
     * @return mixed
     */
    public function getMediableId()
    {
        return $this->model_id;
    }

    /**
     * @param mixed $model_id
     */
    public function setMediableId($model_id)
    {
        $this->model_id = $model_id;
    }

    /**
     * @return mixed
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param mixed $file
     */
    public function setFile($file)
    {
        $this->file = $file;
    }

}