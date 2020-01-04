<?php
 
namespace DesarrolloHosting\YubikeyLoginBundle\Security\RolesProcessor;
 
use DesarrolloHosting\YubikeyLoginBundle\Security\Exception\RolesProcessorException;
 
class SingleRolesProcessor implements RolesProcessorInterface {
 
    /**
     * This RoleProcessor is supposed to be used only when one (1) role is defined in the database,
     * This RoleProcessor removes the variable name from the role.
     * 
     * @param array $roles Array containing the roles
     * @return array Array containing the role without the variable name
     */
    function process(array $roles) {
        if(sizeof($roles) > 1){
            throw new RolesProcessorException("Too many roles received");
        }
        $exploded_role = explode('_', $roles[0]);
        unset($exploded_role[1]);
        
        $new_role = implode('_', $exploded_role);
        
        return array($new_role);
    }
 
}