<?php

namespace Richpolis\UsuariosBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Richpolis\UsuariosBundle\Entity\Usuario;

class UsuarioType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre')
            ->add('apellido','text',array('required'=>false))
            ->add('telefono','text',array('required'=>false))
            ->add('email')
            ->add('password','password',array('required'=>false))
            ->add('salt','hidden')
            ->add('observaciones',null,array('required'=>false,'label'=>'Observaciones'))
            ->add('imagen','hidden')
            ->add('isActive')
            ->add('grupo','choice',array(
                'label'=>'Grupo',
                'empty_value'=>false,
                'choices'=>Usuario::getArrayTipoGrupo(),
                'preferred_choices'=>Usuario::getPreferedTipoGrupo(),
                'attr'=>array(
                    'class'=>'validate[required] form-control placeholder',
                    'placeholder'=>'Grupo',
                )))
            ->add('facebook_id','hidden')
            ->add('facebook_access_token','hidden')
            ->add('twitter_id','hidden')
            ->add('twitter_access_token','hidden')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Richpolis\UsuariosBundle\Entity\Usuario'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'richpolis_usuariosbundle_usuario';
    }
}
