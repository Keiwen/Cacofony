<?php

namespace Keiwen\Cacofony\DependencyInjection\Compiler;

use Keiwen\Cacofony\DependencyInjection\KeiwenCacofonyExtension;
use Keiwen\Cacofony\Translator\CodeTranslator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Parameter;


class TranslatorPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('translator.default')) {
            return;
        }

        $definition = $container->getDefinition('translator.default');
        $definition->setClass(CodeTranslator::class);
        $definition->addMethodCall('setTranslationParameters', array(
            new Parameter(KeiwenCacofonyExtension::TRANSLATOR_CODE_LOCALE),
            new Parameter(KeiwenCacofonyExtension::TRANSLATOR_CODE_PATTERN),
        ));
    }
}
