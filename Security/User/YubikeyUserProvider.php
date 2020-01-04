<?php
 
namespace DesarrolloHosting\YubikeyLoginBundle\Security\User;
 
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use DesarrolloHosting\YubikeyLoginBundle\Libraries\ModHex\ModHex;
use DesarrolloHosting\YubikeyLoginBundle\Security\RolesProcessor\RolesProcessorInterface;
use DesarrolloHosting\YubikeyLoginBundle\Security\Exception\SystemNotFoundException;
 
class YubikeyUserProvider implements UserProviderInterface {
 
    private $system_name;
    private $roles_processor;
    private $base_role;
    
    public function __construct($system_name, RolesProcessorInterface $roles_processor, $base_role) {
        $this->system_name = $system_name;
        $this->roles_processor = $roles_processor;
        $this->base_role = $base_role;
    }
 
    /**
     * Gets the user information from the database.
     * 
     * @param DesarrolloHosting\YubikeyLoginBundle\Controller\ConnectionController $connection
     * @param int $yubikey_id
     * @param string $system_name
     * 
     * @return array Format: array('username' => 'User Name', 'roles' => array(array('name' => 'role1_name', 'value' => 'role1_value'), array('name' => 'role2_name', 'value' => 'role2_value')));
     * 
     * @throws UsernameNotFoundException
     * @throws AccessDeniedException
     */
    function getUserInfo($yubikey_id, $system_name) {
       
        $url = str_replace(' ', '%20', "http://administracion.hosting.cl/api/permissions/yubikey_login/$system_name/$yubikey_id");
        
        $user_info = json_decode(file_get_contents($url), true);
        
        if($user_info["success"] === false){
            $error = $user_info["error"];
            switch ($error) {
                case 'invalid_parameters':
                    throw new \Exception($user_info["message"]);
                case 'system_not_found':
                    throw new SystemNotFoundException($user_info["system"]);
                case 'employee_not_found':
                    throw new UsernameNotFoundException($user_info["yubikey"]);
                case 'no_access':
                    throw new AccessDeniedException($user_info["employee"]);
                case 'exception':
                    throw new \Exception($user_info["message"]);
                default:
                    break;
            }
            
        }
        
        return array('username' => $user_info["username"], 'roles' => $user_info["variables"]);
    }
 
    public function loadUserByUsername($username) {
        return $this->loadUserByOTP($username);
    }
 
    public function loadUserByOTP($otp) {
        $srctext = ((strlen($otp) % 2) == 1) ? $srctext = 'c' . $otp : $otp;
        $yubikey_id = trim(hexdec(ModHex::b64ToHex(ModHex::modhexToB64(substr($srctext, 2, 10))))); // No se consideran los 2 primeros caracteres por si se ha modificado la Yubikey
 
        return $this->loadUserByYubikeyId($yubikey_id);
    }
 
    public function loadUserByYubikeyId($yubikey_id) {
        $user_info = $this->getUserInfo($yubikey_id, $this->system_name);
           
        // The authenticated user will always have the ROLE_USER, even if the system doesn't have any other configurable options
        $unprocessed_roles = array();
        $roles = array();
        foreach ($user_info['roles'] as $role) {
            $nombre = strtoupper(str_replace(' ', '', $role['name']));
            $valor = strtoupper(str_replace(' ', '_', $role['value']));
            $unprocessed_roles[] = 'ROLE_' . $nombre . '_' . $valor;
        }
        if (sizeof($unprocessed_roles) > 0) {
            $roles = $this->roles_processor->process($unprocessed_roles);
        }
        
        if ($this->base_role && !in_array($this->base_role, $roles)) {
            $roles[] = $this->base_role;
        }
 
        return new YubikeyUser($yubikey_id, $user_info['username'], $roles);
    }
 
    public function refreshUser(UserInterface $user) {
        if (!$user instanceof YubikeyUser) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }
 
        return $this->loadUserByYubikeyId($user->getYubikeyId());
    }
 
    public function supportsClass($class) {
        return $class === 'DesarrolloHosting\YubikeyLoginBundle\Security\User\YubikeyUser';
    }
 
}