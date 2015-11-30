<?php
/**
 * Created by PhpStorm.
 * User: Rafidion Michael
 * Date: 30/11/2014
 * Time: 17:41
 */

namespace Mykees\MediaBundle\Twig\Extension;

use Mykees\MediaBundle\Interfaces\Mediable;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Mykees\MediaBundle\Util\Reflection;

class UploaderExtension extends \Twig_Extension
{

    public $generator;

    public function __construct(UrlGeneratorInterface $generator)
    {
        $this->generator = $generator;
    }


    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('iframe_uploader', [$this, 'iframeUploader'], [
                'is_safe'=>array('html')
            ]),
            new \Twig_SimpleFunction('iframe_basic_uploader', [$this, 'iframeBasicUploader'], [
                'is_safe'=>array('html')
            ]),
            new \Twig_SimpleFunction('editor_uploader', [$this, 'wysiwygUploader'], [
                'is_safe'=>array('html'),
                'needs_environment'=>true
            ])
        ];
    }

    /**
     * Display the iframe of uploader
     * @param $entity
     * @return string
     */
    public function iframeUploader(Mediable $entity)
    {
        $model    = Reflection::getClassShortName($entity);
        $model_id = $entity->getId();
        $bundle   = Reflection::getShortBundleRepository($entity);
        if ($model_id) {
            $url = $this->generator->generate('mykees_media', [
                'model' => $model,
                'bundle'=> $bundle,
                'model_id'=> $model_id,
            ]);

            return '<iframe src="'.$url.'" style="width:100%;border: none;min-height:100%;" class="iframe-uploader">
                </iframe>';
        } else {
            return '<h3 style="font-weight: bold;text-align: center;color:#777">
                The <span style="color:#DD6F6F;border-bottom:2px dashed #777;">ID</span> 
                from your entity <span style="color:#DD6F6F;border-bottom:2px dashed #777;">'.$model.
                '</span> is required to use the uploader</h3>';
        }
    }

    /**
     * Display the iframe of uploader
     * @param $entity
     * @return string
     */
    public function iframeBasicUploader(Mediable $entity)
    {
        $model    = Reflection::getClassShortName($entity);
        $bundle   = Reflection::getShortBundleRepository($entity);

        $url = $this->generator->generate('mykees_media_basic', [
            'model' => $model,
            'bundle'=> $bundle,
        ]);

        return '<iframe src="'.$url.'" style="width:100%; border: none; min-height:100%;" 
            frameBorder="0" class="iframe-uploader"></iframe>';
    }

    /**
     * Display a wysiwyg uploader
     * @param $entity
     * @return
     */
    public function wysiwygUploader(\Twig_Environment $env, Mediable $entity)
    {
        $model    = Reflection::getClassShortName($entity);
        $model_id = $entity->getId();
        $bundle   = Reflection::getShortBundleRepository($entity);

        return  $env->render('MykeesMediaBundle:Media:editor/tinymce.html.twig', [
            'model'=>$model,
            'model_id'=>$model_id,
            'bundle'=> $bundle,
        ]);
    }

    public function getName()
    {
        return "mykees_uploader";
    }
}
