<?php

namespace Richpolis\DreamsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Richpolis\BackendBundle\Utils\Richsys as RpsStms;
/**
 * Componente
 *
 * @ORM\Table(name="componentes")
 * @ORM\Entity(repositoryClass="Richpolis\DreamsBundle\Entity\ComponenteRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Componente
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
     * @ORM\Column(name="tipo", type="integer")
     */
    private $tipo;

    /**
     * @var string
     *
     * @ORM\Column(name="componente", type="text", nullable=true)
     */
    private $componente;

    /**
     * @var \Richpolis\DreamsBundle\Entity\Dream
     *
     * @ORM\ManyToOne(targetEntity="Richpolis\DreamsBundle\Entity\Dream",inversedBy="componentes")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="dream_id", referencedColumnName="id")
     * })
     */
    private $dream;

    /**
     * @var integer
     *
     * @ORM\Column(name="position", type="integer")
     */
    private $position;

	
    const TIPO_IMAGEN=1;
    const TIPO_VIDEO=2;
    const TIPO_LINK=3;
    const TIPO_MUSICA=4;
    const TIPO_FLASH=5;
        
    static public $sTipo=array(
        self::TIPO_IMAGEN=>'Imagen',
        self::TIPO_VIDEO=>'Video',
        self::TIPO_LINK=>'Link',
        self::TIPO_MUSICA=>'Musica',
        self::TIPO_FLASH=>'Flash'
    );
    
    public function getStringTipo(){
        return self::$sTipo[$this->getTipo()];
    }

    static function getArrayTipo(){
        return self::$sTipo;
    }

    static function getPreferedTipo(){
        return array(self::TIPO_IMAGEN);
    }
	
    public function __construct(){
	$this->tipo = self::TIPO_IMAGEN;
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
     * Set tipo
     *
     * @param integer $tipo
     * @return Componente
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;

        return $this;
    }

    /**
     * Get tipo
     *
     * @return integer 
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * Set componente
     *
     * @param string $componente
     * @return Componente
     */
    public function setComponente($componente)
    {
        $this->componente = $componente;

        return $this;
    }

    /**
     * Get componente
     *
     * @return string 
     */
    public function getComponente()
    {
        return $this->componente;
    }

    /*** uploads ***/
    
    /**
     * @Assert\File(maxSize="6000000")
     */
    private $file;

    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
        // check if we have an old image path
        if (isset($this->componente)) {
            // store the old name to delete after the update
            $this->temp = $this->componente;
            $this->componente = null;
        } else {
            $this->componente = 'initial';
        }
        $directorio=$this->getUploadRootDir();
        if(!file_exists($directorio)){
          mkdir($directorio, 0777);  
        }
    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }
    
    /**
    * @ORM\PrePersist
    * @ORM\PreUpdate
    */
    public function preUpload()
    {
      if (null !== $this->getFile()) {
            // do whatever you want to generate a unique name
            $filename = sha1(uniqid(mt_rand(), true));
            $this->componente = $filename.'.'.$this->getFile()->guessExtension();
        }
    }

    /**
    * @ORM\PostPersist
    * @ORM\PostUpdate
    */
    public function upload()
    {
      if (null === $this->getFile()) {
            return;
        }

        // if there is an error when moving the file, an exception will
        // be automatically thrown by move(). This will properly prevent
        // the entity from being persisted to the database on error
        $this->getFile()->move($this->getUploadRootDir(), $this->componente);

        // check if we have an old image
        if (isset($this->temp)) {
            // delete the old image
            unlink($this->getUploadRootDir().'/'.$this->temp);
            // clear the temp image path
            $this->temp = null;
        }
        $this->file = null;
    }

    /**
    * @ORM\PostRemove
    */
    public function removeUpload()
    {
      if ($file = $this->getAbsolutePath()) {
        if(file_exists($file)){
            unlink($file);
        }
      }
    }
    
    protected function getUploadDir()
    {
        return '/uploads/dreams';
    }

    protected function getUploadRootDir()
    {
        return __DIR__.'/../../../../web'.$this->getUploadDir();
    }
    
    
    /**
     * Rutas de archivos 
     */
    public function getWebPath()
    {
        if($this->getTipo()==self::TIPO_LINK){
            return RpsStms::getVideoIdYoutube($this->componente);
        }else{
            return null === $this->componente ? null : $this->getUploadDir().'/'.$this->componente;
        }
    }
    
    public function getAbsolutePath()
    {
        return null === $this->componente ? null : $this->getUploadRootDir().'/'.$this->componente;
    }
    
    public function getTemplate(){
        switch($this->getTipo()){
            case self::TIPO_DIALOGO:
                if($this->getTipoUsuario() == self::TIPO_USUARIO_PAPA){
                    return 'FrontendBundle:Default:dialogoPapa.html.twig';
                }else{
                    return 'FrontendBundle:Default:dialogoNino.html.twig';
                }
            case self::TIPO_IMAGEN:
                return 'FrontendBundle:Default:imagenNino.html.twig';
            case self::TIPO_LINK:
                return 'FrontendBundle:Default:videoNino.html.twig';
            case self::TIPO_MUSICA:
                return 'FrontendBundle:Default:sonidoNino.html.twig';
            default:
                return 'FrontendBundle:Default:dialogoPapa.html.twig';
        } 
    }
}
