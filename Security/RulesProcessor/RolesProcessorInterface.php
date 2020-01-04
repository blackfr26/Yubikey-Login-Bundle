<?php
 
namespace DesarrolloHosting\YubikeyLoginBundle\Security\RolesProcessor;
 
interface RolesProcessorInterface {
    
    function process(array $roles);
}