<?php

namespace Richpolis\DreamsBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Richpolis\DreamsBundle\Entity\Componente;
use Richpolis\DreamsBundle\Form\ComponenteType;

/**
 * Componente controller.
 *
 * @Route("/admin/componentes")
 */
class ComponenteController extends Controller
{

    /**
     * Lists all Componente entities.
     *
     * @Route("/", name="componentes")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('DreamsBundle:Componente')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Componente entity.
     *
     * @Route("/", name="componentes_create")
     * @Method("POST")
     * @Template("DreamsBundle:Componente:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Componente();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('componentes_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Componente entity.
     *
     * @param Componente $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Componente $entity)
    {
        $form = $this->createForm(new ComponenteType(), $entity, array(
            'action' => $this->generateUrl('componentes_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Componente entity.
     *
     * @Route("/new", name="componentes_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Componente();
        
        $max = $em->getRepository('DreamsBundle:Componente')->getMaxPosicion();
        if($max == null){
            $max=0;
        }
        $entity->setPosition($max+1);
        
        $form   = $this->createCreateForm($entity);
        
        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Componente entity.
     *
     * @Route("/{id}", name="componentes_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('DreamsBundle:Componente')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Componente entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Componente entity.
     *
     * @Route("/{id}/edit", name="componentes_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('DreamsBundle:Componente')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Componente entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Componente entity.
    *
    * @param Componente $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Componente $entity)
    {
        $form = $this->createForm(new ComponenteType(), $entity, array(
            'action' => $this->generateUrl('componentes_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Componente entity.
     *
     * @Route("/{id}", name="componentes_update")
     * @Method("PUT")
     * @Template("DreamsBundle:Componente:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('DreamsBundle:Componente')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Componente entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('componentes_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Componente entity.
     *
     * @Route("/{id}", name="componentes_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('DreamsBundle:Componente')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Componente entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('componentes'));
    }

    /**
     * Creates a form to delete a Componente entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('componentes_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
