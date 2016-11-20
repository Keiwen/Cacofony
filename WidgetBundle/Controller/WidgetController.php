<?php

namespace Keiwen\Cacofony\WidgetBundle\Controller;


use Keiwen\Cacofony\Controller\AppController;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class WidgetController
 * To be declared as service
 * Idea is to have a new twig function that will call a widget method here
 *
 * Widget method should be called {widgetName}[Widget].
 * 'Widget' suffix is not mandatory although strongly recommended
 * Method should return:
 * - Response object (it will render the content)
 * - array of template parameters if you used Template annotation (it will get the template and render it)
 * - string as html content (will display it as this)
 */
class WidgetController extends AppController
{

    protected $autodumpParamWidgetSuffix = '';


    public function __construct(ContainerInterface $container)
    {
        parent::__construct();
        $this->setContainer($container);
    }


    /**
     * @param string $autodumpParamWidgetSuffix
     */
    public function setAutodumpParamWidgetSuffix(string $autodumpParamWidgetSuffix)
    {
        $this->autodumpParamWidgetSuffix = '_' . ltrim($autodumpParamWidgetSuffix, '_');
    }


    /**
     * @param string $suffix
     * @return string
     */
    protected function getAutodumpParameterName(string $suffix = '')
    {
        $name = parent::getAutodumpParameterName('_widget');
        $name .= $this->autodumpParamWidgetSuffix;
        return $name . $suffix;
    }


    /**
     * @param string $view template name
     * @param array  $parameters
     * @return string
     */
    public function renderWidgetContent(string $view, array $parameters = array())
    {
        $response = $this->render($view, $parameters, $this->response);
        return $response->getContent();
    }


}