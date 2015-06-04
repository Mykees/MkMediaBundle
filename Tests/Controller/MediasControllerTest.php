<?php

namespace Mykees\MediaBundle\Tests\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class MediasControllerTest extends WebTestCase
{
    protected $client;
    protected $container;
    protected $manager;
    protected $image;

    public function setUp()
    {
        //Load fixtures
        $fixtures = array(
            'Mykees\MediaBundle\DataFixtures\ORM\LoadMedias',
            'Mvc\BlogBundle\DataFixtures\ORM\LoadPostsData',
        );
        $this->loadFixtures($fixtures);

        $this->client = static::createClient();
        $this->container = $this->client->getContainer();
        $this->manager = $this->container->get('mk.media.manager');
        $this->image = $this->container->get('kernel')->getRootDir().'/../web';

        parent::setUp();
    }

    public function testListMedias()
    {
        $crawler = $this->client->request('GET', '/admin/medias/Post/MvcBlogBundle/1');
        $this->assertEquals(200,$this->client->getResponse()->getStatusCode());
        $this->assertEquals(2,$crawler->filter('.count-tr')->count());


        $crawler = $this->client->request('GET', '/admin/medias/Post/MvcBlogBundle/2');
        $this->assertEquals(200,$this->client->getResponse()->getStatusCode());
        $this->assertEquals(1,$crawler->filter('.count-tr')->count());
    }

    public function testFindAllMediasForAModel()
    {
        $medias  = $this->manager->findMediasByModelAndId('Post',1);
        $this->assertGreaterThan(1,count($medias));
        $this->assertEquals(2, count($medias));
    }

    public function testRemoveFile()
    {
        $this->client->request('GET', '/admin/medias/delete/Post/MvcBlogBundle/9');
        $this->assertEquals(200,$this->client->getResponse()->getStatusCode());

        $medias  = $this->manager->findMediasByModelAndId('Post',2);
        $this->assertEquals(0, count($medias));
    }


    public function testValidUploadFileFormat()
    {
        $filePath = dirname(__DIR__).'/../Resources/public/images/elly.jpg';
        $file = new UploadedFile(
            $filePath,
            'elly.jpg',
            'image/jpeg',
            123
        );
        $this->client->request(
            'POST',
            '/admin/medias/add/Post/MvcBlogBundle/13',
            ['name'=>'Elly'],
            ['file'=>$file],
            ['HTTP_X-Requested-With' => 'XMLHttpRequest']
        );

        $this->assertEquals(200,$this->client->getResponse()->getStatusCode());
        $medias  = $this->manager->findMediasByModelAndId('Post',13);

        $this->assertEquals(1, count($medias));

    }

    public function testInvalidUploadFileFormat()
    {
        $filePath = dirname(__DIR__).'/../Resources/public/images/thumb.png';
        $file = new UploadedFile(
            $filePath,
            'thumb.png',
            'image/png',
            123
        );
        $this->client->request(
            'POST',
            '/admin/medias/add/Post/MvcBlogBundle/17',
            ['name'=>'thumb'],
            ['file'=>$file],
            ['HTTP_X-Requested-With' => 'XMLHttpRequest']
        );

        $this->assertEquals(500,$this->client->getResponse()->getStatusCode());

        $this->manager = $this->container->get('mk.media.manager');
        $medias  = $this->manager->findMediasByModelAndId('Post',17);
        $this->assertEquals(0, count($medias));
    }

}
