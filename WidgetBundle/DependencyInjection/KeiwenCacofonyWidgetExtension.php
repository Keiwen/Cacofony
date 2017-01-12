<?php

namespace Keiwen\Cacofony\WidgetBundle\DependencyInjection;

use Keiwen\Cacofony\WidgetBundle\Controller\WidgetController;
use Keiwen\Cacofony\WidgetBundle\Controller\WidgetDisplayController;
use Keiwen\Cacofony\WidgetBundle\EventListener\AsyncWidgetListener;
use Keiwen\Cacofony\WidgetBundle\Twig\TwigWidget;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;


class KeiwenCacofonyWidgetExtension extends ConfigurableExtension
{


    public function loadInternal(array $mergedConfig, ContainerBuilder $container)
    {

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );

        $loader->load('services.yml');

        $this->addClassesToCompile(array(
            AsyncWidgetListener::class,
            TwigWidget::class,
        ));

    }


}