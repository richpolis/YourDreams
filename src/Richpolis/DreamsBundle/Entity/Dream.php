<?php

namespace Richpolis\DreamsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Dream
 *
 * @ORM\Table(name="dreams")
 * @ORM\Entity(repositoryClass="Richpolis\DreamsBundle\Entity\DreamRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Dream
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
     * @var string
     *
     * @ORM\Column(name="dream", type="string", length=140,nullable=false)
     * @Assert\NotBlank(message="Cual es tu sueño?")
     */
    private $titulo;
    
    /**
     * @var string
     *
     * @ORM\Column(name="dream", type="text",nullable=false)
     */
    private $dream;
    
    /**
     * @var string
     *
     * @ORM\Column(name="lugar", type="string", length=140,nullable=false)
     */
    private $lugar;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="is_compartir", type="boolean")
     */
    private $compartir;
    
    
    /**
     * @var \Richpolis\UsuariosBundle\Entity\Usuario
     *
     * @ORM\ManyToOne(targetEntity="Richpolis\UsuariosBundle\Entity\Usuario", inversedBy="dreams")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="usuario_id", referencedColumnName="id")
     * })
     */
    private $usuario;
    
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=false)
     */
    private $updatedAt;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->componentes = new \Doctrine\Common\Collections\ArrayCollection();
        $this->compartir = true;
    }
    
    public function __toString() {
        return $this->titulo;
    }
    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    
    
    /*
     * Timestable
     */
    
    /**
     ** @ORM\PrePersist
     */
    public function setCreatedAtValue()
    {
        if(!$this->getCreatedAt())
        {
          $this->createdAt = new \DateTime();
        }
        if(!$this->getUpdatedAt())
        {
          $this->updatedAt = new \DateTime();
        }
    }
    
    /**
     * @ORM\PreUpdate
     */
    public function setUpdatedAtValue()
    {
        $this->updatedAt = new \DateTime();
    }
    
}
