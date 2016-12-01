<?php

namespace Keiwen\Cacofony\WidgetBundle;

use Keiwen\Cacofony\WidgetBundle\Controller\WidgetController;
use Keiwen\Cacofony\WidgetBundle\DependencyInjection\Compiler\WidgetCompiler;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class KeiwenCacofonyWidgetBundle extends Bundle
{

    const CONTROLLER_PATH = 'Controller';
    const CONTROLLER_DEFAULT_CLASSNAME = 'WidgetController';
    const TWIGEXT_CLASSNAME = 'Twig\\TwigWidget';

    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(
            new WidgetCompiler(static::getTwigExtensionClassName(), static::class)
        );
    }


    /**
     * @param string $name widget controller name
     * @return string
     */
    public static function getControllerClassName(string $name = '')
    {
        if(empty($name)) $name = static::CONTROLLER_DEFAULT_CLASSNAME;
        //be sure to end controller name by Controller
        if(!preg_match('#(.*)Controller$#', $name)) $name .= 'Controller';

        //try static (child bundle)
        $static = static::getBundleNamespace();
        if(!empty(static::CONTROLLER_PATH)) $static .= '\\' . static::CONTROLLER_PATH;
        $static .= '\\' . ucfirst($name);
        if(class_exists($static)) {
            //check if subclass
            if(is_subclass_of($static, WidgetController::class)) {
                return $static;
            }
            throw new \RuntimeException(sprintf('Controller "%s" used as widget controller must extends "%s"', $static, WidgetController::class));
        }

        //try self (parent bundle)
        $self = self::getDefaultBundleNamespace();
        if(!empty(self::CONTROLLER_PATH)) $self .= '\\' . self::CONTROLLER_PATH;
        $self .= '\\' . ucfirst($name);
        if(class_exists($self)) {
            //check if subclass
            if(is_subclass_of($self, WidgetController::class)) {
                return $self;
            }
            throw new \RuntimeException(sprintf('Controller "%s" used as widget controller must extends "%s"', $self, WidgetController::class));
        }

        throw new \RuntimeException(sprintf('Controller "%s" not found (looked for [%s] and [%s])', $name, $static, $self));
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
