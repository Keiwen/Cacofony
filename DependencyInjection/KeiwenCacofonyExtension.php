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
    const PARAM_FETCHER_CONTROLLER_PARAM_CONF = 'keiwen_cacofony.param_fetcher.controller_parameter';
    const PARAM_FETCHER_FETCHER_CLASS = 'keiwen_cacofony.param_fetcher.fetcher_class';
    const PARAM_FETCHER_READER_CLASS = 'keiwen_cacofony.param_fetcher.reader_class';
    const ROLE_PREFIXES_CONF = 'keiwen_cacofony.rolechecker.role_prefixes';
    const EXCEPTION_PREVIOUS_ON_TWIGERROR = 'keiwen_cacofony.exception.previous_on_twigerror';
    const TRANSLATOR_CODE_LOCALE = 'keiwen_cacofony.code_translator.locale';
    const TRANSLATOR_CODE_PATTERN = 'keiwen_cacofony.code_translator.pattern';
    const TEMPLATE_GUESSER_EXTENSION = 'keiwen_cacofony.template_guesser.extension';

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
        $container->setParameter(self::PARAM_FETCHER_CONTROLLER_PARAM_CONF, $mergedConfig['param_fetcher']['controller_parameter']);
        $container->setParameter(self::PARAM_FETCHER_FETCHER_CLASS, $mergedConfig['param_fetcher']['fetcher_class']);
        $container->setParameter(self::PARAM_FETCHER_READER_CLASS, $mergedConfig['param_fetcher']['reader_class']);
        $container->setParameter(self::EXCEPTION_PREVIOUS_ON_TWIGERROR, $mergedConfig['exception']['previous_on_twigerror']);
        $container->setParameter(self::TRANSLATOR_CODE_LOCALE, $mergedConfig['code_translator']['locale']);
        $container->setParameter(self::TRANSLATOR_CODE_PATTERN, $mergedConfig['code_translator']['display_pattern']);
        $container->setParameter(self::TEMPLATE_GUESSER_EXTENSION, $mergedConfig['template_guesser']['extension']);

        $loader->load('services.yml');
    }


    public function prepend(ContainerBuilder $container)
    {
    }

}
