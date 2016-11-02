<?php


namespace Keiwen\Cacofony\EventListener;

use Keiwen\Cacofony\Http\Request;
use Keiwen\Cacofony\ParamFetcher\ParamFetcher;
use Doctrine\Common\Annotations\Reader;
use Keiwen\Cacofony\ParamFetcher\ParamReader;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ParamFetcherListener
{
    protected $container;
    protected $annotationReader;
    protected $validator;

    const PARAM_FETCHER_MAIN_CLASS = ParamFetcher::class;


    public function __construct(ContainerInterface $container, Reader $annotationReader, ValidatorInterface $validator)
    {
        $this->container = $container;
        $this->annotationReader = $annotationReader;
        $this->validator = $validator;
    }


    /**
     * Core controller handler.
     *
     * @param FilterControllerEvent $event
     *
     * @throws \InvalidArgumentException
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        /** @var Request $request */
        $request = $event->getRequest();
        if(!$request instanceof Request) return;

        $callable = $event->getController();
        list($controller, $methodName) = $callable;
        if(!$controller instanceof Controller) return;

        $attributeName = $this->getControllerAttributeName($controller, $methodName);
        $paramFetcher = $this->getParamFetcher($request, $controller, $methodName);
        $request->attributes->set($attributeName, $paramFetcher);
    }


    /**
     * @param Controller $controller
     * @return ParamReader
     */
    protected function getParamReader(Controller $controller)
    {
        $paramReaderClass = $this->container->getParameter('keiwen_cacofony.param_reader.class');
        try {
            $paramReader = new $paramReaderClass($this->annotationReader, $controller);
        } catch (\Exception $e) {
            $paramReader = new ParamReader($this->annotationReader, $controller);
        }
        return $paramReader;
    }


    /**
     * @param Request    $request
     * @param Controller $controller
     * @param string     $methodName
     * @return ParamFetcher
     */
    protected function getParamFetcher(Request $request, Controller $controller, string $methodName)
    {
        $paramReader = $this->getParamReader($controller);

        $paramFetcherClass = $this->container->getParameter('keiwen_cacofony.param_fetcher.class');
        try {
            $paramFetcher = new $paramFetcherClass($request, $paramReader, $methodName, $this->validator);
        } catch (\Exception $e) {
            $paramFetcher = new ParamFetcher($request, $paramReader, $methodName, $this->validator);
        }
        return $paramFetcher;
    }


    /**
     * Determines which attribute the paramFetcher should be injected as.
     * @param Controller $controller
     * @param string     $methodName
     * @return mixed|string
     */
    private function getControllerAttributeName(Controller $controller, string $methodName)
    {
        $method = new \ReflectionMethod($controller, $methodName);
        foreach($method->getParameters() as $param) {
            if ($this->isParamFetcherType($param)) {
                return $param->getName();
            }
        }
        // If there is no typehint, inject the paramFetcher using a default name.
        return $this->container->getParameter('keiwen_cacofony.param_fetcher.controller_parameter');
    }


    /**
     * Returns true if the given controller parameter is type-hinted as
     * an instance of Annotation.
     * @param \ReflectionParameter $controllerParam A parameter of the controller action.
     * @return bool
     */
    private function isParamFetcherType(\ReflectionParameter $controllerParam)
    {
        $type = $controllerParam->getClass();
        if (null === $type) {
            return false;
        }
        return $type->isSubclassOf(self::PARAM_FETCHER_MAIN_CLASS);
    }
}
