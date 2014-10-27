<?php
namespace Richpolis\UsuariosBundle\Security\Core\User;
 
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\EntityUserProvider as BaseClass;
use Symfony\Component\Security\Core\User\UserInterface;
use Richpolis\UsuariosBundle\Entity\Usuario;
 
class UserProvider extends BaseClass
{
 
    /**
     * {@inheritDoc}
     */
    public function connect(UserInterface $user, UserResponseInterface $response)
    {
        $property = $this->getProperty($response);
        $username = $response->getUsername();
 
        //on connect - get the access token and the user ID
        $service = $response->getResourceOwner()->getName();
 
        $setter = 'set'.ucfirst($service);
        $setter_id = $setter.'Id';
        $setter_token = $setter.'AccessToken';
 
        //we "disconnect" previously connected users
        
        if (null !== $previousUser = $this->repository->findOneBy(array($property => $username))) {
            $previousUser->$setter_id(null);
            $previousUser->$setter_token(null);
            $this->em->flush();
        }
		
		if($service == "facebook"){
			$fbid = $username;
			$user_fb = "https://graph.facebook.com/" .$fbid;
			$picture = $user_fb."/picture?width=260&height=260";
			$user->setImagen($picture);
		}
 
        //we connect current user
        $user->$setter_id($username);
        $user->$setter_token($response->getAccessToken());
 
        $this->em->flush();
    }
 
    /**
     * {@inheritdoc}
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        $username = $response->getUsername();
        $email = $response->getEmail();
        $imagen = $response->getProfilePicture();
        $nombre = $response->getRealName();
		//var_dump(compact('username','email','imagen','nombre')); die;
        if(null == $email){
			$user = $this->repository->findOneBy(array($this->getProperty($response) => $username));
        }else{
            $user = $this->repository->findOneBy(array('email' => $email));
        }
		
        //when the user is registrating
        if (null === $user) {
            $service = $response->getResourceOwner()->getName();
            $setter = 'set'.ucfirst($service);
            $setter_id = $setter.'Id';
            $setter_token = $setter.'AccessToken';
            // create new user here
            $user = new Usuario();
            $user->$setter_id($username);
            $user->$setter_token($response->getAccessToken());
            //I have set all requested data with the user's username
            //modify here with relevant data
            if(null != $email){
            	$user->setEmail($email);
			}else{
				$user->setEmail($username);
			}
            $user->setPassword($username);
            $user->setNombre($nombre);
            $user->setApellido("");
            $user->setImagen($imagen);
            $user->setIsActive(true);
            $user->setGrupo(Usuario::GRUPO_USUARIOS);
            $this->em->persist($user);
            $this->em->flush();
            return $user;
        }
 
        //if user exists - go with the HWIOAuth way
        //$user = parent::loadUserByOAuthUserResponse($response);
 
        $serviceName = $response->getResourceOwner()->getName();
        $setter = 'set' . ucfirst($serviceName) . 'AccessToken';

		if($serviceName == "facebook"){
				$fbid = $username;
				$user_fb = "https://graph.facebook.com/" .$fbid;
				$picture = $user_fb."/picture?width=260&height=260";
				$user->setImagen($picture);
		}
		
        //update access token
        $user->$setter($response->getAccessToken());
 
        return $user;
    }
	
   /**
     * Gets the property for the response.
     *
     * @param UserResponseInterface $response
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    protected function getProperty(UserResponseInterface $response)
    {
        $resourceOwnerName = $response->getResourceOwner()->getName();

        if (!isset($this->properties[$resourceOwnerName])) {
            throw new \RuntimeException(sprintf("No property defined for entity for resource owner '%s'.", $resourceOwnerName));
        }

        return $this->properties[$resourceOwnerName];
    }

 
}

