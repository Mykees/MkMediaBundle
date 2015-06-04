<?php
/**
 * Created by PhpStorm.
 * User: tetsu0o
 * Date: 28/12/14
 * Time: 15:02
 */

namespace Mykees\MediaBundle\Manager;


use Mykees\MediaBundle\Interfaces\Mediable;
use Mykees\MediaBundle\Util\Reflection;

abstract class AbstractManager {

    public function getModelInfos(array $datas)
    {
        $ids= [];
        $models = [];
        $model_exist = false;

        foreach( $datas as $k=>$data )
        {
            if($data instanceof Mediable)
            {
                array_push($ids,$data->getMediableId());
                if( $model_exist != $data->getMediableModel() )
                {
                    array_push($models,$data->getMediableModel());
                    $model_exist = $data->getMediableModel();
                }
            }
        }

        return [ 'ids'=>$ids, 'models'=>$models ];
    }

    public function refreshMediasArray(array $medias, array $models)
    {
        $this->clean($models);
        foreach($models as $model)
        {
            foreach($medias as $media){
                if($model instanceof Mediable)
                {
                    if($model->getId() == $media->getMediableId() && $model->getMediableModel() == $media->getMediableModel())
                    {
                        $model->getMedias()->add($media);
                    }
                }
            }
        }
    }

    public function addMedia($media, Mediable $model)
    {
        $model->getMedias()->add($media);
    }

    public function addMedias(array $medias, Mediable $model)
    {
        foreach($medias as $media){
            if(!empty($media) && Reflection::getClassShortName($media) == 'Media'){
                $this->addMedia($media,$model);
            }
        }
    }

    public function refreshMedias(array $medias, Mediable $model)
    {
        $model->getMedias()->clear();
        $this->addMedias($medias,$model);
    }

    public function clean(array $models)
    {
        foreach($models as $model)
        {
            $model->getMedias()->clear();
        }
    }
}
