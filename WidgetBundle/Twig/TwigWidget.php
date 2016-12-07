<?php

namespace Keiwen\Cacofony\WidgetBundle\Twig;


use Keiwen\Cacofony\Reader\TemplateAnnotationReader;
use Keiwen\Cacofony\WidgetBundle\Controller\WidgetController;
use Keiwen\Cacofony\WidgetBundle\Exception\UnhandledWidgetRendererException;
use Keiwen\Cacofony\WidgetBundle\Exception\WidgetNotFoundException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;

class TwigWidget extends \Twig_Extension
{

    const DEFAULT_TEMPLATE_EXTENSION = '.html.twig';
    const WIDGET_FUNCTION = 'widget';
    const ASYNC_WIDGET_FUNCTION = 'widgetAsync';

    /** @var WidgetController */
    protected $controller;
    /** @var ContainerInterface */
    protected $container;
    protected $bundleClassName;

    protected static $controllerInstances = array();

    public function __construct(ContainerInterface $container, string $bundleClassName)
    {
        $this->container = $container;
        $this->bundleClassName = $bundleClassName;
    }

    /**
     * @return string
     */
	public function getName()
    {
		return 'caco_twig_widget_extension';
	}

    /**
     * @return array
     */
	public function getFunctions()
    {
		return array(
            static::WIDGET_FUNCTION => new \Twig_SimpleFunction(
                static::WIDGET_FUNCTION,
                array($this, 'callWidget'),
                array('is_safe' => array('html'))
            ),
            static::ASYNC_WIDGET_FUNCTION => new \Twig_SimpleFunction(
                static::ASYNC_WIDGET_FUNCTION,
                array($this, 'callAsyncWidget'),
                array('is_safe' => array('html'))
            ),
		);
	}


    /**
     * @param string $widget
     * @return string
     */
    public function callWidget(string $widget, array $parameters = array())
    {
        list($controllerName, $widgetName) = $this->readControllerWidgetName($widget);
        $this->controller = $this->loadController($controllerName);
        $method = $widgetName . 'Widget';
        if(!method_exists($this->controller, $method)) {
            $method = $widgetName;
            if(!method_exists($this->controller, $method)) {
                throw new WidgetNotFoundException(
                    sprintf('No method for rendering widget "%s" in controller %s', $widgetName, get_class($this->controller))
                );
            }
        }

        $this->controller->setWidgetParameters($parameters);
        $widgetReturn = $this->controller->$method();
        $this->controller->resetWidgetParameters();

        return $this->renderWidget($widgetReturn, $method);
    }


    /**
     * @param string $url or route name
     * @param string $method
     * @param array  $parameters
     * @param string $loaderVersion
     * @param string $loadErrorVersion
     * @return string
     */
    public function callAsyncWidget(string $url,
                                    array $parameters = array(),
                                    string $method = 'GET',
                                    string $loaderVersion = '',
                                    string $loadErrorVersion = '')
    {
        $this->controller = $this->loadController();
        if(strpos($url, '/') === false) {
            //consider that route name given
            $url = $this->controller->generateWidgetRouteUrl($url, $parameters);
        }
        $widgetReturn = $this->controller->asyncLoaderWidget($url, $parameters, $method, $loaderVersion, $loadErrorVersion);
        return $this->renderWidget($widgetReturn, 'asyncLoaderWidget');
    }


    /**
     * @param mixed  $widgetReturn
     * @param string $methodCalled
     * @return string
     * @throws UnhandledWidgetRendererException
     */
    protected function renderWidget($widgetReturn, string $methodCalled)
    {
        switch(true) {
            case is_array($widgetReturn):
                //widget send back template parameters only, should have template annotation
                $bundleName = explode('\\', $this->bundleClassName);
                $bundleName = array_pop($bundleName);
                $annotationReader = new TemplateAnnotationReader($this->controller, $bundleName);
                $annotationReader->setMethodSuffix('Widget');
                $template = $annotationReader->getTemplateFromAnnotation($methodCalled);
                return $this->controller->renderWidgetContent($template, $widgetReturn);
            case is_string($widgetReturn);
                //consider html content directly provided
                return $widgetReturn;
            case empty($widgetReturn):
                $error = 'Cannot handle void return';
                break;
            case $widgetReturn instanceof Response:
                return $widgetReturn->getContent();
            case is_object($widgetReturn):
                $error = sprintf('Cannot handle return object of class %s', get_class($widgetReturn));
                break;
            default:
                $error = 'Cannot handle non-object return';
                break;

        }
        $error .= ' for widget "%s" in controller %s';
        throw new UnhandledWidgetRendererException(sprintf($error, $methodCalled, get_class($this->controller)));
    }


    /**
     * @param string $name
     * @return WidgetController
     */
    protected function loadController(string $name = '')
    {
        $controller = ($this->bundleClassName)::getControllerClassName($name);
        if(empty(static::$controllerInstances[$name])) {
            static::$controllerInstances[$name] = new $controller($this->container);
        }
        return static::$controllerInstances[$name];
    }


    /**
     * @param string $name
     * @return array
     */
    protected function readControllerWidgetName(string $name)
    {
        $matches = array();
        $pattern = '#(.*):(.*)#';
        preg_match($pattern, $name, $matches);
        $controllerName = empty($matches[1]) ? '' : $matches[1];
        $widgetName = empty($matches[2]) ? $name : $matches[2];
        return array($controllerName, $widgetName);
    }

}
