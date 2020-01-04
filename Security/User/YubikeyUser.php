<?php
 
namespace DesarrolloHosting\YubikeyLoginBundle\Security\User;
 
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Exception;
 
class YubikeyUser implements UserInterface, EquatableInterface {
    
    private $yubikey_id;
    private $full_name;
    private $roles;
 
    public function __construct($yubikey_id, $full_name, array $roles) {
        $this->yubikey_id = $yubikey_id;
        $this->full_name = $full_name;
        $this->roles = $roles;        
    }
 
    public function getRoles() {
        return $this->roles;
    }
 
    public function getPassword() {throw new Exception("YubikeyLogin does not use passwords");}
 
    public function getSalt() {throw new Exception("YubikeyLogin does not use salt");}
 
    public function getUsername() {
        return $this->getFullName();
    }
 
    public function getYubikeyId(){
        return $this->yubikey_id;
    }
    
    public function getFullName(){
        return $this->full_name;
    }
    
    public function eraseCredentials() {
        
    }
 
    public function isEqualTo(UserInterface $user) {
        if (!$user instanceof YubikeyUser) {
            return false;
        }
 
        if ($this->yubikey_id !== $user->getYubikeyId()) {
            return false;
        }
 
        if ($this->full_name !== $user->getFullName()) {
            return false;
        }
 
        return true;
    }
 
}