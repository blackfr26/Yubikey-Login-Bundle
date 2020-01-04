<?php
 
namespace DesarrolloHosting\YubikeyLoginBundle;
 
use Symfony\Component\HttpKernel\Bundle\Bundle;
use DesarrolloHosting\YubikeyLoginBundle\DependencyInjection\YubikeyLoginExtension;
 
class DesarrolloHostingYubikeyLoginBundle extends Bundle {
 
    public function getContainerExtension() {
        return new YubikeyLoginExtension();
    }
 
}