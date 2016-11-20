<?php

namespace Keiwen\Cacofony\WidgetBundle;

use Keiwen\Cacofony\WidgetBundle\DependencyInjection\Compiler\WidgetCompiler;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class KeiwenCacofonyWidgetBundle extends Bundle
{

    const CONTROLLER_CLASSNAME = 'Controller\\WidgetController';
    const TWIGEXT_CLASSNAME = 'Twig\\TwigWidget';

    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(
            new WidgetCompiler(static::getControllerClassName(), static::getTwigExtensionClassName(), static::getBundleName())
        );
    }


    /**
     * @return string
     */
    protected static function getControllerClassName()
    {
        $static = static::getBundleNamespace() . '\\' . static::CONTROLLER_CLASSNAME;
        if(class_exists($static)) return $static;
        return static::getDefaultBundleNamespace() . '\\' . self::CONTROLLER_CLASSNAME;
    }

    /**
     * @return string
     */
    protected static function getTwigExtensionClassName()
    {
        $static = static::getBundleNamespace() . '\\' . static::TWIGEXT_CLASSNAME;
        if(class_exists($static)) return $static;
        return static::getDefaultBundleNamespace() . '\\' . self::TWIGEXT_CLASSNAME;
    }


    /**
     * get current bundle namespace (work with inherited bundle)
     * @return string
     */
    protected static function getBundleNamespace()
    {
        $class = explode('\\', static::class);
        array_pop($class);
        return implode('\\', $class);
    }

    /**
     * get parent bundle namespace
     * @return string
     */
    protected static function getDefaultBundleNamespace()
    {
        $class = explode('\\', self::class);
        array_pop($class);
        return implode('\\', $class);
    }

    /**
     * get current bundle classname without namespace (work with inherited bundle)
     * @return string
     */
    protected static function getBundleName()
    {
        $class = explode('\\', static::class);
        return array_pop($class);
    }

}
