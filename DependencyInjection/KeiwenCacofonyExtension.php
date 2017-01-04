<?php

namespace Keiwen\Cacofony\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;


class KeiwenCacofonyExtension extends ConfigurableExtension
{

    const API_PARAMETERS_CONF = 'keiwen_cacofony.api_parameters';
    const CONTROLLER_CONF = 'keiwen_cacofony.controller';
    const PARAM_FETCHER_LISTENER_PRIORITY_CONF = 'keiwen_cacofony.param_fetcher_listener.priority';
    const PARAM_FETCHER_CONTROLLER_PARAM_CONF = 'keiwen_cacofony.param_fetcher.controller_parameter';
    const ROLE_PREFIXES_CONF = 'keiwen_cacofony.role_prefixes';

    public function loadInternal(array $mergedConfig, ContainerBuilder $container)
    {

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );

        $container->setParameter(self::API_PARAMETERS_CONF, $mergedConfig['api_parameters']);
        $container->setParameter(self::CONTROLLER_CONF, $mergedConfig['controller']);
        if(!isset($mergedConfig['rolechecker']['role_prefixes'])) $mergedConfig['rolechecker']['role_prefixes'] = array();
        $container->setParameter(self::ROLE_PREFIXES_CONF, $mergedConfig['rolechecker']['role_prefixes']);
        $container->setParameter(self::PARAM_FETCHER_LISTENER_PRIORITY_CONF, $mergedConfig['param_fetcher']['listener_priority']);
        $container->setParameter(self::PARAM_FETCHER_CONTROLLER_PARAM_CONF, $mergedConfig['param_fetcher']['controller_parameter']);

        $loader->load('services_entityRegistry.yml');
        $loader->load('services_paramFetcher.yml');
        $loader->load('services_security.yml');
        $loader->load('services_request.yml');
        $loader->load('services_form.yml');
        $loader->load('services_twig.yml');
    }


}
