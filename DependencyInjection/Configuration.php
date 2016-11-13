<?php


namespace Keiwen\Cacofony\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;


class Configuration implements ConfigurationInterface
{


    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('keiwen_cacofony');

        $rootNode
            ->children()
                ->arrayNode('param_fetcher')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->integerNode('listener_priority')->defaultValue(4)->min(0)->end()
                        ->scalarNode('controller_parameter')->defaultValue('paramFetcher')->cannotBeEmpty()->end()
                    ->end()
                ->end()
                ->arrayNode('controller')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('default_cache_service_id')->defaultValue('cache')->end()
                        ->scalarNode('default_entity_registry_service_id')->defaultValue('keiwen_cacofony.entity_registry')->end()
                        ->scalarNode('default_log_service_id')->defaultValue('monolog.logger')->end()
                        ->scalarNode('default_request_service_id')->defaultValue('keiwen_cacofony.request')->cannotBeEmpty()->end()
                        ->scalarNode('log_channel')->defaultValue('KeiwenCacofony')->end()
                        ->scalarNode('getparam_disable_cache')->defaultValue('noCache')->end()
                        ->booleanNode('autodump')->defaultValue(true)->end()
                        ->scalarNode('autodump_parameter')->defaultValue('_templateParameters')->cannotBeEmpty()->end()
                        ->booleanNode('api_format_response')->defaultValue(true)->end()
                    ->end()
                ->end()
                ->arrayNode('api_parameters')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('ref_url')->defaultValue('ref_url')->cannotBeEmpty()->end()
                        ->scalarNode('parameters')->defaultValue('parameters')->cannotBeEmpty()->end()
                        ->scalarNode('result')->defaultValue('result')->cannotBeEmpty()->end()
                        ->scalarNode('http_code')->defaultValue('http_code')->cannotBeEmpty()->end()
                        ->scalarNode('message')->defaultValue('message')->cannotBeEmpty()->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }




}
