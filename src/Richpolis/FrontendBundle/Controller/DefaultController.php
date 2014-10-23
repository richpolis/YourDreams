<?php

namespace Richpolis\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Component\HttpFoundation\Request;

use Richpolis\UsuariosBundle\Form\UsuarioFrontendType;
use Richpolis\UsuariosBundle\Entity\Usuario;

use Richpolis\DreamsBundle\Form\DreamFrontendType;
use Richpolis\DreamsBundle\Entity\Dream;

class DefaultController extends Controller
{
	
    /**
     * @Route("/",name="homepage")
     * @Template()
     */
    public function indexAction(Request $request)
    {
		$em = $this->getDoctrine()->getManager();
		$dreams = $this->getRepository('DreamsBundle:Dream')->findBy(
			array('usuario'=>$this->getUser()),array('createdAt'=>'DESC'),
		);
		
        return array(
			'dreams'=>$dreams,
		);
    }

    /**
     * @Route("/registro",name="registro")
     * @Template()
     */
    public function registroAction(Request $request)
    {
        $usuario = new Usuario();
        $form = $this->createForm( new UsuarioFrontendType(), $usuario);
        $isNew = true;
        if($request->isMethod('POST')){
            $parametros = $request->request->all();
            $form->handleRequest($request);
            if($form->isValid()){
                $em = $this->getDoctrine()->getManager();
                $this->setSecurePassword($usuario);
                $usuario->setGrupo(Usuario::GRUPO_USUARIOS);
                $em->persist($usuario);
                $em->flush();
                $this->get('session')->getFlashBag()->add(
                     'notice',
                     'Ahora entra para crear tus historias.'
                );
                return $this->redirect($this->generateUrl('login'));
            }
        }
        
        return array(
            'form'      =>  $form->createView(),
            'titulo'    => 'Registro',
            'usuario'   => $usuario,
            'isNew'     =>  true,
        );
    }
	
	/**
     * @Route("/editar",name="editar_usuario")
     * @Template("FrontendBundle:Default:registro.html.twig")
     * @Method({"GET","POST"})
     */
    public function editarAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $usuario = $this->getUser();
        if (!$usuario) {
            return $this->redirect($this->generateUrl('login'));
        }
        $form = $this->createForm( new UsuarioFrontendType(), $usuario);
        $isNew = false;
        if($request->isMethod('POST')){
            //obtiene la contraseña
            $current_pass = $usuario->getPassword();
            $form->handleRequest($request);
            if($form->isValid()){
                if (null == $usuario->getPassword()) {
                    $usuario->setPassword($current_pass);
                } else {
                    $this->setSecurePassword($usuario);
                }
                $em->flush();
                
            }
        }
        
        return array(
            'form'      =>  $form->createView(),
            'usuario'   =>  $usuario,
            'titulo'    => 'Editar registro',
            'isNew'     =>  $isNew,
        );
    }
	
	/**
     * @Route("/mostrar/{id}",name="show_dream")
     * @Template()
     */
    public function mostrarAction(Request $request,$id)
    {
		$em = $this->getDoctrine()->getManager();
		$dream = $this->getRepository('DreamsBundle:Dream')->findOneBy(
			array('usuario'=>$this->getUser(),'id'=>$id),
		);
		
		if(null == $dream){
			return $this->redirect('homepage');
		}
		
		return array('dream'=>$dream);
		
    }
	
	/**
     * @Route("/dream",name="create_dream")
     * @Method({"GET","POST"})
     * @Template()
     */
    public function crearAction(Request $request)
    {
        $dream = new Dream();
        $form = $this->createForm( new DreamFrontendType(), $dream);
        $isNew = true;
        if($request->isMethod('POST')){
            $parametros = $request->request->all();
            $form->handleRequest($request);
            if($form->isValid()){
                $em = $this->getDoctrine()->getManager();
                $em->persist($dream);
                $em->flush();
                $this->get('session')->getFlashBag()->add(
                     'notice',
                     'Tu sueño ha sido creado.'
                );
                return $this->redirect($this->generateUrl('homepage'));
            }
        }
        
        return array(
            'form'      =>  $form->createView(),
            'titulo'    => 'Nuevo sueño',
            'usuario'   => $dream,
            'isNew'     =>  true,
        );
    }
	
	/**
     * @Route("/dream/{id}",name="update_dream")
     * @Template("FrontendBundle:Default:crear.html.twig")
     * @Method({"GET","POST","PUT"})
     */
    public function editarAction(Request $request,$id)
    {
        $em = $this->getDoctrine()->getManager();
        $dream = $em->getRepository('DreamsBundle:Dream')->findOneBy(
			array('usuario'=>$this->getUser(),'id'=>$id)
		);
		if(null == $dream){
			return $this->redirect($this->generateUrl('homepage'));
		}
		$form = $this->createForm( new UsuarioFrontendType(), $dream);
        $isNew = false;
        if($request->isMethod('POST')){
            $form->handleRequest($request);
            if($form->isValid()){
                $em->flush();
            }
        }
        
        return array(
            'form'      =>  $form->createView(),
            'dream'   	=>  $dream,
            'titulo'    => 'Editar sueño',
            'isNew'     =>  $isNew,
        );
    }
    
    /**
     * @Route("/dream/{id}",name="delete_dream")
     * @Method({"DELETE"})
     */
    public function eliminarHistoriaAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $dream = $em->getRepository('DreamsBundle:Dream')->findOneBy(
			array('usuario'=>$this->getUser(),'id'=>$id)
		);
		if(null == $dream){
            return new JsonResponse(json_encode(array('accion'=>'bat','mensaje'=>'El registro no existe')));
        }
        
        foreach($dream->getGalerias() as $galeria){
            $dream->removeGaleria($galeria);
			$em->remove($galeria);
            $em->flush();
        }
		
        $em->remove($dream);
        $em->flush();
        
        return new JsonResponse(json_encode(array('accion'=>'ok','mensaje'=>'El registro fue eliminado')));
    }

    /**
     * @Route("/buscar",name="find_dream")
     * @Template()
     */
    public function buscarAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        
		$buscar = $request->query->get('q','%');
		
        $dreams = $em->getRepository('DreamsBundle:Dream')
                        ->findDreams($buscar,$this->getUser(),"<>");
      	       
        return array(
            'dreams'    =>  $dreams,
        );
    }
	
	private function setSecurePassword(&$entity) {
        $encoder = $this->get('security.encoder_factory')->getEncoder($entity);
        $passwordCodificado = $encoder->encodePassword(
                    $entity->getPassword(),
                    $entity->getSalt()
        );
        $entity->setPassword($passwordCodificado);
    }
    
}

