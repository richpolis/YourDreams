<?php

namespace Richpolis\DreamsBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Richpolis\DreamsBundle\Entity\Dream;
use Richpolis\DreamsBundle\Form\DreamType;

use Richpolis\BackendBundle\Utils\Richsys as RpsStms;

use Richpolis\BackendBundle\Utils\qqFileUploader;
use Richpolis\GaleriasBundle\Entity\Galeria;

/**
 * Dream controller.
 *
 * @Route("/admin/dreams")
 */
class DreamController extends Controller
{

    /**
     * Lists all Dream entities.
     *
     * @Route("/", name="dreams")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $buscar = $request->get('buscar','');
        if(strlen($buscar)>0){
                $options = array('filterParam'=>'buscar','filterValue'=>$buscar);
        }else{
                $options = array();
        }
        $query = $em->getRepository('DreamsBundle:Dream')->queryFindDreams($buscar);
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query, $this->get('request')->query->get('page', 1),10, $options 
        );

        return compact('pagination');
    }
    /**
     * Creates a new Dream entity.
     *
     * @Route("/", name="dreams_create")
     * @Method("POST")
     * @Template("DreamsBundle:Dream:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Dream();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('dreams_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Dream entity.
     *
     * @param Dream $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Dream $entity)
    {
        $form = $this->createForm(new DreamType(), $entity, array(
            'action' => $this->generateUrl('dreams_create'),
            'method' => 'POST',
        ));

        //$form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Dream entity.
     *
     * @Route("/new", name="dreams_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Dream();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Dream entity.
     *
     * @Route("/{id}", name="dreams_show", requirements={"id": "\d+"})
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('DreamsBundle:Dream')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Dream entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
            'get_galerias' =>$this->generateUrl('dreams_galerias',array('id'=>$entity->getId()),true),
            'post_galerias' =>$this->generateUrl('dreams_galerias_upload', array('id'=>$entity->getId()),true),
            'post_galerias_link_video' =>$this->generateUrl('dreams_galerias_link_video', array('id'=>$entity->getId()),true),
            'url_delete' => $this->generateUrl('dreams_galerias_delete',array('id'=>$entity->getId(),'idGaleria'=>'0'),true),
        );
    }

    /**
     * Displays a form to edit an existing Dream entity.
     *
     * @Route("/{id}/edit", name="dreams_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('DreamsBundle:Dream')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Dream entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Dream entity.
    *
    * @param Dream $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Dream $entity)
    {
        $form = $this->createForm(new DreamType(), $entity, array(
            'action' => $this->generateUrl('dreams_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        //$form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Dream entity.
     *
     * @Route("/{id}", name="dreams_update")
     * @Method("PUT")
     * @Template("DreamsBundle:Dream:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('DreamsBundle:Dream')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Dream entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('dreams_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Dream entity.
     *
     * @Route("/{id}", name="dreams_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('DreamsBundle:Dream')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Dream entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('dreams'));
    }

    /**
     * Creates a form to delete a Dream entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('dreams_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete','attr'=>array('class'=>'btn btn-danger')))
            ->getForm()
        ;
    }
    
    /**
     * Exporta la lista completa de usuarios.
     *
     * @Route("/exportar", name="dreams_export")
     * @Method("GET")
     */
    public function exportarAction() {
        $dreams = $this->getDoctrine()
                ->getRepository('DreamsBundle:Dream')
                ->findDreams();

        $response = $this->render(
                'DreamsBundle:Dream:list.xls.twig', array('dreams' => $dreams)
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="export-dreams.xls"');
        return $response;
    }

    /**
     * Lists all Dream galerias entities.
     *
     * @Route("/{id}/galerias", name="dreams_galerias", requirements={"id" = "\d+"})
     * @Method("GET")
     */
    public function galeriasAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $dream = $em->getRepository('DreamsBundle:Dream')->find($id);
        
        $galerias = $dream->getGalerias();
        $get_galerias = $this->generateUrl('dreams_galerias',array('id'=>$dream->getId()),true);
        $post_galerias = $this->generateUrl('dreams_galerias_upload', array('id'=>$dream->getId()),true);
        $post_galerias_link_video = $this->generateUrl('dreams_galerias_link_video', array('id'=>$dream->getId()),true);
        $url_delete = $this->generateUrl('dreams_galerias_delete',array('id'=>$dream->getId(),'idGaleria'=>'0'),true);
        
        return $this->render('GaleriasBundle:Galeria:galerias.html.twig', array(
            'galerias'=>$galerias,
            'get_galerias' =>$get_galerias,
            'post_galerias' =>$post_galerias,
            'post_galerias_link_video' =>$post_galerias_link_video,
            'url_delete' => $url_delete,
        ));
    }
    
    /**
     * Crea una galeria de una dream.
     *
     * @Route("/{id}/galerias", name="dreams_galerias_upload", requirements={"id" = "\d+"})
     * @Method("POST")
     */
    public function galeriasUploadAction(Request $request,$id){
        $em = $this->getDoctrine()->getManager();
        $dream=$em->getRepository('DreamsBundle:Dream')->find($id);
       
        if(!$request->request->has('tipoArchivo')){ 
            // list of valid extensions, ex. array("jpeg", "xml", "bmp")
            $allowedExtensions = array("jpeg","png","gif","jpg");
            // max file size in bytes
            $sizeLimit = 6 * 1024 * 1024;
            $uploader = new qqFileUploader($allowedExtensions, $sizeLimit,$request->server);
            $uploads= $this->container->getParameter('richpolis.uploads');
            $result = $uploader->handleUpload($uploads."/galerias/");
            // to pass data through iframe you will need to encode all html tags
            /*****************************************************************/
            //$file = $request->getParameter("qqfile");
            $max = $em->getRepository('GaleriasBundle:Galeria')->getMaxPosicion();
            if($max == null){
                $max=0;
            }
            if(isset($result["success"])){
                $registro = new Galeria();
                $registro->setArchivo($result["filename"]);
                $registro->setThumbnail($result["filename"]);
                $registro->setTitulo($result["titulo"]);
                $registro->setIsActive(true);
                $registro->setPosition($max+1);
                $registro->setTipoArchivo(RpsStms::TIPO_ARCHIVO_IMAGEN);
                //unset($result["filename"],$result['original'],$result['titulo'],$result['contenido']);
                $em->persist($registro);
                $registro->crearThumbnail();    
                $dream->getGalerias()->add($registro);
                $em->flush();
            }
        }else{
            $result = $request->request->all(); 
            $registro = new Galeria();
            $registro->setArchivo($result["archivo"]);
            $registro->setIsActive($result['isActive']);
            $registro->setPosition($result['position']);
            $registro->setTipoArchivo($result['tipoArchivo']);
            $em->persist($registro);
            $dream->getGalerias()->add($registro);
            $em->flush();  
        }
        
        $response = new \Symfony\Component\HttpFoundation\JsonResponse();
        $response->setData($result);
        return $response;
    }
    
    /**
     * Crea una galeria link video de una dream.
     *
     * @Route("/{id}/galerias/link/video", name="dreams_galerias_link_video", requirements={"id" = "\d+"})
     * @Method({"POST","GET"})
     */
    public function galeriasLinkVideoAction(Request $request,$id){
        $em = $this->getDoctrine()->getManager();
        $dream=$em->getRepository('DreamsBundle:Dream')->find($id);
        $parameters = $request->request->all();
      
        if(isset($parameters['archivo'])){ 
            $registro = new Galeria();
            $registro->setArchivo($parameters['archivo']);
            $registro->setIsActive($parameters['isActive']);
            $registro->setPosition($parameters['position']);
            $registro->setTipoArchivo($parameters['tipoArchivo']);
            $em->persist($registro);
            $dream->getGalerias()->add($registro);
            $em->flush();  
        }
        $response = new \Symfony\Component\HttpFoundation\JsonResponse();
        $response->setData($parameters);
        return $response;
    }
    
    /**
     * Deletes una Galeria entity de una Dream.
     *
     * @Route("/{id}/galerias/{idGaleria}", name="dreams_galerias_delete", requirements={"id" = "\d+"})
     * @Method("DELETE")
     */
    public function deleteGaleriaAction(Request $request, $id, $idGaleria)
    {
            $em = $this->getDoctrine()->getManager();
            $dream = $em->getRepository('DreamsBundle:Dream')->find($id);
            $galeria = $em->getRepository('GaleriasBundle:Galeria')->find(intval($idGaleria));

            if (!$dream) {
                throw $this->createNotFoundException('Unable to find Dream entity.');
            }
            
            $dream->getGalerias()->removeElement($galeria);
            $em->remove($galeria);
            $em->flush();
        

        $response = new \Symfony\Component\HttpFoundation\JsonResponse();
        $response->setData(array("ok"=>true));
        return $response;
    }
}
