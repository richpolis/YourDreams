<?php

namespace Richpolis\ComentariosBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * ComentarioRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ComentarioRepository extends EntityRepository
{
    public function queryFindComentarios($buscar = "",$usuario=null,$operador="=")
    {
        $em = $this->getEntityManager();
        if(strlen($buscar)==0){
            if($usuario == null){
                $consulta = $em->createQuery('SELECT h '
                    . 'FROM ComentariosBundle:Comentario h '
                    . 'ORDER BY h.titulo ASC');
            }else{
                $consulta = $em->createQuery("SELECT h "
                    . "FROM ComentariosBundle:Comentario h "
                    . "WHERE h.usuario$operador:usuario "    
                    . "ORDER BY h.titulo ASC");
                $consulta->setParameters(array(
                    'usuario' => $usuario->getId()
                ));
            }
        }else{
            if($usuario == null){
                $consulta = $em->createQuery("SELECT h "
                    . "FROM ComentariosBundle:Comentario h "
                    . "WHERE h.titulo LIKE :titulo OR h.lugar LIKE :lugar OR h.dream LIKE :dream "    
                    . "ORDER BY h.titulo ASC");
                $consulta->setParameters(array(
					'titulo' => "%".$buscar."%",
                	'lugar' => "%".$buscar."%",
                	'dream' => "%".$buscar."%"
                ));
            }else{
                $consulta = $em->createQuery("SELECT h "
                    . "FROM ComentariosBundle:Comentario h "   
                    . "WHERE h.titulo LIKE :dream  "
                    . "AND h.usuario$operador:usuario AND (h.titulo LIKE :titulo OR h.lugar LIKE :lugar OR h.dream LIKE :dream) "    
                    . "ORDER BY h.titulo ASC");
                $consulta->setParameters(array(
                    'usuario' => $usuario->getId(),
					'titulo' => "%".$buscar."%",
                	'lugar' => "%".$buscar."%",
                	'dream' => "%".$buscar."%"
                ));
            }
        }
        return $consulta;
    }
    
    public function findComentarios($buscar = "", $usuario=null, $operador="="){
        return $this->queryFindComentarios($buscar,$usuario,$operador)->getResult();
    }
}
