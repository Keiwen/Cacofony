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


    public function loadInternal(array $mergedConfig, ContainerBuilder $container)
    {

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );

        $apiParameters = $mergedConfig['api_parameters'];
        $container->setParameter(self::API_PARAMETERS_CONF, $apiParameters);
        $controllerParameters = $mergedConfig['controller'];
        $container->setParameter(self::CONTROLLER_CONF, $controllerParameters);

        $container->setParameter('keiwen_cacofony.param_fetcher_listener.priority', $mergedConfig['param_fetcher']['listener_priority']);
        $container->setParameter('keiwen_cacofony.param_fetcher.controller_parameter', $mergedConfig['param_fetcher']['controller_parameter']);

        $loader->load('services_entityRegistry.yml');
        $loader->load('services_paramFetcher.yml');
        $loader->load('services_security.yml');
        $loader->load('services_request.yml');
        $loader->load('services_form.yml');
        $loader->load('services_twig.yml');
    }


}
