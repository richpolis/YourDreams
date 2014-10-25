<?php

namespace Richpolis\DreamsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Richpolis\UsuariosBundle\Form\DataTransformer\UsuarioToNumberTransformer;

class DreamFrontendType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $em = $options['em'];
        $transformer = new UsuarioToNumberTransformer($em);
        
        $builder
            ->add('titulo')
            ->add('dream',null,array('label'=>'Descripción'))
            ->add('lugar','text',array('required'=>false))
            ->add('compartir')
	    ->add('file','file',array('label'=>'Archivos','required'=>false))
            ->add($builder->create('usuario', 'hidden')->addViewTransformer($transformer))
        ;

    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'data_class' => 'Richpolis\DreamsBundle\Entity\Dream',
            ))
            ->setRequired(array(
                'em',
            ))
            ->setAllowedTypes(array(
                'em' => 'Doctrine\Common\Persistence\ObjectManager',
            ));
                
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'richpolis_dreamsbundle_dream';
    }
}
