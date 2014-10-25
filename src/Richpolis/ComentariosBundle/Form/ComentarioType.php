<?php

namespace Richpolis\ComentariosBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Richpolis\ComentariosBundle\Form\DataTransformer\ComentarioToNumberTransformer;
use Richpolis\DreamsBundle\Form\DataTransformer\DreamToNumberTransformer;
use Richpolis\UsuariosBundle\Form\DataTransformer\UsuarioToNumberTransformer;

class ComentarioType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $em = $options['em'];
        $parentTransformer = new ComentarioToNumberTransformer($em);
        $dreamTransformer = new DreamToNumberTransformer($em);
        $usuarioTransformer = new UsuarioToNumberTransformer($em);

        $builder
                ->add('message', null, array(
                    'label' => 'Mensaje',
                    'required' => true,
                    'attr' => array(
                        'class' => 'validate[required] form-control placeholder',
                    )
                ))
                ->add('status', 'hidden')
                ->add('nivel', 'hidden')
                ->add($builder->create('usuario', 'hidden')->addModelTransformer($usuarioTransformer))
                ->add($builder->create('dream', 'hidden')->addModelTransformer($dreamTransformer))
                ->add($builder->create('parent', 'hidden')->addModelTransformer($parentTransformer))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver
            ->setDefaults(array(
                'data_class' => 'Richpolis\ComentariosBundle\Entity\Comentario'
            ))
            ->setRequired(array('em'))
            ->setAllowedTypes(array('em' => 'Doctrine\Common\Persistence\ObjectManager'))
        ;
    }

    /**
     * @return string
     */
    public function getName() {
        return '';
    }

}
