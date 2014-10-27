<?php

namespace Richpolis\ComentariosBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Mensaje
 *
 * @ORM\Table(name="mensajes")
 * @ORM\Entity(repositoryClass="Richpolis\ComentariosBundle\Entity\MensajeRepository")
 */
class Mensaje
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\OneToOne(targetEntity="Richpolis\UsuariosBundle\Entity\Usuario")
     * @ORM\JoinColumn(name="de_usuario_id", referencedColumnName="id")
     */
    private $deUsuario;

    /**
     * @var integer
     *
     * @ORM\OneToOne(targetEntity="Richpolis\UsuariosBundle\Entity\Usuario")
     * @ORM\JoinColumn(name="para_usuario_id", referencedColumnName="id")
     */
    private $paraUsuario;

    /**
     * @var boolean
     * @todo Si el status del mensaje, si es nuevo o no. 
     *
     * @ORM\Column(name="status", type="boolean")
     */
    private $status;

    /**
     * @var \Richpolis\DreamsBundle\Entity\Dream
     * @todo Comentarios de las dreams
     *
     * @ORM\ManyToOne(targetEntity="Richpolis\DreamsBundle\Entity\Dream",inversedBy="mensajes")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="dream_id", referencedColumnName="id")
     * })
     */
    private $dream;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set status
     *
     * @param boolean $status
     * @return Mensaje
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return boolean 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set deUsuario
     *
     * @param \Richpolis\UsuariosBundle\Entity\Usuario $deUsuario
     * @return Mensaje
     */
    public function setDeUsuario(\Richpolis\UsuariosBundle\Entity\Usuario $deUsuario = null)
    {
        $this->deUsuario = $deUsuario;

        return $this;
    }

    /**
     * Get deUsuario
     *
     * @return \Richpolis\UsuariosBundle\Entity\Usuario 
     */
    public function getDeUsuario()
    {
        return $this->deUsuario;
    }

    /**
     * Set paraUsuario
     *
     * @param \Richpolis\UsuariosBundle\Entity\Usuario $paraUsuario
     * @return Mensaje
     */
    public function setParaUsuario(\Richpolis\UsuariosBundle\Entity\Usuario $paraUsuario = null)
    {
        $this->paraUsuario = $paraUsuario;

        return $this;
    }

    /**
     * Get paraUsuario
     *
     * @return \Richpolis\UsuariosBundle\Entity\Usuario 
     */
    public function getParaUsuario()
    {
        return $this->paraUsuario;
    }

    /**
     * Set dream
     *
     * @param \Richpolis\DreamsBundle\Entity\Dream $dream
     * @return Mensaje
     */
    public function setDream(\Richpolis\DreamsBundle\Entity\Dream $dream = null)
    {
        $this->dream = $dream;

        return $this;
    }

    /**
     * Get dream
     *
     * @return \Richpolis\DreamsBundle\Entity\Dream 
     */
    public function getDream()
    {
        return $this->dream;
    }
}
