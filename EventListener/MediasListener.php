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
use Doctrine\Common\Persistence\ManagerRegistry;
use Mykees\MediaBundle\Manager\MediaManager;

class MediasListener
{
    public $managerRegistry;
    public $rootDir;
    public $resize_parameters;

    public function __construct(ManagerRegistry $managerRegistry, $rootDir, $resize_parameters)
    {
        $this->managerRegistry = $managerRegistry;
        $this->rootDir = $rootDir;
        $this->resize_parameters = $resize_parameters;
    }


    public function preRemove(LifecycleEventArgs $args)
    {
        $model = $args->getEntity();

        if($model instanceof Mediable)
        {
            $manager = new MediaManager($this->managerRegistry,$this->rootDir,$this->resize_parameters);
            $manager->removeAllMediasForModel($model,$this->resize_parameters);
        }
    }

    public function getSubscribedEvents()
    {
        return [
            Events::preRemove
        ];
    }
}
