<?php
 
namespace DesarrolloHosting\YubikeyLoginBundle\Security\RolesProcessor;
 
class RolesProcessorFactory {
 
    public static function createRolesProcessor($roles_processor_name, $params) {
        if (!$roles_processor_name) {
            return new DummyRolesProcessor();
        }
 
        if (class_exists($roles_processor_name)) {
            return new $roles_processor_name();
        }
 
        $camel_case_roles_processor_name = implode('', array_map('ucfirst', explode('_', $roles_processor_name)));
        $class_name = _NAMESPACE_ . '\\' . $camel_case_roles_processor_name . 'RolesProcessor';
        
        if($params){
            return new $class_name($params);
        }
        return new $class_name();
 
        
    }
 
}