<?php


namespace Keiwen\Cacofony\DependencyInjection;

use Keiwen\Cacofony\EntitiesManagement\EntityRegistry;
use Keiwen\Cacofony\ParamFetcher\ParamFetcher;
use Keiwen\Cacofony\ParamFetcher\ParamReader;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;


class Configuration implements ConfigurationInterface
{


    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('keiwen_cacofony');

        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('param_fetcher')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('controller_parameter')
                            ->defaultValue('paramFetcher')->cannotBeEmpty()
                        ->end()
                        ->scalarNode('fetcher_class')
                            ->defaultValue(ParamFetcher::class)->cannotBeEmpty()
                        ->end()
                        ->scalarNode('reader_class')
                            ->defaultValue(ParamReader::class)->cannotBeEmpty()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('controller')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('default_cache')
                            ->defaultValue('cache')
                        ->end()
                        ->scalarNode('default_entity_registry')
                            ->defaultValue(EntityRegistry::class)
                        ->end()
                        ->scalarNode('default_log')
                            ->defaultValue('monolog.logger')
                        ->end()
                        ->scalarNode('default_request')
                            ->defaultValue('@Keiwen\Cacofony\Http\Request')->cannotBeEmpty()
                        ->end()
                        ->scalarNode('log_channel')
                            ->defaultValue('KeiwenCacofony')
                        ->end()
                        ->scalarNode('getparam_disable_cache')
                            ->defaultValue('noCache')
                        ->end()
                        ->booleanNode('api_format_response')
                            ->defaultValue(true)
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
                ->arrayNode('api_parameters')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('ref_url')
                            ->defaultValue('ref_url')->cannotBeEmpty()
                        ->end()
                        ->scalarNode('parameters')
                            ->defaultValue('parameters')->cannotBeEmpty()
                        ->end()
                        ->scalarNode('result')
                            ->defaultValue('result')->cannotBeEmpty()
                        ->end()
                        ->scalarNode('http_code')
                            ->defaultValue('http_code')->cannotBeEmpty()
                        ->end()
                        ->scalarNode('message')
                            ->defaultValue('message')->cannotBeEmpty()
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
