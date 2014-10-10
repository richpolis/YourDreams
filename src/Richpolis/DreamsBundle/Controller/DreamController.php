<?php

namespace Richpolis\DreamsBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Richpolis\DreamsBundle\Entity\Dream;
use Richpolis\DreamsBundle\Form\DreamType;

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
            $em->setClave(md5($entity->getId()));
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
}
