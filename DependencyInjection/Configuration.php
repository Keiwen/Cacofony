<?php


namespace Keiwen\Cacofony\DependencyInjection;

use Keiwen\Cacofony\EntitiesManagement\EntityRegistry;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;


class Configuration implements ConfigurationInterface
{


    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('keiwen_cacofony');

        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('controller')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('default_cache')
                            ->defaultValue('cache')
                        ->end()
                        ->scalarNode('default_entity_registry')
                            ->defaultValue(EntityRegistry::class)
                        ->end()
                        ->scalarNode('default_request')
                            ->defaultValue('@Keiwen\Cacofony\Http\Request')->cannotBeEmpty()
                        ->end()
                        ->scalarNode('getparam_disable_cache')
                            ->defaultValue('noCache')
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('autodump')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('parameter_name')
                            ->defaultValue('_templatesParameters')
                            ->info('Name of the dump\'s key containing template parameters')
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('exception')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('previous_on_twigerror')
                            ->defaultValue(true)
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('rolechecker')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('role_prefixes')
                            ->prototype('scalar')->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('token_generator')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('secret')
                            ->defaultValue('Keiwen_Cacofony_Secret_To_Be_Changed')->cannotBeEmpty()
                        ->end()
                        ->scalarNode('cipher_algo')
                            ->defaultValue('aes-128-cbc')
                        ->end()
                        ->scalarNode('openssl_iv')
                            ->defaultValue('CACO_init_osslIV')
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('code_translator')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('locale')
                            ->defaultValue('transCode')
                        ->end()
                        ->scalarNode('display_pattern')
                            ->defaultValue('#{domain}[{message}]')->cannotBeEmpty()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('template_guesser')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('extension')
                            ->defaultValue('html.twig')
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }




}
