<?php
 
namespace DesarrolloHosting\YubikeyLoginBundle\DependencyInjection;
 
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
 
/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class YubikeyLoginExtension extends Extension {
 
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container) {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        
        $container->setParameter('yubikey_login.user_provider', $config['user_provider']);
        $container->setParameter('yubikey_login.system_name', $config['system_name']);
        $container->setParameter('yubikey_login.background_color', $config['background_color']);
        $container->setParameter('yubikey_login.base_role', $config['base_role']);
        $this->loadYubikeyConfig($config['yubikey'], $container);
        $this->loadRedirectLoginConfig($config['redirect_login'], $container);
        $this->loadRoleProcessorConfig($config['roles_processor'], $container);
        $this->loadAssetsConfig($config['assets'], $container);
        
        $loader = new Loader\YamlFileLoader($container, new FileLocator(_DIR_ . '/../Resources/config'));
        $loader->load('services.yml');
        $loader->load('commands.yml');
        
        $this->addClassesToCompile(array('DesarrolloHosting\\YubikeyLoginBundle\\Libraries\\ModHex\\ModHex'));
    }
 
    public function getAlias() {
        return 'yubikey_login';
    }
    
    private function loadYubikeyConfig(array $config, ContainerBuilder $container){
        $container->setParameter('yubikey_login.yubikey.api_key', $config['api_key']);
        $container->setParameter('yubikey_login.yubikey.client_id', $config['client_id']);
    }
    
    private function loadRedirectLoginConfig(array $config, ContainerBuilder $container){
        $container->setParameter('yubikey_login.redirect_login.type', $config['type']);
        $container->setParameter('yubikey_login.redirect_login.destination', $config['destination']);
    }
    
    private function loadRoleProcessorConfig(array $config, ContainerBuilder $container){
        $container->setParameter('yubikey_login.roles_processor.name', $config['name']);
        $container->setParameter('yubikey_login.roles_processor.params', $config['params']);
    }
    
    private function loadAssetsConfig(array $config, ContainerBuilder $container){
        $container->setParameter('yubikey_login.assets.css', $config['css']);
        $container->setParameter('yubikey_login.assets.js', $config['js']);
        $container->setParameter('yubikey_login.assets.logo', $config['logo']);
    }
 
}
