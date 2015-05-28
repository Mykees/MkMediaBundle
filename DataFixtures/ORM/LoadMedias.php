<?php
/**
 * Created by PhpStorm.
 * User: Rafidion Michael
 * Date: 28/11/2014
 * Time: 17:38
 */

namespace Mykees\MediaBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Mykees\MediaBundle\Entity\Media;

class LoadMedias implements FixtureInterface {

    public function load(ObjectManager $manager)
    {
        $media = new Media();
        $media->setName('test');
        $media->setFile('2014/11/test.jpg');
        $media->setMediableModel('Post');
        $media->setMediableId(1);

        $media2 = new Media();
        $media2->setName('test2');
        $media2->setFile('2014/11/test2.jpg');
        $media2->setMediableModel('Post');
        $media2->setMediableId(1);

        $media3 = new Media();
        $media3->setName('test3');
        $media3->setFile('2014/11/test3.jpg');
        $media3->setMediableModel('Post');
        $media3->setMediableId(2);

        $manager->persist($media);
        $manager->flush();
        $manager->persist($media2);
        $manager->flush();
        $manager->persist($media3);
        $manager->flush();

    }

} 