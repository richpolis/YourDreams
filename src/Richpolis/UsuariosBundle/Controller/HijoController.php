<?php

namespace Richpolis\UsuariosBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Richpolis\UsuariosBundle\Entity\Hijo;
use Richpolis\UsuariosBundle\Form\HijoType;

/**
 * Hijo controller.
 *
 * @Route("/admin/hijos")
 */
class HijoController extends Controller
{

    /**
     * Lists all Hijo entities.
     *
     * @Route("/", name="hijos")
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
        $query = $em->getRepository('UsuariosBundle:Hijo')->queryFindHijos($buscar);
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
                $query, $this->get('request')->query->get('page', 1),10, $options 
        );

        return compact('pagination');
    }
    /**
     * Creates a new Hijo entity.
     *
     * @Route("/", name="hijos_create")
     * @Method("POST")
     * @Template("UsuariosBundle:Hijo:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Hijo();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('hijos_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Hijo entity.
     *
     * @param Hijo $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Hijo $entity)
    {
        $form = $this->createForm(new HijoType(), $entity, array(
            'action' => $this->generateUrl('hijos_create'),
            'method' => 'POST',
        ));

        //$form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Hijo entity.
     *
     * @Route("/new", name="hijos_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Hijo();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Hijo entity.
     *
     * @Route("/{id}", name="hijos_show", requirements={"id": "\d+"})
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('UsuariosBundle:Hijo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Hijo entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Hijo entity.
     *
     * @Route("/{id}/edit", name="hijos_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('UsuariosBundle:Hijo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Hijo entity.');
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
    * Creates a form to edit a Hijo entity.
    *
    * @param Hijo $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Hijo $entity)
    {
        $form = $this->createForm(new HijoType(), $entity, array(
            'action' => $this->generateUrl('hijos_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        //$form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Hijo entity.
     *
     * @Route("/{id}", name="hijos_update")
     * @Method("PUT")
     * @Template("UsuariosBundle:Hijo:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('UsuariosBundle:Hijo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Hijo entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('hijos_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Hijo entity.
     *
     * @Route("/{id}", name="hijos_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('UsuariosBundle:Hijo')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Hijo entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('hijos'));
    }

    /**
     * Creates a form to delete a Hijo entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('hijos_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Eliminar','attr'=>array('class'=>'btn btn-danger')))
            ->getForm()
        ;
    }
    
    /**
     * Exporta la lista completa de hijos.
     *
     * @Route("/exportar", name="hijos_export")
     * @Method("GET")
     */
    public function exportarAction() {
        $hijos = $this->getDoctrine()
                ->getRepository('UsuariosBundle:Hijo')
                ->findHijos();

        $response = $this->render(
                'UsuariosBundle:Hijo:list.xls.twig', array('hijos' => $hijos)
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="export-hijos.xls"');
        return $response;
    }
}
