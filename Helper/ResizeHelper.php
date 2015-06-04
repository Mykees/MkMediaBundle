<?php
/**
 * Created by PhpStorm.
 * User: tetsu0o
 * Date: 29/12/14
 * Time: 02:03
 */

namespace Mykees\MediaBundle\Helper;

use Imagine\Image\ImageInterface;

class ResizeHelper {

    public $options;
    public $webroot;


    public function __construct(array $resize_option, $webroot){
        $this->options = $resize_option;
        $this->webroot = $webroot.'/img/';
    }


    public function resize($image)
    {
        $absolute_path = $this->webroot . $image;
        $absolute_info = pathinfo($absolute_path);
        $allowedExtension = ['jpg','JPG','jpeg',"JPEG",'png','PNG','gif','GIF'];
        $extension = pathinfo($image);

        if(in_array($extension['extension'],$allowedExtension))
        {
            if(!empty($this->options))
            {
                foreach($this->options['size'] as $k=>$v)
                {
                    $width = $v['width'];
                    $height = $v['height'];
                    $dest = $absolute_info['dirname'] . '/' . $absolute_info['filename'] . "_$width" . "x$height" . '.jpg';

                    if(file_exists($dest))
                    {
                        return false;
                    }

                    $imagine = new \Imagine\Gd\Imagine();
                    $mode = $this->options['mode'];

                    $imagine->open($absolute_info['dirname'] . '/' . $absolute_info['filename'] . '.jpg')
                        ->thumbnail(new \Imagine\Image\Box($width,$height), !empty($mode) && $mode == 'inset' ? ImageInterface::THUMBNAIL_INSET : ImageInterface::THUMBNAIL_OUTBOUND)
                        ->save($dest);
                }
            }
        }

        return true;
    }
}
