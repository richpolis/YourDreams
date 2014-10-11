<?php

namespace Richpolis\DreamsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DreamType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titulo')
            ->add('dream',null,array('label'=>'Descripción'))
            ->add('lugar','text',array('required'=>false))
            ->add('compartir')
            ->add('usuario')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Richpolis\DreamsBundle\Entity\Dream'
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
