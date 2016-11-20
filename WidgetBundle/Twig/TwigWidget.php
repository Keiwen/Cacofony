<?php

namespace Keiwen\Cacofony\WidgetBundle\Twig;


use Keiwen\Cacofony\Reader\TemplateAnnotationReader;
use Keiwen\Cacofony\WidgetBundle\Controller\WidgetController;
use Keiwen\Cacofony\WidgetBundle\Exception\UnhandledWidgetRendererException;
use Keiwen\Cacofony\WidgetBundle\Exception\WidgetNotFoundException;
use Symfony\Component\HttpFoundation\Response;

class TwigWidget extends \Twig_Extension
{

    const DEFAULT_TEMPLATE_EXTENSION = '.html.twig';
    const WIDGET_FUNCTION = 'widget';

    /** @var WidgetController */
    protected $controller;
    /** @var TemplateAnnotationReader */
    protected $annotationReader;
    protected $bundleName;

    public function __construct(WidgetController $controller, string $bundleName)
    {
        $this->controller = $controller;
        $this->annotationReader = new TemplateAnnotationReader($controller, $bundleName);
        $this->annotationReader->setMethodSuffix('Widget');
        $this->bundleName = $bundleName;
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
		);
	}


    /**
     * @param string $widget
     * @return string
     */
    public function callWidget(string $widget)
    {
        $method = $widget . 'Widget';
        if(!method_exists($this->controller, $method)) {
            $method = $widget;
            if(!method_exists($this->controller, $method)) {
                throw new WidgetNotFoundException(
                    sprintf('No method for rendering widget "%s" in controller %s', $widget, get_class($this->controller))
                );
            }
        }
        $this->controller->setAutodumpParamWidgetSuffix($widget);
        $widgetReturn = $this->controller->$method();
        return $this->renderWidget($widgetReturn, $method);
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
                $template = $this->annotationReader->getTemplateFromAnnotation($methodCalled);
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


}