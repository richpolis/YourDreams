<?php

namespace Richpolis\ComentariosBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Comentario
 *
 * @ORM\Table(name="comentarios")
 * @ORM\Entity(repositoryClass="Richpolis\ComentariosBundle\Repository\ComentarioRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Comentario
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
     * @var \Richpolis\UsuariosBundle\Entity\Usuario
     *
     * @ORM\ManyToOne(targetEntity="Richpolis\UsuariosBundle\Entity\Usuario")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="usuario_id", referencedColumnName="id")
     * })
     */
    private $usuario;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="text")
     * @Assert\NotBlank()
     * @Assert\Length(
     *     min = 6,
     *     max = 300
     * )
     */
    private $message;
    
    /**
     * @var boolean
     * @todo Si la noticia es inactiva no es visible para el usuario. 
     *
     * @ORM\Column(name="status", type="integer")
     */
    private $status;

    /**
     * @var \Richpolis\DreamsBundle\Entity\Dream
     * @todo Comentarios de las dreams
     *
     * @ORM\ManyToOne(targetEntity="Richpolis\DreamsBundle\Entity\Dream")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="dream_id", referencedColumnName="id")
     * })
     */
    private $dream;

    /**
     * @var integer
     *
     * @ORM\Column(name="nivel", type="integer")
     */
    private $nivel;

    /**
     * @ORM\ManyToOne(targetEntity="Comentario", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     */
    private $parent;
    
    /**
     * @ORM\OneToMany(targetEntity="Comentario", mappedBy="parent")
     * @ORM\OrderBy({"id" = "ASC"})
     */
    private $children;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    private $createdAt;

    const STATUS_NUEVO = 0;
    const STATUS_ENTREGADO = 1;
    const STATUS_RECHAZADO = 2;
        
    static public $sStatus=array(
        self::STATUS_NUEVO=>'Nuevo mensaje',
        self::STATUS_ENTREGADO=>'Mensaje entregado',
        self::STATUS_RECHAZADO=>'Mensaje rechazado'
    );
    
    public function __construct() {
        $this->status = Comentario::STATUS_NUEVO;
        $this->dream = new \Doctrine\Common\Collections\ArrayCollection();
	$this->nivel = 0;		
    }
    
    public function getStringStatus(){
        return self::$sStatus[$this->getStatus()];
    }

    static function getArrayStatus(){
        return self::$sStatus;
    }

    static function getPreferedStatus(){
        return array(self::STATUS_NUEVO);
    }
    
    /*
     * Timestable
     */
    
    /**
     * @ORM\PrePersist
     */
    public function setCreatedAtValue()
    {
        if(!$this->getCreatedAt())
        {
          $this->createdAt = new \DateTime();
        }
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

    /**
     * Set message
     *
     * @param string $message
     * @return Comentario
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string 
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return Comentario
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set nivel
     *
     * @param integer $nivel
     * @return Comentario
     */
    public function setNivel($nivel)
    {
        $this->nivel = $nivel;

        return $this;
    }

    /**
     * Get nivel
     *
     * @return integer 
     */
    public function getNivel()
    {
        return $this->nivel;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Comentario
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set parent
     *
     * @param \Richpolis\ComentariosBundle\Entity\Comentario $parent
     * @return Comentario
     */
    public function setParent(\Richpolis\ComentariosBundle\Entity\Comentario $parent = null)
    {
        $this->parent = $parent;
        
        if(($parent instanceof Comentario) && (null!==$parent)){
            $this->setNivel($parent->getNivel()+1);
        }    

        return $this;
    }

    /**
     * Get parent
     *
     * @return \Richpolis\ComentariosBundle\Entity\Comentario 
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Add children
     *
     * @param \Richpolis\ComentariosBundle\Entity\Comentario $children
     * @return Comentario
     */
    public function addChild(\Richpolis\ComentariosBundle\Entity\Comentario $children)
    {
        $this->children[] = $children;

        return $this;
    }

    /**
     * Remove children
     *
     * @param \Richpolis\ComentariosBundle\Entity\Comentario $children
     */
    public function removeChild(\Richpolis\ComentariosBundle\Entity\Comentario $children)
    {
        $this->children->removeElement($children);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Set usuario
     *
     * @param \Richpolis\UsuariosBundle\Entity\Usuario $usuario
     * @return Comentario
     */
    public function setUsuario(\Richpolis\UsuariosBundle\Entity\Usuario $usuario = null)
    {
        $this->usuario = $usuario;

        return $this;
    }

    /**
     * Get usuario
     *
     * @return \Richpolis\UsuariosBundle\Entity\Usuario 
     */
    public function getUsuario()
    {
        return $this->usuario;
    }

    /**
     * Set dream
     *
     * @param \Richpolis\DreamsBundle\Entity\Dream $dream
     * @return Comentario
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
