<?php
/**
 * Created by PhpStorm.
 * User: Rafidion Michael
 * Date: 12/12/2014
 * Time: 12:21
 */

namespace Mykees\MediaBundle\EventListener;


use Doctrine\ORM\Event\LifecycleEventArgs;
use Mykees\MediaBundle\Interfaces\Mediable;
use Symfony\Component\DependencyInjection\ContainerInterface;

class MediasListener
{

    public $container;
    public $manager;
    public $entity;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function preRemove(LifecycleEventArgs $args)
    {
        $model = $args->getEntity();
        $this->manager = $this->container->get('mk.media.manager');
        if($model instanceof Mediable)
        {
            $this->manager->removeAllMediasForModel($model);
        }
    }

    public function getSubscribedEvents()
    {
        return [
            Events::preRemove
        ];
    }
} 