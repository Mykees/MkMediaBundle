<?php
namespace Mykees\MediaBundle\Controller;

use Mykees\MediaBundle\Entity\Media;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class WidgetAjaxController extends Controller
{
    public function fetchForModelAction($model)
    {
        $em = $this->getDoctrine()->getManager();
        $mediaRepo = $em->getRepository('MykeesMediaBundle:Media');
        $images = $mediaRepo->findAll();
        return new Response($this->renderView('MykeesMediaBundle:Widget:image_results.html.twig', array(
            'groupName' => substr(str_shuffle(MD5(microtime())), 0, 10),
            'images' => $images,
        )));
    }
}
