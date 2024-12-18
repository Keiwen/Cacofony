<?php

namespace Keiwen\Cacofony\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;


class KeiwenCacofonyExtension extends ConfigurableExtension implements PrependExtensionInterface
{

    const CONTROLLER_CONF = 'keiwen_cacofony.controller';
    const AUTODUMP_PARAM = 'keiwen_cacofony.autodump.paramname';
    const ROLE_PREFIXES_CONF = 'keiwen_cacofony.rolechecker.role_prefixes';
    const TOKEN_SECRET_CONF = 'keiwen_cacofony.token_generator.secret';
    const TOKEN_CIPHER_ALGO_CONF = 'keiwen_cacofony.token_generator.cipher_algo';
    const TOKEN_OPENSSL_IV_CONF = 'keiwen_cacofony.token_generator.openssl_iv';
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

        $container->setParameter(self::CONTROLLER_CONF, $mergedConfig['controller']);
        $container->setParameter(self::AUTODUMP_PARAM, $mergedConfig['autodump']['parameter_name']);
        if(!isset($mergedConfig['rolechecker']['role_prefixes'])) $mergedConfig['rolechecker']['role_prefixes'] = array();
        $container->setParameter(self::ROLE_PREFIXES_CONF, $mergedConfig['rolechecker']['role_prefixes']);
        $container->setParameter(self::TOKEN_SECRET_CONF, $mergedConfig['token_generator']['secret']);
        $container->setParameter(self::TOKEN_CIPHER_ALGO_CONF, $mergedConfig['token_generator']['cipher_algo']);
        $container->setParameter(self::TOKEN_OPENSSL_IV_CONF, $mergedConfig['token_generator']['openssl_iv']);
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
