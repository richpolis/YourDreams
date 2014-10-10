<?php

namespace Richpolis\UsuariosBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * UsuarioRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UsuarioRepository extends EntityRepository
{
    public function queryFindUsuarios($buscar = "")
    {
        $em = $this->getEntityManager();
        if(strlen($buscar)==0){
            $consulta = $em->createQuery('SELECT u '
                . 'FROM UsuariosBundle:Usuario u '
                . 'ORDER BY u.username ASC, u.ciudad ASC');
        }else{
            $consulta = $em->createQuery("SELECT u "
                . "FROM UsuariosBundle:Usuario u "
                . "WHERE u.username LIKE :username OR u.email LIKE :email OR u.ciudad LIKE :ciudad OR u.biografia LIKE :biografia "
                . "ORDER BY u.username ASC, u.ciudad ASC");
            $consulta->setParameters(array(
                'username' => "%".$buscar."%",
                'email' => "%".$buscar."%",
                'ciudad' => "%".$buscar."%",
                'biografia' => "%".$buscar."%"
            ));
        }
        return $consulta;
    }
    
    public function findUsuarios($buscar = ""){
        return $this->queryFindUsuarios($buscar)->getResult();
    }
}
