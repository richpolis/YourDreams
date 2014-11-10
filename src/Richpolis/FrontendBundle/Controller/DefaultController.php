<?php

namespace Richpolis\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Richpolis\UsuariosBundle\Form\UsuarioFrontendType;
use Richpolis\UsuariosBundle\Entity\Usuario;
use Richpolis\DreamsBundle\Form\DreamFrontendType;
use Richpolis\DreamsBundle\Entity\Dream;
use Richpolis\ComentariosBundle\Form\ComentarioType;
use Richpolis\ComentariosBundle\Entity\Comentario;
use Richpolis\ComentariosBundle\Entity\Mensaje;
use Richpolis\BackendBundle\Utils\Richsys as RpsStms;
use Richpolis\BackendBundle\Utils\qqFileUploader;
use Richpolis\GaleriasBundle\Entity\Galeria;

class DefaultController extends Controller {

    /**
     * @Route("/s/{clave}",name="share_dream")
     * @Template("FrontendBundle:Default:mostrar.html.twig")
     * @Method({"GET"})
     */
    public function shareDreamAction(Request $request, $clave) {
        $em = $this->getDoctrine()->getManager();
        $dream = $em->getRepository('DreamsBundle:Dream')->findOneBy(
                array('clave' => $clave)
        );

        if (null == $dream) {
            return $this->redirect($this->generateUrl('login'));
        }

        $comentarios = $em->getRepository('ComentariosBundle:Comentario')->findBy(
                array('dream' => $dream), array('createdAt' => 'DESC')
        );

        if (null !== $this->getUser()) {
            $this->findMensajeAUsuario($this->getUser(), $dream, $em);
        }

        return compact('dream', 'comentarios');
    }

    /**
     * @Route("/",name="homepage")
     * @Template()
     */
    public function indexAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $dreams = $em->getRepository('DreamsBundle:Dream')->findBy(
                array('usuario' => $this->getUser()), array('createdAt' => 'DESC')
        );

        $mensajes = $em->getRepository('ComentariosBundle:Mensaje')->findBy(
                array('paraUsuario' => $this->getUser(), 'status' => true,)
        );

        return array(
            'dreams' => $dreams,
            'mensajes' => $mensajes,
        );
    }

    /**
     * @Route("/registro",name="registro")
     * @Template()
     */
    public function registroAction(Request $request) {
        $usuario = new Usuario();
        $form = $this->createForm(new UsuarioFrontendType(), $usuario);
        $isNew = true;
        if ($request->isMethod('POST')) {
            $parametros = $request->request->all();
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $this->setSecurePassword($usuario);
                $usuario->setGrupo(Usuario::GRUPO_USUARIOS);
                $em->persist($usuario);
                $em->flush();
                $this->get('session')->getFlashBag()->add(
                        'notice', 'Ahora entra para crear tus historias.'
                );
                return $this->redirect($this->generateUrl('login'));
            }
        }

        return array(
            'form' => $form->createView(),
            'titulo' => 'Registro',
            'usuario' => $usuario,
            'isNew' => true,
        );
    }

    /**
     * @Route("/editar",name="editar_usuario")
     * @Template("FrontendBundle:Default:registro.html.twig")
     * @Method({"GET","POST"})
     */
    public function editarAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $usuario = $this->getUser();
        if (!$usuario) {
            return $this->redirect($this->generateUrl('login'));
        }
        $form = $this->createForm(new UsuarioFrontendType(), $usuario);
        $isNew = false;
        if ($request->isMethod('POST')) {
            //obtiene la contraseña
            $current_pass = $usuario->getPassword();
            $form->handleRequest($request);
            if ($form->isValid()) {
                if (null == $usuario->getPassword()) {
                    $usuario->setPassword($current_pass);
                } else {
                    $this->setSecurePassword($usuario);
                }
                $em->flush();
            }
        }

        return array(
            'form' => $form->createView(),
            'usuario' => $usuario,
            'titulo' => 'Editar registro',
            'isNew' => $isNew,
        );
    }

    /**
     * @Route("/mostrar/{id}",name="show_dream",requirements={"id": "\d+"})
     * @Template("FrontendBundle:Default:mostrar.html.twig")
     * @Method({"GET"})
     */
    public function showDreamAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        $dream = $em->getRepository('DreamsBundle:Dream')->findOneBy(
                array('usuario' => $this->getUser(), 'id' => $id)
        );

        if (null == $dream) {
            return $this->redirect('homepage');
        }

        $comentarios = $em->getRepository('ComentariosBundle:Comentario')
                ->findBy(array('dream' => $dream));

        return compact('dream', 'comentarios');
    }

    /**
     * @Route("/dream",name="create_dream")
     * @Method({"GET","POST"})
     * @Template("FrontendBundle:Default:crear.html.twig")
     */
    public function createDreamAction(Request $request) {
        $dream = new Dream();
        $dream->setUsuario($this->getUser());
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(new DreamFrontendType(), $dream, array(
            'em' => $em,
        ));
        $isNew = true;
        if ($request->isMethod('POST')) {
            $parametros = $request->request->all();
            $form->handleRequest($request);
            if ($form->isValid()) {
                //salvamos el upload
                $this->saveGaleriaDream($dream, $em);
                //creamos el sueño
                $em->persist($dream);
                $em->flush();
                //creamos la clave para compartir
                $dream->setClave(md5($dream->getId()));
                $em->flush();
                $this->get('session')->getFlashBag()->add(
                        'notice', 'Tu sueño ha sido creado.'
                );
                return $this->redirect($this->generateUrl('homepage'));
            }
        }

        return array(
            'form' => $form->createView(),
            'titulo' => 'Nuevo sueño',
            'dream' => $dream,
            'isNew' => true,
        );
    }

    /**
     * @Route("/dream/{id}",name="update_dream",requirements={"id": "\d+"})
     * @Template("FrontendBundle:Default:crear.html.twig")
     * @Method({"GET","POST","PUT"})
     */
    public function updateDreamAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        $dream = $em->getRepository('DreamsBundle:Dream')->findOneBy(
                array('usuario' => $this->getUser(), 'id' => $id)
        );
        if (null == $dream) {
            return $this->redirect($this->generateUrl('homepage'));
        }
        $form = $this->createForm(new DreamFrontendType(), $dream, array(
            'em' => $this->getDoctrine()->getManager(),
        ));
        $isNew = false;
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $this->saveGaleriaDream($dream, $em);
                $em->flush();
            }
        }

        return array(
            'form' => $form->createView(),
            'dream' => $dream,
            'titulo' => 'Editar sueño',
            'isNew' => $isNew,
        );
    }

    /**
     * @Route("/dream/{id}",name="delete_dream",requirements={"id": "\d+"})
     * @Method({"DELETE"})
     */
    public function deleteDreamAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        $dream = $em->getRepository('DreamsBundle:Dream')->findOneBy(
                array('usuario' => $this->getUser(), 'id' => $id)
        );
        if (null == $dream) {
            return new JsonResponse(json_encode(array('accion' => 'bat', 'mensaje' => 'El registro no existe')));
        }

        foreach ($dream->getGalerias() as $galeria) {
            $dream->removeGaleria($galeria);
            $em->remove($galeria);
        }

        $comentarios = $em->getRepository('ComentariosBundle:Comentario')->findBy(
                array('dream' => $dream), array('createdAt' => 'DESC')
        );

        foreach ($comentarios as $comentario) {
            $em->remove($comentario);
        }

        $em->remove($dream);
        $em->flush();

        return new JsonResponse(json_encode(array('accion' => 'ok', 'mensaje' => 'El registro fue eliminado')));
    }

    /**
     * @Route("/buscar",name="find_dream")
     * @Template("FrontendBundle:Default:buscar.html.twig")
     * @Method({"GET","POST"})
     */
    public function buscarAction(Request $request) {
        $em = $this->getDoctrine()->getManager();

        $buscar = $request->get("q", "");

        if (strlen($buscar) > 0) {
            $dreams = $em->getRepository('DreamsBundle:Dream')
                    ->findDreams($buscar, $this->getUser(), "<>");
        } else {
            $dreams = array();
        }

        return array(
            'dreams' => $dreams,
        );
    }

    private function setSecurePassword(&$entity) {
        $encoder = $this->get('security.encoder_factory')->getEncoder($entity);
        $passwordCodificado = $encoder->encodePassword(
                $entity->getPassword(), $entity->getSalt()
        );
        $entity->setPassword($passwordCodificado);
    }

    private function saveGaleriaDream(Dream $dream, $em) {
        //archivo upload
        $file = $dream->getFile();
        if (null != $file) {
            $galeria = new \Richpolis\GaleriasBundle\Entity\Galeria();
            $galeria->setFile($file);
            $galeria->setPosition(0);
            $galeria->setTipoArchivo(RpsStms::getTipoArchivo($file->getClientOriginalName()));
            $galeria->setTitulo($file->getClientOriginalName());

            $dream->addGaleria($galeria);
            $em->persist($galeria);
        }
    }

    /**
     * @Route("/dream/{id}/mensaje",name="create_dream_mensaje",requirements={"id": "\d+"})
     * @Method({"GET","POST","PUT"})
     */
    public function createDreamMensajeAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        $dream = $em->getRepository('DreamsBundle:Dream')->find($id);
        if (null == $dream) {
            return new JsonResponse(json_encode(array('respuesta' => 'bat', 'mensaje' => 'El registro no existe')));
        }
        $comentario = new Comentario();
        $comentario->setDream($dream);
        //parent, comentario anterior, si no existe es null.
        if ($request->query->has('parent')) {
            $parent = $em->getRepository('ComentariosBundle:Comentario')
                    ->find($request->query->get('parent'));
            $comentario->setParent($parent);
        } else {
            $parent = null;
        }
        $comentario->setUsuario($this->getUser());
        $form = $this->createForm(new ComentarioType(), $comentario, array(
            'em' => $this->getDoctrine()->getManager(),
        ));
        $isNew = true;

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em->persist($comentario);
                $em->flush();
                $parent = $comentario->getParent();
                if ($parent == null) {
                    $this->agregarMensajeAUsuario($comentario->getDream()->getUsuario(), $this->getUser(), $dream, $em);
                } else {
                    $this->agregarMensajeAUsuario($parent->getUsuario(), $this->getUser(), $dream, $em);
                }
                $response = new JsonResponse(json_encode(array(
                            'html' => '',
                            'respuesta' => 'creado',
                )));
                return $response;
            }
        }

        $response = new JsonResponse(json_encode(array(
                    'form' => $this->renderView('FrontendBundle:Default:formComentario.html.twig', array(
                        'rutaAction' => $this->generateUrl('create_dream_mensaje', array('id' => $dream->getId())),
                        'form' => $form->createView(),
                        'comentario' => $comentario,
                    )),
                    'respuesta' => 'nuevo',
        )));
        return $response;
    }

    private function agregarMensajeAUsuario($para, $de, &$dream, &$em) {
        $mensaje = $em->getRepository('ComentariosBundle:Mensaje')
                ->findOneBy(array(
            'paraUsuario' => $para,
            'deUsuario' => $de,
            'dream' => $dream,
        ));
        if (null == $mensaje) {
            $mensaje = new Mensaje();
            $mensaje->setParaUsuario($para);
            $mensaje->setDeUsuario($de);
            $mensaje->setDream($dream);
            $mensaje->setStatus(true);
            $em->persist($mensaje);
        } else {
            $mensaje->setStatus(true);
        }

        $em->flush();
    }

    private function findMensajeAUsuario($para, &$dream, &$em) {
        $mensaje = $em->getRepository('ComentariosBundle:Mensaje')
                ->findOneBy(array(
            'paraUsuario' => $para,
            'dream' => $dream,
        ));
        if (null !== $mensaje) {
            $mensaje->setStatus(false);
            $em->flush();
        }
    }

    /**
     * Crea una galeria de una dream.
     *
     * @Route("/upload/galerias", name="galerias_upload")
     * @Method("POST")
     */
    public function galeriasUploadAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        $dream = $em->getRepository('DreamsBundle:Dream')->find($id);

        if (!$request->request->has('tipoArchivo')) {
            // list of valid extensions, ex. array("jpeg", "xml", "bmp")
            $allowedExtensions = array("jpeg", "png", "gif", "jpg");
            // max file size in bytes
            $sizeLimit = 6 * 1024 * 1024;
            $uploader = new qqFileUploader($allowedExtensions, $sizeLimit, $request->server);
            $uploads = $this->container->getParameter('richpolis.uploads');
            $result = $uploader->handleUpload($uploads . "/galerias/");
            // to pass data through iframe you will need to encode all html tags
            /*             * ************************************************************** */
            //$file = $request->getParameter("qqfile");
            $max = $em->getRepository('GaleriasBundle:Galeria')->getMaxPosicion();
            if ($max == null) {
                $max = 0;
            }
            if (isset($result["success"])) {
                $registro = new Galeria();
                $registro->setArchivo($result["filename"]);
                $registro->setThumbnail($result["filename"]);
                $registro->setTitulo($result["titulo"]);
                $registro->setIsActive(true);
                $registro->setPosition($max + 1);
                $registro->setTipoArchivo(RpsStms::TIPO_ARCHIVO_IMAGEN);
                //unset($result["filename"],$result['original'],$result['titulo'],$result['contenido']);
                $em->persist($registro);
                $registro->crearThumbnail();
                $dream->getGalerias()->add($registro);
                $em->flush();
            }
        } else {
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
     * @Route("/upload/galerias/link/video", name="dreams_galerias_link_video")
     * @Method({"POST","GET"})
     */
    public function galeriasLinkVideoAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        $dream = $em->getRepository('DreamsBundle:Dream')->find($id);
        $parameters = $request->request->all();

        if (isset($parameters['archivo'])) {
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

}
