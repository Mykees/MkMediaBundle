<?php
/**
 * Created by PhpStorm.
 * User: tetsu0o
 * Date: 30/12/14
 * Time: 02:47
 */

namespace Mykees\MediaBundle\Twig\Extension;


class ImageResizeExtension extends \Twig_Extension
{
    public $rootDir;

    public function __construct($rootDir)
    {
        $this->rootDir = $rootDir;
    }



    public function getFunctions()
    {
        return [
            'image' => new \Twig_Function_Method($this, 'getImage', array('is_safe' => array('html'))),
        ];
    }

    public function getImage($image, $width, $height, $options=[])
    {
        $attr = false;
        foreach($options as $k=>$opt)
        {
            if( $k > 1 ){
                $attr .= ' ';
            }
            $attr .= $k.'="'.$opt.'"';
        }

        $webroot = $this->rootDir.'/../web/';
        $info = pathinfo($image);
        $imageResize = $info['dirname'] . '/' . $info['filename'] . "_$width" . "x$height" . '.jpg';
        $fullPathImageResize = $webroot . $imageResize;

        if(file_exists($fullPathImageResize))
        {
            return '<img src="'. $imageResize .'" '. $attr .'>';
        } else {
            return false;
        }

    }

    public function getName()
    {
        return 'image_resize';
    }
}
