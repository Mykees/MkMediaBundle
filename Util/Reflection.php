<?php
/**
 * Created by PhpStorm.
 * User: tetsu0o
 * Date: 28/12/14
 * Time: 12:45
 */

namespace Mykees\MediaBundle\Util;


class Reflection {


    public static function getClassName($model)
    {
        $reflection = new \ReflectionClass($model);
        return $reflection->getName();
    }


    public static function getClassShortName ( $model ) {
        $reflection = new \ReflectionClass( $model );
        return $reflection->getShortName();
    }


    public static function getBundlePath ( $model  ){
        $explode = explode('\\', self::getClassName($model ));
        return $explode[0].'\\'.$explode[1].'\\'.$explode[2].'\\'.$explode[3];
    }


    public static function getBundleRepository ( $model  ){
        $explode = explode('\\', self::getClassName($model ));
        return $explode[0].$explode[1].':'.$explode[3];
    }

    public static function getShortBundleRepository ( $model  ){
        $explode = explode('\\', self::getClassName($model ));
        return $explode[0].$explode[1];
    }


} 
