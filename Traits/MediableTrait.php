<?php
/**
 * Created by PhpStorm.
 * User: tetsu0o
 * Date: 28/12/14
 * Time: 12:41
 */

namespace Mykees\MediaBundle\Traits;
use Doctrine\Common\Collections\ArrayCollection;
use Mykees\MediaBundle\Util\Reflection;

trait MediableTrait {

    protected $medias;

    public function getMediableModel(){
        return Reflection::getClassShortName($this);
    }

    public function getMediableId(){
        return $this->getId();
    }

    public function getMedias(){
        $this->medias = $this->medias ? : new ArrayCollection();
        return $this->medias;
    }
} 