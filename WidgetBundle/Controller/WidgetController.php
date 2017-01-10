<?php

namespace Keiwen\Cacofony\WidgetBundle\Controller;


use Keiwen\Cacofony\Controller\AppController;
use Keiwen\Cacofony\EventListener\AutoDumpListener;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
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
 *
 * Widget could be called with parameters. Use getWidgetParameter method
 */
class WidgetController extends AppController
{

    protected $widgetParameters;
    protected static $asyncWidgetsCalled = array();

    public function __construct(ContainerInterface $container)
    {
        parent::__construct();
        $this->setContainer($container);
        $this->resetWidgetParameters();
    }


    /**
     * @param string $view template name
     * @param array  $parameters
     * @return string
     */
    public function renderWidgetContent(string $view, array $parameters = array())
    {
        $response = $this->render($view, $parameters, $this->response);
        $autodump = $this->get('keiwen_cacofony.autodump');
        $autodump->addParameterToDump($view, $parameters, AutoDumpListener::SUBPART_WIDGET);
        return $response->getContent();
    }

    /**
     * @param array $widgetParameters
     */
    public function setWidgetParameters(array $widgetParameters)
    {
        $this->widgetParameters = $widgetParameters;
    }

    /**
     * @return mixed
     */
    public function getWidgetParameter(string $name, $default = '')
    {
        return isset($this->widgetParameters[$name]) ? $this->widgetParameters[$name] : $default;
    }

    /**
     *
     */
    public function resetWidgetParameters()
    {
        $this->widgetParameters = array();
    }


    /**
     * @param string $url
     * @param array  $parameters
     * @param string $method
     * @param string $loaderVersion
     * @param string $loadErrorVersion
     * @return array
     * @Template("KeiwenCacofonyWidgetBundle:AsyncWidget:asyncLoaderWidget.html.twig")
     */
    public function asyncLoaderWidget(string $url,
                                      array $parameters = array(),
                                      string $method = 'GET',
                                      string $loaderVersion = '',
                                      string $loadErrorVersion = '')
    {
        $parameters = json_encode($parameters);
        //generate unique id
        $id = md5($method.urlencode($url).$parameters.rand(0, 999999));
        self::$asyncWidgetsCalled[] = $id;
        return array(
            'id' => $id,
            'url' => $url,
            'method' => $method,
            'parameters' => $parameters,
            'loaderVersion' => $loaderVersion,
            'loadErrorVersion' => $loadErrorVersion,
        );
    }


    /**
     * @param string $route
     * @param array  $parameters
     * @return string
     */
    public function generateWidgetRouteUrl(string $routeName, $parameters = array())
    {
        return $this->generateUrl($routeName, $parameters);
    }

    /**
     * @return array
     */
    public static function retrieveAsyncWidgetsCalled()
    {
        return self::$asyncWidgetsCalled;
    }

}
