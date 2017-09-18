<?php

namespace Keiwen\Cacofony\DependencyInjection;

use Keiwen\Cacofony\EntitiesManagement\EntityRegistry;
use Keiwen\Cacofony\EventListener\AutoDumpListener;
use Keiwen\Cacofony\EventListener\ParamFetcherListener;
use Keiwen\Cacofony\ParamFetcher\ParamFetcher;
use Keiwen\Cacofony\Reader\TemplateAnnotationReader;
use Keiwen\Cacofony\Twig\TwigRequest;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;


class KeiwenCacofonyExtension extends ConfigurableExtension implements PrependExtensionInterface
{

    const API_PARAMETERS_CONF = 'keiwen_cacofony.api_parameters';
    const CONTROLLER_CONF = 'keiwen_cacofony.controller';
    const AUTODUMP_PARAM = 'keiwen_cacofony.autodump.paramname';
    const PARAM_FETCHER_LISTENER_PRIORITY_CONF = 'keiwen_cacofony.param_fetcher_listener.priority';
    const PARAM_FETCHER_CONTROLLER_PARAM_CONF = 'keiwen_cacofony.param_fetcher.controller_parameter';
    const ROLE_PREFIXES_CONF = 'keiwen_cacofony.rolechecker.role_prefixes';
    const EXCEPTION_PREVIOUS_ON_TWIGERROR = 'keiwen_cacofony.exception.previous_on_twigerror';

    const TWIG_FORMTHEME_TEL = 'KeiwenCacofonyBundle:formtheme:tel.html.twig';
    const TWIG_FORMTHEME_DATE = 'KeiwenCacofonyBundle:formtheme:date.html.twig';
    const TWIG_FORMTHEME_RADIOCHECK = 'KeiwenCacofonyBundle:formtheme:radio_checkbox.html.twig';

    public function loadInternal(array $mergedConfig, ContainerBuilder $container)
    {

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );

        $container->setParameter(self::API_PARAMETERS_CONF, $mergedConfig['api_parameters']);
        $container->setParameter(self::CONTROLLER_CONF, $mergedConfig['controller']);
        $container->setParameter(self::AUTODUMP_PARAM, $mergedConfig['autodump']['parameter_name']);
        if(!isset($mergedConfig['rolechecker']['role_prefixes'])) $mergedConfig['rolechecker']['role_prefixes'] = array();
        $container->setParameter(self::ROLE_PREFIXES_CONF, $mergedConfig['rolechecker']['role_prefixes']);
        $container->setParameter(self::PARAM_FETCHER_LISTENER_PRIORITY_CONF, $mergedConfig['param_fetcher']['listener_priority']);
        $container->setParameter(self::PARAM_FETCHER_CONTROLLER_PARAM_CONF, $mergedConfig['param_fetcher']['controller_parameter']);
        $container->setParameter(self::EXCEPTION_PREVIOUS_ON_TWIGERROR, $mergedConfig['exception']['previous_on_twigerror']);

        $loader->load('services.yml');
        $loader->load('services_entityRegistry.yml');
        $loader->load('services_paramFetcher.yml');
        $loader->load('services_security.yml');
        $loader->load('services_request.yml');
        $loader->load('services_form.yml');
        $loader->load('services_twig.yml');
    }


    public function prepend(ContainerBuilder $container)
    {
        $container->prependExtensionConfig('twig', array(
            'form_themes' => array(
                static::TWIG_FORMTHEME_TEL,
                static::TWIG_FORMTHEME_DATE,
                static::TWIG_FORMTHEME_RADIOCHECK,
            )
        ));
    }

}
