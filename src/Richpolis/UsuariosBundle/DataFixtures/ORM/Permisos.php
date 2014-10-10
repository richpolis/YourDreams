<?php

/*
 * Creado por Ricardo Alcantara <richpolis@gmail.com>
 *
 */

namespace Richpolis\BackendBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Richpolis\UsuariosBundle\Entity\Roles;

/**
 * Fixtures de la entidad Roles.
 */
class Permisos extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    public function getOrder()
    {
        return 00;
    }

    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $rolAdmin = new Roles();
		$rolAdmin->setNombre('ROLE_ADMIN');
		$rolUsuario = new Roles();
		$rolUsuario->setNombre('ROLE_USUARIO');
		
		$manager->persist($rolAdmin);
		$manager->persist($rolUsuario);
		
		
        $manager->flush();
    }

    
}