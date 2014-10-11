<?php

/*
 * (c) Javier Eguiluz <javier.eguiluz@gmail.com>
 * 
 * Modificado por Ricardo Alcantara <richpolis@gmail.com>
 *
 */

namespace Richpolis\BackendBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Richpolis\UsuariosBundle\Entity\Usuario;


/**
 * Fixtures de la entidad Usuario.
 */
class Usuarios extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    public function getOrder()
    {
        return 10;
    }

    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        // Obtener todas las ciudades de la base de datos
        $grupoAdmin = Usuario::GRUPO_ADMIN;
        $grupoUsuario = Usuario::GRUPO_USUARIOS;
		
        // usuario Richpolis 
        $richpolis = new Usuario();
        $richpolis->setEmail('richpolis@gmail.com');
        $richpolis->setSalt(base_convert(sha1(uniqid(mt_rand(), true)), 16, 36));
        $passwordEnClaro = 'sfR0xC4s';
        $encoder = $this->container->get('security.encoder_factory')->getEncoder($richpolis);
        $passwordCodificado = $encoder->encodePassword($passwordEnClaro, $richpolis->getSalt());
        $richpolis->setPassword($passwordCodificado);
		$richpolis->setNombre("Ricardo");
		$richpolis->setApellido("Alcantara");
		$richpolis->setGrupo($grupoAdmin);
        $manager->persist($richpolis);
            
        // usuario Administrador
        $usuarioAdmin = new Usuario();
        $usuarioAdmin->setEmail('admin@yourdreams.com');
        $usuarioAdmin->setSalt(base_convert(sha1(uniqid(mt_rand(), true)), 16, 36));
        $passwordEnClaro = 'admin';
        $encorder = $this->container->get('security.encoder_factory')->getEncoder($usuarioAdmin);
        $passwordCodificado = $encoder->encodePassword($passwordEnClaro, $usuarioAdmin->getSalt());
        $usuarioAdmin->setPassword($passwordCodificado);
		$usuarioAdmin->setNombre("Admin");
		$usuarioAdmin->setApellido("YourDreams");
		$richpolis->setGrupo($grupoAdmin);
        $manager->persist($usuarioAdmin);
		$manager->flush();
		

        // usuario Normal
        $usuarioNormal = new Usuario();
        $usuarioNormal->setEmail('usuario1@yourdreams.com');
        $usuarioNormal->setSalt(base_convert(sha1(uniqid(mt_rand(), true)), 16, 36));
        $passwordEnClaro = '12345678';
        $encorder = $this->container->get('security.encoder_factory')->getEncoder($usuarioNormal);
        $passwordCodificado = $encoder->encodePassword($passwordEnClaro, $usuarioNormal->getSalt());
        $usuarioNormal->setPassword($passwordCodificado);
        $usuarioNormal->setNombre("Usuario1");
		$usuarioNormal->setApellido("YourDreams");
		$richpolis->setGrupo($grupoUsuario);
        $manager->persist($usuarioNormal);
        $manager->flush();
    }

    
}