<?php 

namespace Richpolis\UsuariosBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use Richpolis\UsuariosBundle\Entity\Usuario;

class UsuarioToNumberTransformer implements DataTransformerInterface
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
     * Transforms an object (issue) to a string (number).
     *
     * @param  Issue|null $issue
     * @return string
     */
    public function transform($usuario)
    {
        if (null === $usuario) {
            return "";
        }

        return $usuario->getId();
    }

    /**
     * Transforms a string (number) to an object (comentario).
     *
     * @param  string $number
     *
     * @return Comentario|null
     *
     * @throws TransformationFailedException if object (comentario) is not found.
     */
    public function reverseTransform($number)
    {
        if (!$number) {
            return null;
        }

        $usuario = $this->om
            ->getRepository('UsuariosBundle:Usuario')
            ->find($number);
        ;

        if (null === $usuario) {
            throw new TransformationFailedException(sprintf(
                'An Usuario with id "%s" does not exist!',
                $number
            ));
        }

        return $usuario;
    }
}