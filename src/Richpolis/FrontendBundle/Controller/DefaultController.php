<?php

namespace Richpolis\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;


class DefaultController extends Controller
{
	
    /**
     * @Route("/",name="homepage")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        return array();
    }

    /**
     * @Route("/registro",name="registro")
     * @Template()
     */
    public function registroAction(Request $request)
    {
        return array();
    }
	
	/**
     * @Route("/mostrar/{id}",name="show_dream")
     * @Template()
     */
    public function mostrarAction(Request $request,$id)
    {
        return array('entity'=>$id);
    }
	
	/**
     * @Route("/crear",name="create_dream")
     * @Template()
     */
    public function crearAction(Request $request)
    {
        return array();
    }
    
}
