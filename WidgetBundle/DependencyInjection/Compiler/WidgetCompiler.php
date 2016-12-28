<?php

namespace Keiwen\Cacofony\WidgetBundle\DependencyInjection\Compiler;


use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class WidgetCompiler implements CompilerPassInterface
{

    protected $twigExtensionClassName;
    protected $bundleClassName;

    public function __construct(string $twigExtensionClassName, string $bundleClassName)
    {
        $this->twigExtensionClassName = $twigExtensionClassName;
        $this->bundleClassName = $bundleClassName;
    }

    public function process(ContainerBuilder $container)
    {
        //change services classes for bundle overwrite
        $container->getDefinition('keiwen_cacofony_widget.twig')->setClass($this->twigExtensionClassName);
        $container->setParameter('keiwen_cacofony_widget.bundleclassname', $this->bundleClassName);
    }


}
