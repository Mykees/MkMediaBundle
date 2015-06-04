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
        $params = [
            'medias'=>$this->get('mk.media.manager')->findMediasByModelAndId($model, $model_id),
            'entity'=>$this->getManage()->getRepository("$bundle:$model")->find($model_id),
            'mode'=>$editor=='true' ? $editor : null,
            'url'=>$editor == "true" ? ['model'=>$model,'bundle'=>$bundle,'model_id'=>$model_id,'mode'=>'true'] : ['model'=>$model,'bundle'=>$bundle,'model_id'=>$model_id]
        ];
        $form = $this->createForm(
            new MediaType(),
            new Media,
            [
                'action' => $this->generateUrl('mykees_media_add',$params['url']),
                'method' => 'POST',
            ]
        );

        return $this->render('MykeesMediaBundle:Media:index.html.twig',[
            'form'=>$form->createView(),
            'model'=>$model,
            'bundle'=> $bundle,
            'model_id'=> $model_id,
            'params'=>$params
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
        $requestArray = [
            'model_id'=>$request->get('model_id'),
            'model'=>$request->get('model'),
            'file'=>$request->files,
            'bundle'=>$request->get('bundle'),
            'mode'=>$request->get('mode')
        ];

        if( $request->isXmlHttpRequest() && $requestArray['file'] )
        {
            //Init Event
            $event = $this->initEvent($requestArray['file'],$requestArray['model'],$requestArray['model_id']);

            if($event->getMedia())
            {
                $requestArray['entity'] = $this->getManage()->getRepository("{$requestArray['bundle']}:{$requestArray['model']}")->find($requestArray['model_id']);
                $requestArray['media'] = $event->getMedia();

                return $this->render('MykeesMediaBundle:Media:upload/upload_list.html.twig',[
                    'params'=>$requestArray
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
        $params = [];

        if( !$id )
        {
            $params = [
                'class'=>$request->get('class'),
                'alt'=>$request->get('alt')
            ];
            $params['media'] = $this->getManage()->getRepository('MykeesMediaBundle:Media')->findOneBy(['name'=>$params['alt'],'model'=>$model]);
        }else{
            $params = [
                'media'=>$this->getManage()->getRepository('MykeesMediaBundle:Media')->find($id),
                'class'=>null
            ];
        }
        $form = $this->createForm(
            new MediaShowType(),
            $params['media'],
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
            'media'=>$params['media'],'form'=>$form->createView(),'class'=>$params['class'],"model"=>$model
        ]);
    }


    /**
     * Delete a media (Ajax)
     * @param $model
     * @param $bundle
     * @param $id
     * @return Response
     */
    public function deleteAction( $model, $bundle, $id )
    {
        if( $id )
        {
            $media = $this->getManage()->getRepository('MykeesMediaBundle:Media')->find($id);
            $media_manager = $this->get('mk.media.manager');
            $media_manager->unlink($model,$media);

            $model_referer = $this->getManage()->getRepository("$bundle:$model")->find($media->getMediableId());
            if(method_exists($model_referer,'getThumb') && $model_referer->getThumb()->getId() == $media->getId())
            {
                $model_referer->setThumb(null);
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
