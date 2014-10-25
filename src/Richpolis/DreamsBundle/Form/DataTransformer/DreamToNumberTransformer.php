<?php 

namespace Richpolis\DreamsBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use Richpolis\DreamsBundle\Entity\Dream;

class DreamToNumberTransformer implements DataTransformerInterface
{
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    /**
     * Transforms an object (dream) to a string (number).
     *
     * @param  Issue|null $issue
     * @return string
     */
    public function transform($dream)
    {
        if (null === $dream) {
            return "";
        }

        return $dream->getId();
    }

    /**
     * Transforms a string (number) to an object (dream).
     *
     * @param  string $number
     *
     * @return Comentario|null
     *
     * @throws TransformationFailedException if object (dream) is not found.
     */
    public function reverseTransform($number)
    {
        if (!$number) {
            return null;
        }

        $dream = $this->om
            ->getRepository('DreamsBundle:Dream')
            ->find($number);
        ;

        if (null === $dream) {
            throw new TransformationFailedException(sprintf(
                'An Dream with id "%s" does not exist!',
                $number
            ));
        }

        return $dream;
    }
}