<?php
namespace Mykees\MediaBundle\Controller;

use Mykees\MediaBundle\Entity\Media;
use Mykees\MediaBundle\Event\MediaUploadEvents;
use Mykees\MediaBundle\Event\UploadEvent;
use Mykees\MediaBundle\Form\Type\MediaShowType;
use Mykees\MediaBundle\Form\Type\MediaType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MediasController extends Controller
{

    private function getManage()
    {
        return $this->getDoctrine()->getManager();
    }

    /**
     * Index Media List
     * @param $model
     * @param $bundle
     * @param $model_id
     * @param $editor
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction( $model, $bundle, $model_id, $editor )
    {
        $medias  = $this->get('mk.media.manager')->findMediasByModelAndId($model, $model_id);
        $entity  = $this->getManage()->getRepository("$bundle:$model")->find($model_id);
        $mode    = $editor=='true' ? $editor : null ;
        $url = $editor == "true" ? ['model'=>$model,'bundle'=>$bundle,'model_id'=>$model_id,'mode'=>'true'] : ['model'=>$model,'bundle'=>$bundle,'model_id'=>$model_id];
        $form = $this->createForm(
            new MediaType(),
            new Media,
            [
                'action' => $this->generateUrl('mykees_media_add',$url),
                'method' => 'POST',
            ]
        );

        return $this->render('MykeesMediaBundle:Media:index.html.twig',[
            'form'=>$form->createView(),
            'medias'=> $medias,
            'entity'=>$entity,
            'model'=>$model,
            'bundle'=> $bundle,
            'model_id'=> $model_id,
            "mode"=> $mode
        ]);
    }

    /**
     * Save And Upload Media (Ajax)
     * @param Request $request
     * @throws \Exception
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addAction( Request $request )
    {
        $file     = $request->files;
        $model_id = $request->get('model_id');
        $model    = $request->get('model');
        $bundle   = $request->get('bundle');
        $mode     = $request->get('mode');
        if( $request->isXmlHttpRequest() && $file )
        {
            //Init Event
            $event = $this->initEvent($file,$model,$model_id);

            if($event->getMedia())
            {
                $entity = $this->getManage()->getRepository("$bundle:$model")->find($model_id);

                return $this->render('MykeesMediaBundle:Media:upload/upload_list.html.twig',[
                    'media'=>$event->getMedia(),'model'=>$model,'entity'=>$entity,'bundle'=>$bundle,'model_id'=>$model_id,'mode'=>$mode
                ]);
            }else{
                $response = new Response();
                $response->setContent(json_encode(array(
                    'error'=>"Le format n'est pas valid"
                )));
                $response->headers->set('Content-Type', 'application/json');
                $response->setStatusCode(500);
                return $response;
            }
        }
    }

    private function initEvent($file,$model,$model_id)
    {
        $event = new UploadEvent();
        $event->setFile($file);
        $event->setMediableModel($model);
        $event->setMediableId($model_id);
        $event->setContainer($this->container);
        $event->setRootDir($this->get('kernel')->getRootDir());
        //File upload process
        $this->get("event_dispatcher")->dispatch(MediaUploadEvents::UPLOAD_FILE, $event);

        return $event;
    }

    /**
     * Add Thumb to an entity
     * @param $model
     * @param $bundle
     * @param $model_id
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function thumbAction( $model, $bundle, $model_id, $id, Request $request )
    {
        $media  = $this->getManage()->getRepository("MykeesMediaBundle:Media")->find($id);
        $entity  = $this->getManage()->getRepository("$bundle:$model")->find($model_id);
        $entity->setThumb($media);
        $this->getManage()->persist($entity);
        $this->getManage()->flush();

        return $this->referer($request);
    }

    /**
     * Show a medias for add in the textarea
     * @param null $model
     * @param null $id
     * @param Request $request
     * @return Response
     */
    public function showAction( $model=null, $id = null, Request $request )
    {
        if( !$id )
        {
            $class = $request->get('class');
            $alt = $request->get('alt');
            $media = $this->getManage()->getRepository('MykeesMediaBundle:Media')->findOneBy(['name'=>$alt,'model'=>$model]);
        }else{
            $media = $this->getManage()->getRepository('MykeesMediaBundle:Media')->find($id);
            $class=null;
        }
        $form = $this->createForm(
            new MediaShowType(),
            $media,
            [
                'action' => $this->generateUrl('mykees_media_show',['model'=>$model,'id'=>$id]),
                'method' => 'POST',
            ]
        );
        if($request->getMethod() == "POST")
        {
            $media = $request->request->all();
            return $this->render('MykeesMediaBundle:Media:tinymce.html.twig',['media'=>$media]);
        }

        return $this->render('MykeesMediaBundle:Media:show/show.html.twig',[
            'media'=>$media,'form'=>$form->createView(),'class'=>$class,"model"=>$model
        ]);
    }


    /**
     * Delete a media (Ajax)
     * @param $model
     * @param $bundle
     * @param $id
     * @param Request $request
     * @return Response
     */
    public function deleteAction( $model, $bundle, $id, Request $request )
    {
        if( $id )
        {
            $media = $this->getManage()->getRepository('MykeesMediaBundle:Media')->find($id);
            $media_manager = $this->get('mk.media.manager');
            $media_manager->unlink($model,$media);

            $modelReferer = $this->getManage()->getRepository("$bundle:$model")->find($media->getMediableId());
            if(method_exists($modelReferer,'getThumb') && $modelReferer->getThumb()->getId() == $media->getId())
            {
                $modelReferer->setThumb(null);
            }

            $media_manager->remove($media);
        }

        return new Response();
    }

    /**
     * Referer
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    private function referer(Request $request)
    {
        $referer = $request->headers->get('referer');

        return $this->redirect($referer);
    }
}
