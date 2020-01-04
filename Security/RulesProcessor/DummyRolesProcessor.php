<?php
 
namespace DesarrolloHosting\YubikeyLoginBundle\Security\RolesProcessor;
 
class DummyRolesProcessor implements RolesProcessorInterface {
    
    /**
     * This RoleProcesser keeps the roles unchanged
     * 
     * @param array $roles Array containing the roles
     * @return array Array containing the unchanged roles
     */
    function process(array $roles){
        return $roles;
    }
}