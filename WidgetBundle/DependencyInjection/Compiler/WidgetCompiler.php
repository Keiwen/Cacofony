<?php

namespace Keiwen\Cacofony\WidgetBundle\DependencyInjection\Compiler;


use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class WidgetCompiler implements CompilerPassInterface
{

    protected $controllerClassName;
    protected $twigExtensionClassName;
    protected $bundleName;

    public function __construct(string $controllerClassName, string $twigExtensionClassName, string $bundleName)
    {
        $this->controllerClassName = $controllerClassName;
        $this->twigExtensionClassName = $twigExtensionClassName;
        $this->bundleName = $bundleName;
    }

    public function process(ContainerBuilder $container)
    {
        //change services classes for bundle overwrite
        $container->getDefinition('keiwen_cacofony_widget.controller')->setClass($this->controllerClassName);
        $container->getDefinition('keiwen_cacofony_widget.twig')->setClass($this->twigExtensionClassName);
        $container->setParameter('keiwen_cacofony_widget.bundlename', $this->bundleName);
    }


}