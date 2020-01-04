<?php
 
namespace DesarrolloHosting\YubikeyLoginBundle\DependencyInjection;
 
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
 
/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('yubikey_login');
        
        $rootNode
            ->children()
                ->scalarNode('user_provider')->isRequired()->end()
                ->scalarNode('system_name')->isRequired()->end()
                ->scalarNode('background_color')->defaultNull()->end()
                ->arrayNode('roles_processor')->isRequired()
                    ->children()
                        ->scalarNode('name')->defaultValue('dummy')->end()
                        ->arrayNode('params')->defaultValue(array())
                            ->prototype('scalar')->end()
                        ->end()
                    ->end()
                ->end() //roles_processor
                ->scalarNode('base_role')->defaultValue('ROLE_USER')->cannotBeEmpty()->end()         
                ->arrayNode('yubikey')->isRequired()
                    ->children()
                        ->scalarNode('api_key')->isRequired()->end()
                        ->integerNode('client_id')->isRequired()->end()
                    ->end()
                ->end() // yubikey
                ->arrayNode('redirect_login')
                    ->addDefaultsIfNotSet()
                    ->info('Where should an authenticated user should be redirected if he visits the login page.')
                    ->children()
                        ->enumNode('type')
                            ->values(array('url', 'route'))
                            ->defaultValue('url')
                        ->end()
                        ->scalarNode('destination')
                            ->defaultNull()
                            ->cannotBeEmpty()
                        ->end()
                    ->end()
                ->end() // redirect_login
                ->arrayNode('assets')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('css')
                            ->defaultValue('bundles/desarrollohostingyubikeylogin/css/style.css')
                            ->cannotBeEmpty()
                        ->end()
                        ->scalarNode('js')
                            ->defaultValue('bundles/desarrollohostingyubikeylogin/js/action.js')
                            ->cannotBeEmpty()
                        ->end()
                        ->scalarNode('logo')
                            ->defaultValue('bundles/desarrollohostingyubikeylogin/images/logo.png')
                            ->cannotBeEmpty()
                        ->end()
                    ->end()
                ->end() // assets
            ->end()
        ;
 
        return $treeBuilder;
    }
}