<?php
namespace Mykees\MediaBundle\Form\DataTransformer;

use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\DataTransformerInterface;
use Doctrine\Common\Persistence\ObjectManager;

class IdToMediaTransformer implements DataTransformerInterface
{
    private $om;

    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    // transforms the Media object to an int
    public function reverseTransform($val)
    {
        if (null === $val) {
            return '';
        }

        return (int)$val;
    }

    // transforms the Media id into an Media object
    public function transform($val)
    {
        if (!$val) {
            return null;
        }

        $media = $this->om->getRepository('MykeesMediaBundle:Media')->findOneBy(array('id' => $val));

        if (null === $media) {
            throw new TransformationFailedException(sprintf('A media with id %s could not be found!', $val));
        }

        return $media;
    }
}
