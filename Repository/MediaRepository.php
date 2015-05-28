<?php
/**
 * Created by PhpStorm.
 * User: Rafidion Michael
 * Date: 30/11/2014
 * Time: 03:30
 */

namespace Mykees\MediaBundle\Repository;


use Doctrine\ORM\EntityRepository;
use Mykees\MediaBundle\Interfaces\Mediable;

class MediaRepository extends EntityRepository
{
    /**
     * Get the query by model name and model id
     * @param $model
     * @param $model_id
     * @return \Doctrine\ORM\Query
     */
    public function queryForModelAndId($model, $model_id)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('m')
            ->from($this->_entityName,'m')
            ->where('m.model = :model')
            ->andWhere('m.modelId = :modelId')
            ->setParameter('model',$model)
            ->setParameter('modelId',$model_id)
            ->getQuery()
        ;
        return $query;
    }

    /**
     * Get query for model name
     * @param $model
     * @return \Doctrine\ORM\Query
     */
    public function queryForModel($model)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('m')
            ->from($this->_entityName,'m')
            ->where('m.model = :model')
            ->setParameter('model',$model)
            ->getQuery()
        ;
        return $query;
    }

    /**
     * get query for an array objects
     * @param array $model_info
     * @return \Doctrine\ORM\Query
     */
    public function queryForArray(array $model_info)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('m')
            ->from($this->_entityName,'m')
            ->where('m.model IN(:model)')
            ->andWhere('m.modelId IN(:modelId)')
            ->setParameter('model',array_values($model_info['models']))
            ->setParameter('modelId',array_values($model_info['ids']))
            ->getQuery()
        ;
        return $query;
    }

    /**
     * find media for an array objects
     * @param array $model_info
     * @return array
     */
    public function findForArrayModels(array $model_info){
        return $this->queryForArray($model_info)
                    ->getResult();
    }

    /**
     * find media by model name and model id
     * @param $model
     * @param $model_id
     * @return array
     */
    public function findForModelAndId($model, $model_id)
    {
        return $this->queryForModelAndId($model, $model_id)
                    ->getResult();
    }

    /**
     * Get media by model name
     * @param $model
     * @return array
     */
    public function findForModel($model)
    {
        return $this->queryForModel($model)
                    ->getResult();
    }

} 